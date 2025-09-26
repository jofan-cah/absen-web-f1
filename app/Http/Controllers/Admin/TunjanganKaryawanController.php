<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absen;
use App\Models\TunjanganKaryawan;
use App\Models\TunjanganType;
use App\Models\Karyawan;
use App\Models\Lembur;
use App\Models\Penalti;
use App\Models\TunjanganDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class TunjanganKaryawanController extends Controller
{
    public function index(Request $request)
    {
        $query = TunjanganKaryawan::with(['karyawan.user', 'tunjanganType', 'penalti', 'approvedBy']);

        // Filter by karyawan
        if ($request->filled('karyawan_id')) {
            $query->where('karyawan_id', $request->karyawan_id);
        }

        // Filter by tunjangan type
        if ($request->filled('tunjangan_type_id')) {
            $query->where('tunjangan_type_id', $request->tunjangan_type_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by periode
        if ($request->filled('periode_dari')) {
            $query->where('period_start', '>=', $request->periode_dari);
        }

        if ($request->filled('periode_sampai')) {
            $query->where('period_end', '<=', $request->periode_sampai);
        }

        // Filter by request via
        if ($request->filled('requested_via')) {
            $query->where('requested_via', $request->requested_via);
        }

        $tunjanganKaryawan = $query->orderBy('created_at', 'desc')
            ->paginate(15);

        $karyawans = Karyawan::with('user')
            ->where('employment_status', 'active')
            ->get();

        $tunjanganTypes = TunjanganType::active()->get();

        $statusOptions = ['pending', 'requested', 'approved', 'received'];

        return view('admin.tunjangan-karyawan.indexTunKar', compact(
            'tunjanganKaryawan',
            'karyawans',
            'tunjanganTypes',
            'statusOptions'
        ));
    }

    public function show(TunjanganKaryawan $tunjanganKaryawan)
    {
        $tunjanganKaryawan->load([
            'karyawan.user',
            'tunjanganType',
            'absen',
            'penalti',
            'approvedBy'
        ]);

        // Get formatted history
        $history = $tunjanganKaryawan->getFormattedHistory();

        return view('admin.tunjangan-karyawan.showTunKar', compact('tunjanganKaryawan', 'history'));
    }

    // Generate tunjangan manual
    public function generateForm()
    {
        $karyawans = Karyawan::with(['user', 'department'])
            ->where('employment_status', 'active')
            ->get();

        // Ambil tunjangan types yang aktif (exclude lembur untuk sekarang)
        $tunjanganTypes = TunjanganType::with(['tunjanganDetails' => function ($query) {
            $query->active()->orderBy('staff_status');
        }])
            ->active()
            ->whereIn('category', ['harian', 'mingguan', 'bulanan'])  // Skip mingguan (lembur) dulu
            ->get();
        // Ambil semua dulu, jangan filter

        return view('admin.tunjangan-karyawan.generateTunkar', compact('karyawans', 'tunjanganTypes'));
    }

    public function generateTunjangan(Request $request)
    {
        // Basic validation
        $request->validate([
            'tunjangan_type_id' => 'required|exists:tunjangan_types,tunjangan_type_id',
            'karyawan_ids' => 'required|array|min:1',
            'karyawan_ids.*' => 'exists:karyawans,karyawan_id',
        ]);

        // Get tunjangan type
        $tunjanganType = TunjanganType::find($request->tunjangan_type_id);

        // Conditional validation berdasarkan category
        $additionalRules = [];

        if ($tunjanganType->category === 'harian' || $tunjanganType->category === 'mingguan') {
            $additionalRules = [
                'period_start' => 'required|date',
                'period_end' => 'required|date|after_or_equal:period_start',
            ];
        } elseif ($tunjanganType->category === 'bulanan') {
            // Untuk uang kuota - butuh bulan tahun
            $additionalRules = [
                'month' => 'required|integer|between:1,12',
                'year' => 'required|integer|min:2020|max:' . (date('Y') + 1),
            ];
        }

        if (!empty($additionalRules)) {
            $request->validate($additionalRules);
        }

        try {
            DB::beginTransaction();

            $generated = 0;
            $errors = [];

            foreach ($request->karyawan_ids as $karyawanId) {
                try {
                    $result = null;

                    if ($tunjanganType->category === 'harian' || $tunjanganType->category === 'mingguan') {
                        $result = $this->generateTunjanganHarian(
                            $karyawanId,
                            $tunjanganType->tunjangan_type_id,
                            $request->period_start,
                            $request->period_end
                        );
                    } elseif ($tunjanganType->category === 'bulanan') {
                        // Generate tetap bulanan
                        $result = $this->generateTunjanganBulanan(
                            $karyawanId,
                            $tunjanganType->tunjangan_type_id,
                            $request->month,
                            $request->year
                        );
                    }

                    if ($result) {
                        $generated++;
                    } else {
                        $karyawan = Karyawan::find($karyawanId);
                        $errors[] = "Gagal generate untuk {$karyawan->full_name}";
                    }
                } catch (\Exception $e) {
                    $karyawan = Karyawan::find($karyawanId);
                    $errors[] = "{$karyawan->full_name}: {$e->getMessage()}";
                }
            }

            DB::commit();

            $message = "{$generated} tunjangan {$tunjanganType->name} berhasil dibuat";
            if (!empty($errors)) {
                $message .= ". Error: " . implode(', ', array_slice($errors, 0, 2));
            }

            return redirect()
                ->route('admin.tunjangan-karyawan.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Gagal generate: ' . $e->getMessage());
        }
    }

    // Method terpisah untuk generate harian (berdasarkan absensi)
    private function generateTunjanganHarian($karyawanId, $tunjanganTypeId, $startDate, $endDate)
    {
        $karyawan = Karyawan::find($karyawanId);

        // Get nominal berdasarkan staff status
        $amount = TunjanganDetail::getAmountByStaffStatus($tunjanganTypeId, $karyawan->staff_status);

        if ($amount <= 0) {
            throw new \Exception("Nominal tunjangan tidak ditemukan untuk status {$karyawan->staff_status}");
        }

        // Hitung hari kerja dari absensi
        $hariKerjaAsli = Absen::where('karyawan_id', $karyawanId)
            ->whereBetween('date', [$startDate, $endDate])
            ->whereNotNull('clock_in')
            ->count();

        // Hitung penalti jika ada
        $hariPotongPenalti = Penalti::getTotalHariPotongan($karyawanId, $startDate, $endDate);

        return TunjanganKaryawan::create([
            'tunjangan_karyawan_id' => TunjanganKaryawan::generateTunjanganKaryawanId(),
            'karyawan_id' => $karyawanId,
            'tunjangan_type_id' => $tunjanganTypeId,
            'period_start' => $startDate,
            'period_end' => $endDate,
            'amount' => $amount,
            'quantity' => $hariKerjaAsli,
            'hari_kerja_asli' => $hariKerjaAsli,
            'hari_potong_penalti' => $hariPotongPenalti,
            'status' => 'pending',
        ]);
    }

    // Method terpisah untuk generate bulanan
    private function generateTunjanganBulanan($karyawanId, $tunjanganTypeId, $month, $year)
    {
        $karyawan = Karyawan::find($karyawanId);

        // Get nominal berdasarkan staff status
        $amount = TunjanganDetail::getAmountByStaffStatus($tunjanganTypeId, $karyawan->staff_status);

        if ($amount <= 0) {
            throw new \Exception("Nominal tunjangan tidak ditemukan untuk status {$karyawan->staff_status}");
        }

        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        return TunjanganKaryawan::create([
            'tunjangan_karyawan_id' => TunjanganKaryawan::generateTunjanganKaryawanId(),
            'karyawan_id' => $karyawanId,
            'tunjangan_type_id' => $tunjanganTypeId,
            'period_start' => $startDate,
            'period_end' => $endDate,
            'amount' => $amount,
            'quantity' => 1, // Bulanan selalu 1
            'status' => 'pending',
        ]);
    }

    // Request tunjangan (bisa dari mobile atau web)
    public function requestTunjangan(TunjanganKaryawan $tunjanganKaryawan, Request $request)
    {
        $request->validate([
            'via' => 'required|in:mobile,web'
        ]);

        try {
            DB::beginTransaction();

            $result = $tunjanganKaryawan->requestTunjangan($request->via, Auth::id());

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tunjangan tidak dapat direquest! Status saat ini: ' . $tunjanganKaryawan->status
                ], 422);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Request tunjangan berhasil dikirim!'
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Gagal request tunjangan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Approve tunjangan (admin only)
    public function approveTunjangan(TunjanganKaryawan $tunjanganKaryawan, Request $request)
    {
        $request->validate([
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $result = $tunjanganKaryawan->approveTunjangan(Auth::id(), $request->notes);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tunjangan tidak dapat disetujui! Status saat ini: ' . $tunjanganKaryawan->status
                ], 422);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tunjangan berhasil disetujui!'
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Gagal approve tunjangan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Konfirmasi terima tunjangan
    public function confirmReceived(TunjanganKaryawan $tunjanganKaryawan, Request $request)
    {
        $request->validate([
            'confirmation_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            DB::beginTransaction();

            $photoPath = null;
            if ($request->hasFile('confirmation_photo')) {
                $photoPath = $request->file('confirmation_photo')->store('tunjangan/confirmations', 'public');
            }

            $result = $tunjanganKaryawan->confirmReceived($photoPath, Auth::id());

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tunjangan tidak dapat dikonfirmasi! Status saat ini: ' . $tunjanganKaryawan->status
                ], 422);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Konfirmasi penerimaan tunjangan berhasil!'
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            // Delete uploaded photo if error
            if (isset($photoPath) && $photoPath) {
                Storage::disk('public')->delete($photoPath);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal konfirmasi penerimaan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Apply penalti ke tunjangan yang sudah ada
    public function applyPenalti(TunjanganKaryawan $tunjanganKaryawan, Request $request)
    {
        $request->validate([
            'penalti_id' => 'required|exists:penaltis,penalti_id',
            'hari_potong' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            $tunjanganKaryawan->applyPenalti($request->penalti_id, $request->hari_potong);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Penalti berhasil diterapkan!'
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menerapkan penalti: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(TunjanganKaryawan $tunjanganKaryawan)
    {
        try {
            // Hanya bisa hapus yang masih pending
            if ($tunjanganKaryawan->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya tunjangan dengan status pending yang dapat dihapus!'
                ], 422);
            }

            DB::beginTransaction();

            // Delete confirmation photo if exists
            if ($tunjanganKaryawan->received_confirmation_photo) {
                Storage::disk('public')->delete($tunjanganKaryawan->received_confirmation_photo);
            }

            $tunjanganKaryawan->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tunjangan berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus tunjangan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:tunjangan_karyawan,tunjangan_karyawan_id'
        ]);

        try {
            DB::beginTransaction();

            $tunjanganKaryawan = TunjanganKaryawan::whereIn('tunjangan_karyawan_id', $request->ids)->get();

            foreach ($tunjanganKaryawan as $tunjangan) {
                if ($tunjangan->status !== 'pending') {
                    throw new \Exception("Tunjangan untuk '{$tunjangan->karyawan->full_name}' tidak dapat dihapus karena sudah diproses!");
                }
            }

            // Delete photos
            foreach ($tunjanganKaryawan as $tunjangan) {
                if ($tunjangan->received_confirmation_photo) {
                    Storage::disk('public')->delete($tunjangan->received_confirmation_photo);
                }
            }

            TunjanganKaryawan::whereIn('tunjangan_karyawan_id', $request->ids)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' tunjangan berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkApprove(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:tunjangan_karyawan,tunjangan_karyawan_id',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $approved = 0;
            $errors = [];

            foreach ($request->ids as $id) {
                try {
                    $tunjangan = TunjanganKaryawan::find($id);
                    $result = $tunjangan->approveTunjangan(Auth::id(), $request->notes);

                    if ($result) {
                        $approved++;
                    } else {
                        $errors[] = "Gagal approve tunjangan {$tunjangan->karyawan->full_name}";
                    }
                } catch (\Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }

            DB::commit();

            $message = "{$approved} tunjangan berhasil disetujui";
            if (!empty($errors)) {
                $message .= ". Error: " . implode(', ', $errors);
            }

            return response()->json([
                'success' => $approved > 0,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Gagal approve: ' . $e->getMessage()
            ], 500);
        }
    }

    // Report & Analytics
    public function report(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $query = TunjanganKaryawan::with(['karyawan', 'tunjanganType'])
            ->whereBetween('period_start', [$startDate, $endDate]);

        // Summary data
        $summary = [
            'total_tunjangan' => $query->count(),
            'total_nominal' => $query->sum('total_amount'),
            'pending' => $query->clone()->where('status', 'pending')->count(),
            'requested' => $query->clone()->where('status', 'requested')->count(),
            'approved' => $query->clone()->where('status', 'approved')->count(),
            'received' => $query->clone()->where('status', 'received')->count(),
        ];

        // Group by tunjangan type
        $byType = $query->clone()
            ->select('tunjangan_type_id', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('tunjangan_type_id')
            ->with('tunjanganType')
            ->get();

        // Group by karyawan
        $byKaryawan = $query->clone()
            ->select('karyawan_id', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('karyawan_id')
            ->with('karyawan')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return view('admin.tunjangan-karyawan.reportTunKar', compact(
            'summary',
            'byType',
            'byKaryawan',
            'startDate',
            'endDate'
        ));
    }

    // Export functionality
    public function export(Request $request)
    {
        try {
            $query = TunjanganKaryawan::with(['karyawan', 'tunjanganType', 'penalti']);

            // Apply filters similar to index
            if ($request->filled('karyawan_id')) {
                $query->where('karyawan_id', $request->karyawan_id);
            }
            if ($request->filled('tunjangan_type_id')) {
                $query->where('tunjangan_type_id', $request->tunjangan_type_id);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $tunjanganKaryawan = $query->get();

            $filename = 'tunjangan_karyawan_' . date('Y-m-d_H-i-s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($tunjanganKaryawan) {
                $file = fopen('php://output', 'w');

                // Header CSV
                fputcsv($file, [
                    'ID',
                    'Karyawan',
                    'Jenis Tunjangan',
                    'Periode Mulai',
                    'Periode Akhir',
                    'Nominal per Unit',
                    'Hari Kerja Asli',
                    'Hari Potong Penalti',
                    'Hari Kerja Final',
                    'Total Nominal',
                    'Status',
                    'Request Via',
                    'Tanggal Request',
                    'Tanggal Approve',
                    'Tanggal Terima',
                    'Tanggal Dibuat'
                ]);

                // Data rows
                foreach ($tunjanganKaryawan as $tunjangan) {
                    fputcsv($file, [
                        $tunjangan->tunjangan_karyawan_id,
                        $tunjangan->karyawan->full_name,
                        $tunjangan->tunjanganType->name,
                        $tunjangan->period_start->format('d-m-Y'),
                        $tunjangan->period_end->format('d-m-Y'),
                        number_format($tunjangan->amount, 0, ',', '.'),
                        $tunjangan->hari_kerja_asli ?? $tunjangan->quantity,
                        $tunjangan->hari_potong_penalti ?? 0,
                        $tunjangan->hari_kerja_final ?? ($tunjangan->hari_kerja_asli ?? $tunjangan->quantity),
                        number_format($tunjangan->total_amount, 0, ',', '.'),
                        ucfirst($tunjangan->status),
                        $tunjangan->requested_via ? ucfirst($tunjangan->requested_via) : '-',
                        $tunjangan->requested_at ? $tunjangan->requested_at->format('d-m-Y H:i') : '-',
                        $tunjangan->approved_at ? $tunjangan->approved_at->format('d-m-Y H:i') : '-',
                        $tunjangan->received_at ? $tunjangan->received_at->format('d-m-Y H:i') : '-',
                        $tunjangan->created_at->format('d-m-Y H:i:s')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
    }

    // Method untuk laporan semua karyawan
    public function allEmployeeReport(Request $request)
    {
        $request->validate([
            'tunjangan_type_id' => 'required|exists:tunjangan_types,tunjangan_type_id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020',
        ]);

        $tunjanganType = TunjanganType::find($request->tunjangan_type_id);
        $month = $request->month;
        $year = $request->year;

        // Generate laporan berdasarkan kategori tunjangan
        if ($tunjanganType->category === 'mingguan') {
            return $this->generateAllEmployeeWeeklyReport($tunjanganType, $month, $year);
        } elseif ($tunjanganType->category === 'bulanan') {
            return $this->generateAllEmployeeMonthlyReport($tunjanganType, $month, $year);
        } elseif ($tunjanganType->category === 'harian') {
            return $this->generateAllEmployeeDailyReport($tunjanganType, $month, $year);
        }

        return back()->with('error', 'Kategori tunjangan tidak didukung');
    }

    // Update method generateAllEmployeeWeeklyReport di controller

    // private function generateAllEmployeeWeeklyReport($tunjanganType, $month, $year)
    // {
    //     $startDate = Carbon::create($year, $month, 1);
    //     $endDate = $startDate->copy()->endOfMonth();

    //     // Get semua karyawan aktif
    //     $karyawans = Karyawan::with(['department'])
    //         ->where('employment_status', 'active')
    //         ->orderBy('full_name')
    //         ->get();

    //     // Bagi bulan jadi minggu-minggu PROPER (Senin-Minggu)
    //     $weeks = [];
    //     $currentDate = $startDate->copy();
    //     $weekNumber = 1;

    //     // Mulai dari Senin pertama dalam bulan (atau awal bulan kalau bukan Senin)
    //     while ($currentDate <= $endDate) {
    //         // Cari awal minggu (Senin)
    //         $weekStart = $currentDate->copy();
    //         if ($weekStart->dayOfWeek != Carbon::MONDAY) {
    //             // Kalau bukan Senin, mundur ke Senin sebelumnya (tapi tetap dalam bulan)
    //             if ($weekStart->day <= 7) {
    //                 // Kalau masih minggu pertama, tetep pake tanggal 1
    //                 $weekStart = $startDate->copy();
    //             } else {
    //                 // Mundur ke Senin
    //                 $weekStart = $weekStart->startOfWeek(Carbon::MONDAY);
    //             }
    //         }

    //         // Akhir minggu (Minggu)
    //         $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

    //         // Pastikan tidak melebihi akhir bulan
    //         if ($weekEnd > $endDate) {
    //             $weekEnd = $endDate->copy();
    //         }

    //         // Pastikan tidak sebelum awal bulan
    //         if ($weekStart < $startDate) {
    //             $weekStart = $startDate->copy();
    //         }

    //         $weeks[] = [
    //             'number' => $weekNumber,
    //             'start' => $weekStart,
    //             'end' => $weekEnd,
    //             'label' => $weekStart->format('d') . '-' . $weekEnd->format('d'),
    //             'full_label' => $weekStart->format('d/m') . ' - ' . $weekEnd->format('d/m'),
    //             'day_names' => $this->getWeekDayNames($weekStart, $weekEnd),
    //             'is_full_week' => $weekStart->diffInDays($weekEnd) >= 6 // 7 hari = 6 diff
    //         ];

    //         // Lompat ke minggu berikutnya
    //         $currentDate = $weekEnd->copy()->addDay();
    //         $weekNumber++;
    //     }

    //     // Get data tunjangan untuk semua karyawan
    //     $employeeData = [];
    //     foreach ($karyawans as $karyawan) {
    //         $weeklyData = [];

    //         foreach ($weeks as $week) {
    //             // Cari tunjangan untuk minggu ini berdasarkan periode yang overlap
    //             $tunjangan = TunjanganKaryawan::where('karyawan_id', $karyawan->karyawan_id)
    //                 ->where('tunjangan_type_id', $tunjanganType->tunjangan_type_id)
    //                 ->where(function ($query) use ($week) {
    //                     $query->whereBetween('period_start', [$week['start'], $week['end']])
    //                         ->orWhereBetween('period_end', [$week['start'], $week['end']])
    //                         ->orWhere(function ($q) use ($week) {
    //                             $q->where('period_start', '<=', $week['start'])
    //                                 ->where('period_end', '>=', $week['end']);
    //                         });
    //                 })
    //                 ->first();

    //             $weeklyData[] = [
    //                 'week' => $week,
    //                 'tunjangan' => $tunjangan,
    //                 'is_taken' => $tunjangan ? in_array($tunjangan->status, ['approved', 'received']) : false,
    //                 'status' => $tunjangan ? $tunjangan->status : 'no_data',
    //                 'amount' => $tunjangan ? $tunjangan->total_amount : 0,
    //             ];
    //         }

    //         $employeeData[] = [
    //             'karyawan' => $karyawan,
    //             'weeks' => $weeklyData,
    //             'total_taken' => collect($weeklyData)->where('is_taken', true)->count(),
    //             'total_amount' => collect($weeklyData)->sum('amount'),
    //         ];
    //     }

    //     $data = [
    //         'tunjanganType' => $tunjanganType,
    //         'month' => $month,
    //         'year' => $year,
    //         'month_name' => $startDate->format('F Y'),
    //         'weeks' => $weeks,
    //         'employees' => $employeeData,
    //         'report_type' => 'mingguan',
    //         'generated_at' => now()->format('d/m/Y H:i:s'),
    //         'summary' => [
    //             'total_employees' => count($employeeData),
    //             'total_weeks' => count($weeks),
    //         ],
    //     ];

    //     $pdf = Pdf::loadView('admin.reports.all-employee-universal', $data);
    //     $pdf->setPaper('A4', 'landscape');

    //     $filename = "Laporan_Tunjangan_Mingguan_Semua_Karyawan_{$month}_{$year}.pdf";
    //     return $pdf->download($filename);
    // }

    // Helper method untuk nama hari
    private function getWeekDayNames($startDate, $endDate)
    {
        $days = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $days[] = [
                'date' => $current->format('d'),
                'day' => $current->format('D'), // Mon, Tue, Wed, etc.
                'full_day' => $current->format('l') // Monday, Tuesday, etc.
            ];
            $current->addDay();
        }

        return $days;
    }

    // private function generateAllEmployeeMonthlyReport($tunjanganType, $month, $year)
    // {
    //     $startDate = Carbon::create($year, $month, 1);
    //     $endDate = $startDate->copy()->endOfMonth();

    //     // Get semua karyawan aktif
    //     $karyawans = Karyawan::with(['department'])
    //         ->where('employment_status', 'active')
    //         ->orderBy('full_name')
    //         ->get();

    //     // Get data tunjangan untuk semua karyawan
    //     $employeeData = [];
    //     foreach ($karyawans as $karyawan) {
    //         $tunjangan = TunjanganKaryawan::where('karyawan_id', $karyawan->karyawan_id)
    //             ->where('tunjangan_type_id', $tunjanganType->tunjangan_type_id)
    //             ->whereYear('period_start', $year)
    //             ->whereMonth('period_start', $month)
    //             ->first();

    //         $employeeData[] = [
    //             'karyawan' => $karyawan,
    //             'tunjangan' => $tunjangan,
    //             'is_taken' => $tunjangan ? in_array($tunjangan->status, ['approved', 'received']) : false,
    //             'status' => $tunjangan ? $tunjangan->status : 'no_data',
    //             'amount' => $tunjangan ? $tunjangan->total_amount : 0,
    //             'approved_date' => $tunjangan && $tunjangan->approved_at ? $tunjangan->approved_at->format('d/m/Y') : null,
    //             'received_date' => $tunjangan && $tunjangan->received_at ? $tunjangan->received_at->format('d/m/Y') : null,
    //         ];
    //     }

    //     // Summary
    //     $summary = [
    //         'total_employees' => count($employeeData),
    //         'taken_count' => collect($employeeData)->where('is_taken', true)->count(),
    //         'pending_count' => collect($employeeData)->where('status', 'pending')->count(),
    //         'total_amount' => collect($employeeData)->sum('amount'),
    //     ];

    //     $data = [
    //         'tunjanganType' => $tunjanganType,
    //         'month' => $month,
    //         'year' => $year,
    //         'month_name' => $startDate->format('F Y'),
    //         'employees' => $employeeData,
    //         'summary' => $summary,
    //         'report_type' => 'bulanan',
    //         'generated_at' => now()->format('d/m/Y H:i:s'),
    //         'weeks' => [],
    //     ];

    //     $pdf = Pdf::loadView('admin.reports.all-employee-universal', $data);
    //     $filename = "Laporan_Tunjangan_Bulanan_Semua_Karyawan_{$month}_{$year}.pdf";

    //     return $pdf->download($filename);
    // }

    private function generateAllEmployeeDailyReport($tunjanganType, $month, $year)
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        // Get semua karyawan aktif
        $karyawans = Karyawan::with(['department'])
            ->where('employment_status', 'active')
            ->orderBy('full_name')
            ->get();

        // Get data tunjangan untuk semua karyawan dalam bulan ini
        $employeeData = [];
        foreach ($karyawans as $karyawan) {
            $tunjanganList = TunjanganKaryawan::where('karyawan_id', $karyawan->karyawan_id)
                ->where('tunjangan_type_id', $tunjanganType->tunjangan_type_id)
                ->whereBetween('period_start', [$startDate, $endDate])
                ->orderBy('period_start')
                ->get();

            $takenCount = $tunjanganList->whereIn('status', ['approved', 'received'])->count();
            $totalAmount = $tunjanganList->sum('total_amount');

            $employeeData[] = [
                'karyawan' => $karyawan,
                'tunjangan_list' => $tunjanganList,
                'total_days' => $tunjanganList->count(),
                'taken_days' => $takenCount,
                'pending_days' => $tunjanganList->count() - $takenCount,
                'total_amount' => $totalAmount,
            ];
        }

        // Summary
        $summary = [
            'total_employees' => count($employeeData),
            'total_days' => collect($employeeData)->sum('total_days'),
            'taken_days' => collect($employeeData)->sum('taken_days'),
            'pending_days' => collect($employeeData)->sum('pending_days'),
            'total_amount' => collect($employeeData)->sum('total_amount'),
        ];

        $data = [
            'tunjanganType' => $tunjanganType,
            'month' => $month,
            'year' => $year,
            'month_name' => $startDate->format('F Y'),
            'employees' => $employeeData,
            'summary' => $summary,
            'report_type' => 'harian',
            'generated_at' => now()->format('d/m/Y H:i:s'),
            'weeks' => [],
        ];

        $pdf = Pdf::loadView('admin.reports.all-employee-universal', $data);
        $filename = "Laporan_Tunjangan_Harian_Semua_Karyawan_{$month}_{$year}.pdf";

        return $pdf->download($filename);
    }

    private function generateAllEmployeeMonthlyReport($tunjanganType, $month, $year)
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        // Get semua karyawan aktif
        $karyawans = Karyawan::with(['department'])
            ->where('employment_status', 'active')
            ->orderBy('full_name')
            ->get();

        // CEK APAKAH INI UANG LEMBUR
        $isLembur = $tunjanganType->code === 'UANG_LEMBUR';

        // Get data tunjangan untuk semua karyawan
        $employeeData = [];
        foreach ($karyawans as $karyawan) {
            if ($isLembur) {
                // ✅ UNTUK LEMBUR: Ambil HANYA dari tabel lemburs
                $lemburList = Lembur::where('karyawan_id', $karyawan->karyawan_id)
                    ->whereYear('tanggal_lembur', $year)
                    ->whereMonth('tanggal_lembur', $month)
                    ->whereIn('status', ['approved', 'processed']) // yang sudah diapprove
                    ->with('tunjanganKaryawan')
                    ->get();

                $totalJam = $lemburList->sum('total_jam');
                $totalLembur = $lemburList->count();

                // Hitung total amount dari tunjangan yang sudah dibuat
                $totalAmount = 0;
                $approvedDate = null;
                $receivedDate = null;
                $allReceived = true;

                foreach ($lemburList as $lembur) {
                    if ($lembur->tunjanganKaryawan) {
                        $totalAmount += $lembur->tunjanganKaryawan->total_amount;

                        if (!$approvedDate && $lembur->tunjanganKaryawan->approved_at) {
                            $approvedDate = $lembur->tunjanganKaryawan->approved_at->format('d/m/Y');
                        }

                        if (!$receivedDate && $lembur->tunjanganKaryawan->received_at) {
                            $receivedDate = $lembur->tunjanganKaryawan->received_at->format('d/m/Y');
                        }

                        if ($lembur->tunjanganKaryawan->status !== 'received') {
                            $allReceived = false;
                        }
                    }
                }

                $employeeData[] = [
                    'karyawan' => $karyawan,
                    'tunjangan' => $lemburList->first()->tunjanganKaryawan ?? null,
                    'is_taken' => $allReceived && $totalLembur > 0,
                    'status' => $allReceived && $totalLembur > 0 ? 'received' : ($totalLembur > 0 ? 'approved' : 'no_data'),
                    'amount' => $totalAmount,
                    'approved_date' => $approvedDate,
                    'received_date' => $receivedDate,
                    'total_lembur' => $totalLembur,
                    'total_jam' => $totalJam,
                    'source' => 'lembur', // PENANDA: data dari lembur
                ];
            } else {
                // ✅ UNTUK UANG MAKAN / KUOTA: Dari TunjanganKaryawan (berbasis absen)
                $tunjangan = TunjanganKaryawan::where('karyawan_id', $karyawan->karyawan_id)
                    ->where('tunjangan_type_id', $tunjanganType->tunjangan_type_id)
                    ->whereYear('period_start', $year)
                    ->whereMonth('period_start', $month)
                    ->whereNull('absen_id') // EXCLUDE yang dari lembur
                    ->first();

                $employeeData[] = [
                    'karyawan' => $karyawan,
                    'tunjangan' => $tunjangan,
                    'is_taken' => $tunjangan ? in_array($tunjangan->status, ['approved', 'received']) : false,
                    'status' => $tunjangan ? $tunjangan->status : 'no_data',
                    'amount' => $tunjangan ? $tunjangan->total_amount : 0,
                    'approved_date' => $tunjangan && $tunjangan->approved_at ? $tunjangan->approved_at->format('d/m/Y') : null,
                    'received_date' => $tunjangan && $tunjangan->received_at ? $tunjangan->received_at->format('d/m/Y') : null,
                    'source' => 'absen', // PENANDA: data dari absen
                ];
            }
        }

        // Summary
        $summary = [
            'total_employees' => count($employeeData),
            'taken_count' => collect($employeeData)->where('is_taken', true)->count(),
            'pending_count' => collect($employeeData)->where('status', 'pending')->count(),
            'total_amount' => collect($employeeData)->sum('amount'),
        ];

        $data = [
            'tunjanganType' => $tunjanganType,
            'month' => $month,
            'year' => $year,
            'month_name' => $startDate->format('F Y'),
            'employees' => $employeeData,
            'summary' => $summary,
            'report_type' => 'bulanan',
            'generated_at' => now()->format('d/m/Y H:i:s'),
            'weeks' => [],
            'is_lembur' => $isLembur,
        ];

        $pdf = Pdf::loadView('admin.reports.all-employee-universal', $data);
        $filename = "Laporan_Tunjangan_Bulanan_Semua_Karyawan_{$month}_{$year}.pdf";

        return $pdf->download($filename);
    }

    private function generateAllEmployeeWeeklyReport($tunjanganType, $month, $year)
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $karyawans = Karyawan::with(['department'])
            ->where('employment_status', 'active')
            ->orderBy('full_name')
            ->get();

        // Bagi minggu
        $weeks = [];
        $currentDate = $startDate->copy();
        $weekNumber = 1;

        while ($currentDate <= $endDate) {
            $weekStart = $currentDate->copy();
            if ($weekStart->dayOfWeek != Carbon::MONDAY) {
                if ($weekStart->day <= 7) {
                    $weekStart = $startDate->copy();
                } else {
                    $weekStart = $weekStart->startOfWeek(Carbon::MONDAY);
                }
            }

            $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);
            if ($weekEnd > $endDate) {
                $weekEnd = $endDate->copy();
            }
            if ($weekStart < $startDate) {
                $weekStart = $startDate->copy();
            }

            $weeks[] = [
                'number' => $weekNumber,
                'start' => $weekStart,
                'end' => $weekEnd,
                'label' => $weekStart->format('d') . '-' . $weekEnd->format('d'),
                'full_label' => $weekStart->format('d/m') . ' - ' . $weekEnd->format('d/m'),
                'day_names' => $this->getWeekDayNames($weekStart, $weekEnd),
                'is_full_week' => $weekStart->diffInDays($weekEnd) >= 6
            ];

            $currentDate = $weekEnd->copy()->addDay();
            $weekNumber++;
        }

        // CEK APAKAH INI UANG LEMBUR
        $isLembur = $tunjanganType->code === 'UANG_LEMBUR';

        $employeeData = [];
        foreach ($karyawans as $karyawan) {
            $weeklyData = [];

            foreach ($weeks as $week) {
                if ($isLembur) {
                    // ✅ UNTUK LEMBUR: Ambil dari tabel lemburs
                    $lemburList = Lembur::where('karyawan_id', $karyawan->karyawan_id)
                        ->whereDate('tanggal_lembur', '>=', $week['start'])
                        ->whereDate('tanggal_lembur', '<=', $week['end'])
                        ->whereIn('status', ['approved', 'processed'])
                        ->with('tunjanganKaryawan')
                        ->get();

                    $totalAmount = 0;
                    $totalJam = $lemburList->sum('total_jam');
                    $anyTaken = false;

                    foreach ($lemburList as $lembur) {
                        if ($lembur->tunjanganKaryawan) {
                            $totalAmount += $lembur->tunjanganKaryawan->total_amount;
                            if (in_array($lembur->tunjanganKaryawan->status, ['approved', 'received'])) {
                                $anyTaken = true;
                            }
                        }
                    }

                    $weeklyData[] = [
                        'week' => $week,
                        'tunjangan' => $lemburList->first()->tunjanganKaryawan ?? null,
                        'is_taken' => $anyTaken,
                        'status' => $anyTaken ? 'approved' : ($lemburList->count() > 0 ? 'pending' : 'no_data'),
                        'amount' => $totalAmount,
                        'total_jam' => $totalJam, // TAMBAHAN untuk lembur
                        'source' => 'lembur',
                    ];
                } else {
                    // ✅ UNTUK UANG MAKAN: Query dari TunjanganKaryawan (berbasis absen)
                    $tunjangan = TunjanganKaryawan::where('karyawan_id', $karyawan->karyawan_id)
                        ->where('tunjangan_type_id', $tunjanganType->tunjangan_type_id)
                        ->whereNull('absen_id') // EXCLUDE lembur
                        ->where(function ($query) use ($week) {
                            $query->whereBetween('period_start', [$week['start'], $week['end']])
                                ->orWhereBetween('period_end', [$week['start'], $week['end']])
                                ->orWhere(function ($q) use ($week) {
                                    $q->where('period_start', '<=', $week['start'])
                                        ->where('period_end', '>=', $week['end']);
                                });
                        })
                        ->first();

                    $weeklyData[] = [
                        'week' => $week,
                        'tunjangan' => $tunjangan,
                        'is_taken' => $tunjangan ? in_array($tunjangan->status, ['approved', 'received']) : false,
                        'status' => $tunjangan ? $tunjangan->status : 'no_data',
                        'amount' => $tunjangan ? $tunjangan->total_amount : 0,
                        'source' => 'absen',
                    ];
                }
            }

            $employeeData[] = [
                'karyawan' => $karyawan,
                'weeks' => $weeklyData,
                'total_taken' => collect($weeklyData)->where('is_taken', true)->count(),
                'total_amount' => collect($weeklyData)->sum('amount'),
            ];
        }

        $data = [
            'tunjanganType' => $tunjanganType,
            'month' => $month,
            'year' => $year,
            'month_name' => $startDate->format('F Y'),
            'weeks' => $weeks,
            'employees' => $employeeData,
            'report_type' => 'mingguan',
            'generated_at' => now()->format('d/m/Y H:i:s'),
            'summary' => [
                'total_employees' => count($employeeData),
                'total_weeks' => count($weeks),
            ],
            'is_lembur' => $isLembur,
        ];

        $pdf = Pdf::loadView('admin.reports.all-employee-universal', $data);
        $pdf->setPaper('A4', 'landscape');

        $filename = "Laporan_Tunjangan_Mingguan_Semua_Karyawan_{$month}_{$year}.pdf";
        return $pdf->download($filename);
    }
    // Form untuk pilih jenis laporan
    public function reportForm()
    {
        $tunjanganTypes = TunjanganType::active()
            ->whereIn('category', ['harian', 'mingguan', 'bulanan'])
            ->get();

        return view('admin.reports.tunjangan-form', compact('tunjanganTypes'));
    }


    public function singleWeekReportForm()
    {
        $tunjanganTypes = TunjanganType::active()
            ->where('category', 'mingguan') // Cuma yang mingguan
            ->get();

        return view('admin.reports.single-week-form', compact('tunjanganTypes'));
    }

    public function generateSingleWeekReport(Request $request)
    {
        $request->validate([
            'tunjangan_type_id' => 'required|exists:tunjangan_types,tunjangan_type_id',
            'week_start' => 'required|date',
        ]);

        $tunjanganType = TunjanganType::find($request->tunjangan_type_id);
        $weekStart = Carbon::parse($request->week_start);

        // Pastikan mulai dari Senin
        if ($weekStart->dayOfWeek !== Carbon::MONDAY) {
            $weekStart = $weekStart->startOfWeek(Carbon::MONDAY);
        }

        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        return $this->generateSingleWeekData($tunjanganType, $weekStart, $weekEnd);
    }

    private function generateSingleWeekData($tunjanganType, $weekStart, $weekEnd)
    {
        $karyawans = Karyawan::with(['department'])
            ->where('employment_status', 'active')
            ->orderBy('full_name')
            ->get();

        $isLembur = $tunjanganType->code === 'UANG_LEMBUR';

        $weekInfo = [
            'start' => $weekStart,
            'end' => $weekEnd,
            'week_label' => $weekStart->format('d/m') . ' - ' . $weekEnd->format('d/m/Y'),
            'full_label' => $weekStart->format('d F Y') . ' - ' . $weekEnd->format('d F Y'),
            'days' => $this->getWeekDays($weekStart, $weekEnd),
            'month_name' => $weekStart->format('F Y'),
        ];

        $employeeData = [];
        foreach ($karyawans as $karyawan) {
            if ($isLembur) {
                // ✅ UNTUK LEMBUR: Ambil dari tabel lemburs
                $lemburList = Lembur::where('karyawan_id', $karyawan->karyawan_id)
                    ->whereDate('tanggal_lembur', '>=', $weekStart)
                    ->whereDate('tanggal_lembur', '<=', $weekEnd)
                    ->whereIn('status', ['approved', 'processed'])
                    ->with('tunjanganKaryawan')
                    ->get();

                // Detail lembur per hari
                $dailyLembur = [];
                $currentDate = $weekStart->copy();

                while ($currentDate <= $weekEnd) {
                    // FIX: Gunakan whereDate di collection dengan format yang benar
                    $lembur = $lemburList->first(function ($item) use ($currentDate) {
                        return $item->tanggal_lembur->format('Y-m-d') === $currentDate->format('Y-m-d');
                    });

                    $dailyLembur[] = [
                        'date' => $currentDate->copy(),
                        'day_name' => $currentDate->format('D'),
                        'lembur' => $lembur,
                        'has_lembur' => $lembur ? true : false,
                        'jam_mulai' => $lembur ? $lembur->jam_mulai : null,
                        'jam_selesai' => $lembur ? $lembur->jam_selesai : null,
                        'total_jam' => $lembur ? $lembur->total_jam : 0,
                        'kategori' => $lembur ? $lembur->kategori_lembur : null,
                    ];

                    $currentDate->addDay();
                }

                $totalJam = $lemburList->sum('total_jam');
                $totalLembur = $lemburList->count();
                $totalAmount = $lemburList->sum(function ($lembur) {
                    return $lembur->tunjanganKaryawan ? $lembur->tunjanganKaryawan->total_amount : 0;
                });

                $anyTaken = $lemburList->some(function ($lembur) {
                    return $lembur->tunjanganKaryawan &&
                        in_array($lembur->tunjanganKaryawan->status, ['approved', 'received']);
                });

                // Ambil tanggal approve/receive pertama yang ada
                $firstApproved = $lemburList->filter(function ($lembur) {
                    return $lembur->tunjanganKaryawan && $lembur->tunjanganKaryawan->approved_at;
                })->first();

                $firstReceived = $lemburList->filter(function ($lembur) {
                    return $lembur->tunjanganKaryawan && $lembur->tunjanganKaryawan->received_at;
                })->first();

                $employeeData[] = [
                    'karyawan' => $karyawan,
                    'tunjangan' => $lemburList->first()->tunjanganKaryawan ?? null,
                    'daily_lembur' => $dailyLembur,
                    'total_lembur' => $totalLembur,
                    'total_jam' => $totalJam,
                    'is_taken' => $anyTaken,
                    'status' => $anyTaken ? 'approved' : ($totalLembur > 0 ? 'pending' : 'no_data'),
                    'amount' => $totalAmount,
                    'approved_date' => $firstApproved && $firstApproved->tunjanganKaryawan ?
                        $firstApproved->tunjanganKaryawan->approved_at->format('d/m/Y') : null,
                    'received_date' => $firstReceived && $firstReceived->tunjanganKaryawan ?
                        $firstReceived->tunjanganKaryawan->received_at->format('d/m/Y') : null,
                    'source' => 'lembur',
                ];
            } else {
                // ✅ UNTUK UANG MAKAN: Dari TunjanganKaryawan + Absen
                $tunjangan = TunjanganKaryawan::where('karyawan_id', $karyawan->karyawan_id)
                    ->where('tunjangan_type_id', $tunjanganType->tunjangan_type_id)
                    ->whereNull('absen_id')
                    ->where(function ($query) use ($weekStart, $weekEnd) {
                        $query->whereBetween('period_start', [$weekStart, $weekEnd])
                            ->orWhereBetween('period_end', [$weekStart, $weekEnd])
                            ->orWhere(function ($q) use ($weekStart, $weekEnd) {
                                $q->where('period_start', '<=', $weekStart)
                                    ->where('period_end', '>=', $weekEnd);
                            });
                    })
                    ->first();

                $dailyAttendance = [];
                $currentDate = $weekStart->copy();

                while ($currentDate <= $weekEnd) {
                    $absen = Absen::where('karyawan_id', $karyawan->karyawan_id)
                        ->whereDate('date', $currentDate)
                        ->first();

                    $dailyAttendance[] = [
                        'date' => $currentDate->copy(),
                        'day_name' => $currentDate->format('D'),
                        'absen' => $absen,
                        'is_present' => $absen && $absen->clock_in ? true : false,
                        'clock_in' => $absen && $absen->clock_in ? $absen->clock_in : null,
                        'clock_out' => $absen && $absen->clock_out ? $absen->clock_out : null,
                    ];

                    $currentDate->addDay();
                }

                $workDays = collect($dailyAttendance)->where('is_present', true)->count();

                $employeeData[] = [
                    'karyawan' => $karyawan,
                    'tunjangan' => $tunjangan,
                    'daily_attendance' => $dailyAttendance,
                    'work_days' => $workDays,
                    'is_taken' => $tunjangan ? in_array($tunjangan->status, ['approved', 'received']) : false,
                    'status' => $tunjangan ? $tunjangan->status : 'no_data',
                    'amount' => $tunjangan ? $tunjangan->total_amount : 0,
                    'approved_date' => $tunjangan && $tunjangan->approved_at ? $tunjangan->approved_at->format('d/m/Y') : null,
                    'received_date' => $tunjangan && $tunjangan->received_at ? $tunjangan->received_at->format('d/m/Y') : null,
                    'source' => 'absen',
                ];
            }
        }

        $summary = [
            'total_employees' => count($employeeData),
            'taken_count' => collect($employeeData)->where('is_taken', true)->count(),
            'not_taken_count' => collect($employeeData)->where('is_taken', false)->where('status', '!=', 'no_data')->count(),
            'no_data_count' => collect($employeeData)->where('status', 'no_data')->count(),
            'total_work_days' => $isLembur ?
                collect($employeeData)->sum('total_lembur') :
                collect($employeeData)->sum('work_days'),
            'total_amount' => collect($employeeData)->sum('amount'),
            'total_jam_lembur' => $isLembur ? collect($employeeData)->sum('total_jam') : 0,
        ];

        $data = [
            'tunjanganType' => $tunjanganType,
            'week_info' => $weekInfo,
            'employees' => $employeeData,
            'summary' => $summary,
            'report_type' => 'single_week',
            'generated_at' => now()->format('d/m/Y H:i:s'),
            'is_lembur' => $isLembur,
        ];

        $pdf = Pdf::loadView('admin.reports.single-week-report', $data);
        $pdf->setPaper('A4', 'landscape');

        $filename = "Laporan_Tunjangan_Minggu_{$weekStart->format('d-m-Y')}_sampai_{$weekEnd->format('d-m-Y')}.pdf";
        return $pdf->download($filename);
    }
    private function getWeekDays($startDate, $endDate)
    {
        $days = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $days[] = [
                'date' => $current->copy(),
                'day_short' => $current->format('D'), // Mon, Tue, Wed
                'day_long' => $current->format('l'),  // Monday, Tuesday
                'date_num' => $current->format('d'),
                'is_weekend' => in_array($current->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY]),
            ];
            $current->addDay();
        }

        return $days;
    }
}

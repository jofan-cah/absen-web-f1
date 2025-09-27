<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lembur;
use App\Models\Karyawan;
use App\Models\TunjanganKaryawan;
use App\Models\TunjanganType;
use App\Models\TunjanganDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class LemburController extends Controller
{
    /**
     * Display listing - untuk admin approval
     */
    public function index(Request $request)
    {
        $query = Lembur::with(['karyawan.user', 'karyawan.department', 'absen', 'approvedBy', 'rejectedBy', 'tunjanganKaryawan']);

        // Filter by karyawan
        if ($request->filled('karyawan_id')) {
            $query->where('karyawan_id', $request->karyawan_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by kategori
        if ($request->filled('kategori_lembur')) {
            $query->where('kategori_lembur', $request->kategori_lembur);
        }

        // Filter by tanggal
        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal_lembur', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal_lembur', '<=', $request->tanggal_sampai);
        }

        $lemburs = $query->orderBy('tanggal_lembur', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Data untuk filter
        $karyawans = Karyawan::with('user')
            ->where('employment_status', 'active')
            ->orderBy('full_name')
            ->get();

        $statusOptions = ['draft', 'submitted', 'approved', 'rejected', 'processed'];
        $kategoriOptions = ['reguler', 'hari_libur', 'hari_besar'];

        // Summary
        $summary = [
            'total' => Lembur::count(),
            'submitted' => Lembur::where('status', 'submitted')->count(),
            'approved' => Lembur::where('status', 'approved')->count(),
            'rejected' => Lembur::where('status', 'rejected')->count(),
            'total_jam_bulan_ini' => Lembur::whereYear('tanggal_lembur', now()->year)
                ->whereMonth('tanggal_lembur', now()->month)
                ->sum('total_jam'),
        ];

        return view('admin.lembur.indexLembur', compact(
            'lemburs',
            'karyawans',
            'statusOptions',
            'kategoriOptions',
            'summary'
        ));
    }

    /**
     * Show detail lembur
     */
    public function show(Lembur $lembur)
    {
        $lembur->load([
            'karyawan.user',
            'karyawan.department',
            'absen.jadwal.shift',
            'approvedBy',
            'rejectedBy',
            'createdBy',
            'tunjanganKaryawan'
        ]);

        return view('admin.lembur.showLembur', compact('lembur'));
    }

    /**
     * Approve lembur - PENTING untuk admin
     */
    public function approve(Lembur $lembur, Request $request)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500'
        ]);

        if ($lembur->status !== 'submitted') {
            return response()->json([
                'success' => false,
                'message' => 'Lembur tidak dapat disetujui! Status saat ini: ' . $lembur->status
            ], 422);
        }

        try {
            DB::beginTransaction();

            $result = $lembur->approve(Auth::id(), $request->notes);

            if (!$result) {
                throw new \Exception('Gagal menyetujui lembur');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lembur berhasil disetujui dan tunjangan telah dibuat!',
                'tunjangan_id' => $lembur->tunjangan_karyawan_id
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal approve lembur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject lembur
     */
    public function reject(Lembur $lembur, Request $request)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        if ($lembur->status !== 'submitted') {
            return response()->json([
                'success' => false,
                'message' => 'Lembur tidak dapat ditolak! Status saat ini: ' . $lembur->status
            ], 422);
        }

        try {
            DB::beginTransaction();

            $result = $lembur->reject(Auth::id(), $request->rejection_reason);

            if (!$result) {
                throw new \Exception('Gagal menolak lembur');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lembur berhasil ditolak'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal reject lembur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk approve - untuk approve banyak sekaligus
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:lemburs,lembur_id',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $approved = 0;
            $errors = [];

            foreach ($request->ids as $id) {
                try {
                    $lembur = Lembur::find($id);

                    if ($lembur->status !== 'submitted') {
                        $errors[] = "{$lembur->karyawan->full_name} - Status tidak valid";
                        continue;
                    }

                    $result = $lembur->approve(Auth::id(), $request->notes);

                    if ($result) {
                        $approved++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "{$lembur->karyawan->full_name} - {$e->getMessage()}";
                }
            }

            DB::commit();

            $message = "{$approved} lembur berhasil disetujui";
            if (!empty($errors)) {
                $message .= ". Error: " . implode(', ', array_slice($errors, 0, 3));
            }

            return response()->json([
                'success' => $approved > 0,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal bulk approve: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:lemburs,lembur_id'
        ]);

        try {
            DB::beginTransaction();

            $lemburs = Lembur::whereIn('lembur_id', $request->ids)->get();

            foreach ($lemburs as $lembur) {
                if (!$lembur->canEdit()) {
                    throw new \Exception("Lembur untuk '{$lembur->karyawan->full_name}' tidak dapat dihapus!");
                }
            }

            // Delete photos
            foreach ($lemburs as $lembur) {
                if ($lembur->bukti_foto) {
                    Storage::disk('s3')->delete($lembur->bukti_foto);
                }
            }

            Lembur::whereIn('lembur_id', $request->ids)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' data lembur berhasil dihapus!'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate tunjangan lembur mingguan
     * Akan generate tunjangan untuk semua lembur yang approved dalam 1 minggu
     */
    public function generateTunjanganMingguan(Request $request)
    {
        $request->validate([
            'week_start' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $weekStart = Carbon::parse($request->week_start)->startOfWeek(Carbon::MONDAY);
            $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

            // Get tunjangan type lembur
            $tunjanganType = TunjanganType::where('code', 'UANG_LEMBUR')->active()->first();

            if (!$tunjanganType) {
                throw new \Exception('Tunjangan type UANG_LEMBUR tidak ditemukan');
            }

            // Get semua lembur approved dalam minggu ini yang belum di-generate
            $lemburs = Lembur::with('karyawan')
                ->where('status', 'approved')
                ->whereNull('tunjangan_karyawan_id')
                ->whereBetween('tanggal_lembur', [$weekStart, $weekEnd])
                ->get();

            if ($lemburs->isEmpty()) {
                throw new \Exception('Tidak ada lembur yang perlu di-generate untuk minggu ini');
            }

            // Group by karyawan
            $lemburByKaryawan = $lemburs->groupBy('karyawan_id');

            $generated = 0;

            foreach ($lemburByKaryawan as $karyawanId => $karyawanLemburs) {
                $karyawan = $karyawanLemburs->first()->karyawan;

                // Hitung total jam dan amount
                $totalJam = $karyawanLemburs->sum('total_jam');

                // Get amount per jam berdasarkan staff status
                $amountPerJam = TunjanganDetail::getAmountByStaffStatus(
                    $tunjanganType->tunjangan_type_id,
                    $karyawan->staff_status
                );

                // Hitung weighted amount (karena ada multiplier berbeda)
                $totalAmount = 0;
                $details = [];
                foreach ($karyawanLemburs as $lembur) {
                    $lemburAmount = $amountPerJam * $lembur->multiplier * $lembur->total_jam;
                    $totalAmount += $lemburAmount;
                    $details[] = "{$lembur->kategori_lembur}: {$lembur->total_jam} jam @ {$lembur->multiplier}x";
                }

                // Create tunjangan karyawan
                $tunjangan = TunjanganKaryawan::create([
                    'tunjangan_karyawan_id' => TunjanganKaryawan::generateTunjanganKaryawanId(),
                    'karyawan_id' => $karyawanId,
                    'tunjangan_type_id' => $tunjanganType->tunjangan_type_id,
                    'period_start' => $weekStart,
                    'period_end' => $weekEnd,
                    'amount' => $amountPerJam,
                    'quantity' => $totalJam,
                    'total_amount' => $totalAmount,
                    'status' => 'pending',
                    'notes' => "Lembur mingguan (" . $weekStart->format('d/m') . " - " . $weekEnd->format('d/m') . "): " . implode(', ', $details),
                ]);

                // Update semua lembur dengan tunjangan_id dan status processed
                foreach ($karyawanLemburs as $lembur) {
                    $lembur->update([
                        'tunjangan_karyawan_id' => $tunjangan->tunjangan_karyawan_id,
                        'status' => 'processed'
                    ]);
                }

                $generated++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil generate {$generated} tunjangan lembur mingguan untuk periode {$weekStart->format('d/m/Y')} - {$weekEnd->format('d/m/Y')}"
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate tunjangan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Form generate tunjangan mingguan
     */
    public function generateTunjanganForm()
    {
        // Get minggu-minggu yang ada lembur approved belum di-generate
        $availableWeeks = Lembur::selectRaw('DATE(DATE_SUB(tanggal_lembur, INTERVAL WEEKDAY(tanggal_lembur) DAY)) as week_start')
            ->where('status', 'approved')
            ->whereNull('tunjangan_karyawan_id')
            ->groupBy('week_start')
            ->orderBy('week_start', 'desc')
            ->get()
            ->map(function($item) {
                $start = Carbon::parse($item->week_start);
                $end = $start->copy()->endOfWeek(Carbon::SUNDAY);
                return [
                    'week_start' => $start->format('Y-m-d'),
                    'label' => $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y'),
                    'count' => Lembur::where('status', 'approved')
                        ->whereNull('tunjangan_karyawan_id')
                        ->whereBetween('tanggal_lembur', [$start, $end])
                        ->count()
                ];
            });

        return view('admin.lembur.generate-tunjanganLembur', compact('availableWeeks'));
    }

    /**
     * Export report
     */
    public function export(Request $request)
    {
        try {
            $query = Lembur::with(['karyawan', 'karyawan.department']);

            // Apply filters
            if ($request->filled('karyawan_id')) {
                $query->where('karyawan_id', $request->karyawan_id);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('tanggal_dari')) {
                $query->where('tanggal_lembur', '>=', $request->tanggal_dari);
            }
            if ($request->filled('tanggal_sampai')) {
                $query->where('tanggal_lembur', '<=', $request->tanggal_sampai);
            }

            $lemburs = $query->orderBy('tanggal_lembur', 'desc')->get();

            $filename = 'lembur_' . date('Y-m-d_H-i-s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($lemburs) {
                $file = fopen('php://output', 'w');

                // Header CSV
                fputcsv($file, [
                    'ID Lembur',
                    'Karyawan',
                    'Department',
                    'Tanggal',
                    'Jam Mulai',
                    'Jam Selesai',
                    'Total Jam',
                    'Kategori',
                    'Multiplier',
                    'Deskripsi',
                    'Status',
                    'Disetujui Oleh',
                    'Tanggal Approve',
                    'Ditolak Oleh',
                    'Tanggal Reject',
                    'Alasan Ditolak',
                ]);

                // Data rows
                foreach ($lemburs as $lembur) {
                    fputcsv($file, [
                        $lembur->lembur_id,
                        $lembur->karyawan->full_name,
                        $lembur->karyawan->department->name ?? '-',
                        $lembur->tanggal_lembur->format('d-m-Y'),
                        $lembur->jam_mulai,
                        $lembur->jam_selesai,
                        $lembur->total_jam,
                        ucfirst(str_replace('_', ' ', $lembur->kategori_lembur)),
                        $lembur->multiplier . 'x',
                        $lembur->deskripsi_pekerjaan ?? '-',
                        ucfirst($lembur->status),
                        $lembur->approvedBy->name ?? '-',
                        $lembur->approved_at ? $lembur->approved_at->format('d-m-Y H:i') : '-',
                        $lembur->rejectedBy->name ?? '-',
                        $lembur->rejected_at ? $lembur->rejected_at->format('d-m-Y H:i') : '-',
                        $lembur->rejection_reason ?? '-',
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal export data: ' . $e->getMessage());
        }
    }

    /**
     * Report analytics
     */
    public function report(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $query = Lembur::with(['karyawan', 'karyawan.department'])
            ->whereBetween('tanggal_lembur', [$startDate, $endDate]);

        // Summary
        $summary = [
            'total_lembur' => $query->count(),
            'total_jam' => $query->sum('total_jam'),
            'approved' => $query->clone()->where('status', 'approved')->count(),
            'processed' => $query->clone()->where('status', 'processed')->count(),
            'rejected' => $query->clone()->where('status', 'rejected')->count(),
            'pending' => $query->clone()->where('status', 'submitted')->count(),
        ];

        // By kategori
        $byKategori = $query->clone()
            ->select('kategori_lembur', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_jam) as total_jam'))
            ->groupBy('kategori_lembur')
            ->get();

        // By karyawan (top 10)
        $byKaryawan = $query->clone()
            ->select('karyawan_id', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_jam) as total_jam'))
            ->groupBy('karyawan_id')
            ->with('karyawan')
            ->orderByDesc('total_jam')
            ->limit(10)
            ->get();

        // By department
        $byDepartment = $query->clone()
            ->join('karyawans', 'lemburs.karyawan_id', '=', 'karyawans.karyawan_id')
            ->join('departments', 'karyawans.department_id', '=', 'departments.department_id')
            ->select('departments.name', DB::raw('COUNT(*) as count'), DB::raw('SUM(lemburs.total_jam) as total_jam'))
            ->groupBy('departments.name')
            ->orderByDesc('total_jam')
            ->get();

        return view('admin.lembur.reportLembur', compact(
            'summary',
            'byKategori',
            'byKaryawan',
            'byDepartment',
            'startDate',
            'endDate'
        ));
    }
}



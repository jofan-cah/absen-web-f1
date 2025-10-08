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
        $query = Lembur::with(['karyawan.user', 'karyawan.department', 'absen', 'approvedBy', 'rejectedBy', 'tunjanganKaryawan', 'coordinator']);

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
            'tunjanganKaryawan',
            'coordinator'
        ]);

        return view('admin.lembur.showLembur', compact('lembur'));
    }

    /**
     * Approve lembur - DENGAN LOGIC BARU (Koordinator untuk Tim Teknis, Admin untuk lainnya)
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

        $user = Auth::user();
        $karyawan = $lembur->karyawan;

        // LOGIC BARU: Cek department untuk approval
        $isTimTeknis = $karyawan->department &&
                       (stripos($karyawan->department->name, 'teknis') !== false ||
                        stripos($karyawan->department->code, 'teknis') !== false);

        $canApprove = false;
        $approvalMessage = '';

        if ($isTimTeknis) {
            // TIM TEKNIS: Harus Koordinator dari department yang sama
            if ($user->karyawan &&
                $user->karyawan->department_id === $karyawan->department_id &&
                in_array($user->karyawan->staff_status, ['koordinator', 'wakil_koordinator'])) {
                $canApprove = true;
                $approvalMessage = 'koordinator';
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Lembur Tim Teknis hanya dapat diapprove oleh Koordinator departmentnya'
                ], 403);
            }
        } else {
            // SELAIN TIM TEKNIS: Admin
            if ($user->role === 'admin') {
                $canApprove = true;
                $approvalMessage = 'admin';
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Lembur hanya dapat diapprove oleh Admin'
                ], 403);
            }
        }

        try {
            DB::beginTransaction();

            $result = $lembur->approve(Auth::id(), $request->notes);

            if (!$result) {
                throw new \Exception('Gagal menyetujui lembur');
            }

            // Simpan coordinator_id jika yang approve adalah koordinator
            if ($approvalMessage === 'koordinator' && $user->karyawan) {
                $lembur->update(['coordinator_id' => $user->karyawan->karyawan_id]);
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
     * Bulk approve - DENGAN LOGIC VALIDASI PERMISSION BARU
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

            $user = Auth::user();
            $approved = 0;
            $errors = [];

            foreach ($request->ids as $id) {
                try {
                    $lembur = Lembur::with('karyawan.department')->find($id);

                    if ($lembur->status !== 'submitted') {
                        $errors[] = "{$lembur->karyawan->full_name} - Status tidak valid";
                        continue;
                    }

                    // Cek permission berdasarkan department
                    $karyawan = $lembur->karyawan;
                    $isTimTeknis = $karyawan->department &&
                                   (stripos($karyawan->department->name, 'teknis') !== false ||
                                    stripos($karyawan->department->code, 'teknis') !== false);

                    $canApprove = false;
                    $approvalType = '';

                    if ($isTimTeknis) {
                        if ($user->karyawan &&
                            $user->karyawan->department_id === $karyawan->department_id &&
                            in_array($user->karyawan->staff_status, ['koordinator', 'wakil_koordinator'])) {
                            $canApprove = true;
                            $approvalType = 'koordinator';
                        }
                    } else {
                        if ($user->role === 'admin') {
                            $canApprove = true;
                            $approvalType = 'admin';
                        }
                    }

                    if (!$canApprove) {
                        $errors[] = "{$lembur->karyawan->full_name} - Tidak ada permission";
                        continue;
                    }

                    $result = $lembur->approve(Auth::id(), $request->notes);

                    if ($result) {
                        // Simpan coordinator_id jika koordinator yang approve
                        if ($approvalType === 'koordinator' && $user->karyawan) {
                            $lembur->update(['coordinator_id' => $user->karyawan->karyawan_id]);
                        }
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
                    Storage::disk('public')->delete($lembur->bukti_foto);
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
                ->whereBetween('tanggal_lembur', [$weekStart, $weekEnd])
                ->whereNull('tunjangan_karyawan_id')
                ->get();

            if ($lemburs->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada lembur yang perlu di-generate tunjangan untuk periode ini'
                ], 404);
            }

            $generated = 0;
            foreach ($lemburs as $lembur) {
                try {
                    $lembur->generateTunjangan();
                    $generated++;
                } catch (\Exception $e) {
                    \Log::error("Gagal generate tunjangan untuk lembur {$lembur->lembur_id}: " . $e->getMessage());
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$generated} tunjangan berhasil di-generate untuk periode {$weekStart->format('d/m/Y')} - {$weekEnd->format('d/m/Y')}"
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
     * Form untuk generate tunjangan mingguan
     */
    public function generateTunjanganForm()
    {
        return view('admin.lembur.generateTunjangan');
    }

    /**
     * Report analytics lembur
     */
    public function report(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        // Analytics data
        $analytics = [
            'total_lembur' => Lembur::whereBetween('tanggal_lembur', [$startDate, $endDate])->count(),
            'total_approved' => Lembur::whereBetween('tanggal_lembur', [$startDate, $endDate])
                ->whereIn('status', ['approved', 'processed'])->count(),
            'total_jam' => Lembur::whereBetween('tanggal_lembur', [$startDate, $endDate])
                ->whereIn('status', ['approved', 'processed'])->sum('total_jam'),
            'by_kategori' => Lembur::whereBetween('tanggal_lembur', [$startDate, $endDate])
                ->whereIn('status', ['approved', 'processed'])
                ->selectRaw('kategori_lembur, COUNT(*) as count, SUM(total_jam) as total_jam')
                ->groupBy('kategori_lembur')
                ->get(),
            'by_department' => Lembur::with('karyawan.department')
                ->whereBetween('tanggal_lembur', [$startDate, $endDate])
                ->whereIn('status', ['approved', 'processed'])
                ->get()
                ->groupBy('karyawan.department.name')
                ->map(function ($items) {
                    return [
                        'count' => $items->count(),
                        'total_jam' => $items->sum('total_jam')
                    ];
                }),
        ];

        return view('admin.lembur.report', compact('analytics', 'month', 'year'));
    }

    /**
     * Export data lembur
     */
    public function export(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $format = $request->get('format', 'pdf'); // pdf or csv

        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $lemburs = Lembur::with(['karyawan.user', 'karyawan.department', 'absen.jadwal.shift', 'approvedBy'])
            ->whereBetween('tanggal_lembur', [$startDate, $endDate])
            ->orderBy('tanggal_lembur')
            ->get();

        if ($format === 'csv') {
            return $this->exportCsv($lemburs, $month, $year);
        }

        return $this->exportPdf($lemburs, $month, $year);
    }

    private function exportCsv($lemburs, $month, $year)
    {
        $filename = "Laporan_Lembur_{$month}_{$year}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($lemburs) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Tanggal',
                'NIP',
                'Nama',
                'Department',
                'Jam Mulai',
                'Jam Selesai',
                'Total Jam',
                'Kategori',
                'Status',
                'Disetujui Oleh'
            ]);

            foreach ($lemburs as $lembur) {
                fputcsv($file, [
                    $lembur->tanggal_lembur->format('d/m/Y'),
                    $lembur->karyawan->nip,
                    $lembur->karyawan->full_name,
                    $lembur->karyawan->department->name ?? '-',
                    $lembur->jam_mulai,
                    $lembur->jam_selesai,
                    $lembur->total_jam,
                    $lembur->kategori_lembur,
                    $lembur->status,
                    $lembur->approvedBy->name ?? '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportPdf($lemburs, $month, $year)
    {
        $data = [
            'lemburs' => $lemburs,
            'month' => $month,
            'year' => $year,
            'period' => Carbon::create($year, $month)->format('F Y')
        ];

        $pdf = Pdf::loadView('admin.lembur.exportPdf', $data);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download("Laporan_Lembur_{$month}_{$year}.pdf");
    }
}

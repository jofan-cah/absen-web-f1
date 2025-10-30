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
use Illuminate\Support\Facades\Log;

class LemburController extends Controller
{
    /**
     * Display listing - untuk admin approval
     */
    public function index(Request $request)
    {
        $query = Lembur::with([
            'karyawan.user',
            'karyawan.department',
            'absen',
            'approvedBy',
            'rejectedBy',
            'tunjanganKaryawan',
            'coordinator'
        ]);

        // Filter by karyawan
        if ($request->filled('karyawan_id')) {
            $query->where('karyawan_id', $request->karyawan_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 🆕 Filter by koordinator_status
        if ($request->filled('koordinator_status')) {
            $query->where('koordinator_status', $request->koordinator_status);
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
        $koordinatorStatusOptions = ['pending', 'approved', 'rejected']; // 🆕

        // Summary dengan breakdown koordinator
        $summary = [
            'total' => Lembur::count(),
            'submitted' => Lembur::where('status', 'submitted')->count(),

            // 🆕 Breakdown submitted
            'pending_koordinator' => Lembur::where('status', 'submitted')
                ->where('koordinator_status', 'pending')
                ->count(),
            'pending_admin' => Lembur::where('status', 'submitted')
                ->where('koordinator_status', 'approved')
                ->count(),

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
            'koordinatorStatusOptions', // 🆕
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

        // VALIDASI: User harus admin
        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Admin yang dapat melakukan approval'
            ], 403);
        }

        try {
            DB::beginTransaction();

            // ✅ CEK JENIS LEMBUR: OnCall atau Request biasa?
            if ($lembur->jenis_lembur === 'oncall') {
                dd('JOFAN');
                // 🔥 ONCALL: Auto-generate tunjangan LANGSUNG
                $result = $lembur->approveOnCall($user->user_id, $request->notes);
                $message = 'OnCall berhasil disetujui dan tunjangan telah dibuat secara langsung!';
            } else {
                // 📋 LEMBUR REQUEST: Flow normal
                if ($lembur->koordinator_status === 'approved') {
                    $result = $lembur->approveByAdmin($user->user_id, $request->notes);
                    $message = 'Lembur berhasil disetujui oleh Admin dan tunjangan telah dibuat!';
                } else {
                    $result = $lembur->approveByAdminDirect($user->user_id, $request->notes);
                    $message = 'Lembur berhasil disetujui langsung oleh Admin (bypass koordinator)!';
                }
            }

            if (!$result) {
                throw new \Exception('Gagal menyetujui lembur');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
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
    /**
     * Reject lembur - LEVEL 2 (Admin)
     * Admin bisa reject walau koordinator sudah approve
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

        $user = Auth::user();

        // VALIDASI: User harus admin
        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Admin yang dapat reject lembur'
            ], 403);
        }

        try {
            DB::beginTransaction();

            // Reject oleh admin (LEVEL 2)
            $result = $lembur->rejectByAdmin($user->user_id, $request->rejection_reason);

            if (!$result) {
                throw new \Exception('Gagal menolak lembur');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lembur berhasil ditolak oleh Admin'
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
    /**
     * Bulk approve - LEVEL 2 (Admin)
     * Hanya approve yang sudah di-approve koordinator
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

            // Validasi: User harus admin
            if ($user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya Admin yang dapat melakukan bulk approve'
                ], 403);
            }

            $approved = 0;
            $errors = [];

            foreach ($request->ids as $id) {
                try {
                    $lembur = Lembur::with('karyawan.department')->find($id);

                    if ($lembur->status !== 'submitted') {
                        $errors[] = "{$lembur->karyawan->full_name} - Status tidak valid";
                        continue;
                    }

                    // VALIDASI: Koordinator harus sudah approve
                    if ($lembur->koordinator_status !== 'approved') {
                        $errors[] = "{$lembur->karyawan->full_name} - Belum diapprove koordinator";
                        continue;
                    }

                    // Approve oleh admin (LEVEL 2)
                    $result = $lembur->approveByAdmin($user->user_id, $request->notes);

                    if ($result) {
                        $approved++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "{$lembur->karyawan->full_name} - {$e->getMessage()}";
                }
            }

            DB::commit();

            $message = "{$approved} lembur berhasil disetujui oleh Admin";
            if (!empty($errors)) {
                $message .= ". Error: " . implode(', ', $errors);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'approved_count' => $approved,
                'error_count' => count($errors)
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
                    Log::error("Gagal generate tunjangan untuk lembur {$lembur->lembur_id}: " . $e->getMessage());
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

        $callback = function () use ($lemburs) {
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

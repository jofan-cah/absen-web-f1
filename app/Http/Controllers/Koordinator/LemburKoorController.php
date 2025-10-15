<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\Lembur;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LemburKoorController extends Controller
{
    /**
     * Display listing - HANYA lembur di department koordinator
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $koordinator = $user->karyawan;

        // Validasi: User harus punya data karyawan
        if (!$koordinator) {
            abort(403, 'Data karyawan tidak ditemukan');
        }

        // Validasi: Harus koordinator atau wakil koordinator
        if (!in_array($koordinator->staff_status, ['koordinator', 'wakil_koordinator'])) {
            abort(403, 'Akses ditolak. Hanya untuk Koordinator.');
        }

        // Query lembur: HANYA di department koordinator (TANPA filter Tim Teknis)
        $query = Lembur::with([
            'karyawan.user',
            'karyawan.department',
            'absen',
            'approvedBy',
            'rejectedBy',
            'coordinator'
        ])
            ->whereHas('karyawan', function ($q) use ($koordinator) {
                // Filter: HANYA Department yang sama
                $q->where('department_id', $koordinator->department_id);
            });

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
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

        // Summary untuk department ini (TANPA filter Tim Teknis)
        $summary = [
            'total' => Lembur::whereHas('karyawan', function ($q) use ($koordinator) {
                $q->where('department_id', $koordinator->department_id);
            })->count(),
            'submitted' => Lembur::where('status', 'submitted')
                ->whereHas('karyawan', function ($q) use ($koordinator) {
                    $q->where('department_id', $koordinator->department_id);
                })->count(),
            'approved' => Lembur::where('status', 'approved')
                ->whereHas('karyawan', function ($q) use ($koordinator) {
                    $q->where('department_id', $koordinator->department_id);
                })->count(),
            'rejected' => Lembur::where('status', 'rejected')
                ->whereHas('karyawan', function ($q) use ($koordinator) {
                    $q->where('department_id', $koordinator->department_id);
                })->count(),
        ];

        $statusOptions = ['draft', 'submitted', 'approved', 'rejected', 'processed'];

        return view('koordinator.lembur.indexLemburKoor', compact(
            'lemburs',
            'statusOptions',
            'summary'
        ));
    }
    public function show(Lembur $lembur)
    {
        $user = Auth::user();
        $koordinator = $user->karyawan;

        // Validasi: Lembur harus dari department koordinator
        if ($lembur->karyawan->department_id !== $koordinator->department_id) {
            abort(403, 'Anda tidak memiliki akses ke lembur ini');
        }

        // Validasi: Harus Tim Teknis (update pattern matching)
        $isTimTeknis = $lembur->karyawan->department &&
            (stripos($lembur->karyawan->department->name, 'teknis') !== false ||
                stripos($lembur->karyawan->department->code, 'teknis') !== false ||
                stripos($lembur->karyawan->department->name, 'Technical Support') !== false ||
                stripos($lembur->karyawan->department->code, 'TECH') !== false);

        if (!$isTimTeknis) {
            abort(403, 'Lembur ini bukan dari Tim Teknis');
        }

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

        return view('koordinator.lembur.showLemburKoor', compact('lembur'));
    }
    /**
     * Approve lembur - LEVEL 1 (Koordinator)
     * Tidak generate tunjangan, hanya approve koordinator_status
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

        // Validasi: Koordinator belum review
        if ($lembur->koordinator_status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Lembur sudah di-review sebelumnya oleh koordinator'
            ], 422);
        }

        $user = Auth::user();
        $koordinator = $user->karyawan;
        $karyawan = $lembur->karyawan;

        // VALIDASI 1: Koordinator harus punya data karyawan
        if (!$koordinator) {
            return response()->json([
                'success' => false,
                'message' => 'Data koordinator tidak ditemukan'
            ], 403);
        }

        // VALIDASI 2: Harus koordinator atau wakil koordinator
        if (!in_array($koordinator->staff_status, ['koordinator', 'wakil_koordinator'])) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Koordinator yang dapat approve lembur'
            ], 403);
        }

        // VALIDASI 3: Department harus sama
        if ($koordinator->department_id !== $karyawan->department_id || $user->status == 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Anda hanya dapat approve lembur di department Anda'
            ], 403);
        }

        // VALIDASI 4: Harus Tim Teknis
        $isTimTeknis = $karyawan->department &&
            (stripos($karyawan->department->name, 'teknis') !== false ||
                stripos($karyawan->department->code, 'teknis') !== false ||
                stripos($karyawan->department->name, 'Technical Support') !== false ||
                stripos($karyawan->department->code, 'TECH') !== false);

        if (!$isTimTeknis) {
            return response()->json([
                'success' => false,
                'message' => 'Anda hanya dapat approve lembur Tim Teknis'
            ], 403);
        }

        try {
            DB::beginTransaction();

            // Approve oleh koordinator (LEVEL 1)
            $result = $lembur->approveByKoordinator($user->user_id, $request->notes);

            if (!$result) {
                throw new \Exception('Gagal menyetujui lembur');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lembur berhasil disetujui oleh Koordinator! Menunggu approval Admin untuk generate tunjangan.'
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
     * Reject lembur - LEVEL 1 (Koordinator)
     * Reject koordinator = reject final (tidak perlu admin review)
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

        // Validasi: Koordinator belum review
        if ($lembur->koordinator_status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Lembur sudah di-review sebelumnya oleh koordinator'
            ], 422);
        }

        $user = Auth::user();
        $koordinator = $user->karyawan;
        $karyawan = $lembur->karyawan;

        // VALIDASI: Department harus sama
        if ($koordinator->department_id !== $karyawan->department_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda hanya dapat reject lembur di department Anda'
            ], 403);
        }

        try {
            DB::beginTransaction();

            // Reject oleh koordinator (LEVEL 1 - FINAL)
            $result = $lembur->rejectByKoordinator($user->user_id, $request->rejection_reason);

            if (!$result) {
                throw new \Exception('Gagal menolak lembur');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lembur berhasil ditolak oleh Koordinator'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal reject lembur: ' . $e->getMessage()
            ], 500);
        }
    }
}

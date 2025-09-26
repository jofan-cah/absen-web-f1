<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penalti;
use App\Models\Karyawan;
use App\Models\Absen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PenaltiController extends Controller
{
    public function index(Request $request)
    {
        $query = Penalti::with(['karyawan', 'absen', 'createdBy', 'approvedBy']);

        // Filter by karyawan
        if ($request->filled('karyawan_id')) {
            $query->where('karyawan_id', $request->karyawan_id);
        }

        // Filter by jenis penalti
        if ($request->filled('jenis_penalti')) {
            $query->where('jenis_penalti', $request->jenis_penalti);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by tanggal penalti
        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal_penalti', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal_penalti', '<=', $request->tanggal_sampai);
        }

        $penaltis = $query->orderBy('tanggal_penalti', 'desc')
            ->paginate(15);

        $karyawans = Karyawan::with('user')
            ->where('employment_status', 'active')
            ->get();

        $jenisOptions = ['telat', 'tidak_masuk', 'pelanggaran', 'custom'];
        $statusOptions = ['active', 'completed', 'cancelled'];

        return view('admin.penalti.indexPenalti', compact('penaltis', 'karyawans', 'jenisOptions', 'statusOptions'));
    }

    public function create(Request $request)
    {
        $karyawans = Karyawan::with('user')
            ->where('employment_status', 'active')
            ->get();

        $jenisOptions = ['telat', 'tidak_masuk', 'pelanggaran', 'custom'];

        // Jika dari absen tertentu
        $absen = null;
        if ($request->filled('absen_id')) {
            $absen = Absen::with('karyawan')->find($request->absen_id);
        }

        return view('admin.penalti.createPenalti', compact('karyawans', 'jenisOptions', 'absen'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,karyawan_id',
            'absen_id' => 'nullable|exists:absens,absen_id',
            'jenis_penalti' => 'required|in:telat,tidak_masuk,pelanggaran,custom',
            'deskripsi' => 'required|string',
            'hari_potong_uang_makan' => 'required|integer|min:0|max:31',
            'tanggal_penalti' => 'required|date',
            'periode_berlaku_mulai' => 'required|date',
            'periode_berlaku_akhir' => 'required|date|after_or_equal:periode_berlaku_mulai',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $penalti = Penalti::create([
                'penalti_id' => Penalti::generatePenaltiId(),
                'karyawan_id' => $request->karyawan_id,
                'absen_id' => $request->absen_id,
                'jenis_penalti' => $request->jenis_penalti,
                'deskripsi' => $request->deskripsi,
                'hari_potong_uang_makan' => $request->hari_potong_uang_makan,
                'tanggal_penalti' => $request->tanggal_penalti,
                'periode_berlaku_mulai' => $request->periode_berlaku_mulai,
                'periode_berlaku_akhir' => $request->periode_berlaku_akhir,
                'status' => 'active',
                'created_by_user_id' => Auth::id(),
                'notes' => $request->notes,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.penalti.index')
                ->with('success', 'Penalti berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollback();

            return back()
                ->withInput()
                ->with('error', 'Gagal membuat penalti: ' . $e->getMessage());
        }
    }

    public function show(Penalti $penalti)
    {
        $penalti->load(['karyawan.user', 'absen', 'createdBy', 'approvedBy', 'tunjanganKaryawan.tunjanganType']);

        return view('admin.penalti.showPenalti', compact('penalti'));
    }

    public function edit(Penalti $penalti)
    {
        // Hanya bisa edit penalti yang masih active
        if ($penalti->status !== 'active') {
            return redirect()
                ->route('admin.penalti.index')
                ->with('error', 'Hanya penalti dengan status aktif yang dapat diedit!');
        }

        $karyawans = Karyawan::with('user')
            ->where('employment_status', 'active')
            ->get();

        $jenisOptions = ['telat', 'tidak_masuk', 'pelanggaran', 'custom'];

        return view('admin.penalti.editPenalti', compact('penalti', 'karyawans', 'jenisOptions'));
    }

    public function update(Request $request, Penalti $penalti)
    {
        // Check status
        if ($penalti->status !== 'active') {
            return back()->with('error', 'Hanya penalti dengan status aktif yang dapat diedit!');
        }

        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,karyawan_id',
            'absen_id' => 'nullable|exists:absens,absen_id',
            'jenis_penalti' => 'required|in:telat,tidak_masuk,pelanggaran,custom',
            'deskripsi' => 'required|string',
            'hari_potong_uang_makan' => 'required|integer|min:0|max:31',
            'tanggal_penalti' => 'required|date',
            'periode_berlaku_mulai' => 'required|date',
            'periode_berlaku_akhir' => 'required|date|after_or_equal:periode_berlaku_mulai',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $penalti->update([
                'karyawan_id' => $request->karyawan_id,
                'absen_id' => $request->absen_id,
                'jenis_penalti' => $request->jenis_penalti,
                'deskripsi' => $request->deskripsi,
                'hari_potong_uang_makan' => $request->hari_potong_uang_makan,
                'tanggal_penalti' => $request->tanggal_penalti,
                'periode_berlaku_mulai' => $request->periode_berlaku_mulai,
                'periode_berlaku_akhir' => $request->periode_berlaku_akhir,
                'notes' => $request->notes,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.penalti.index')
                ->with('success', 'Penalti berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollback();

            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui penalti: ' . $e->getMessage());
        }
    }

    public function destroy(Penalti $penalti)
    {
        try {
            // Check jika ada tunjangan yang terkait
            if ($penalti->tunjanganKaryawan()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus penalti yang sudah mempengaruhi tunjangan karyawan!'
                ], 422);
            }

            DB::beginTransaction();

            $penalti->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Penalti berhasil dihapus!'
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus penalti: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approve(Penalti $penalti, Request $request)
    {
        $request->validate([
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $result = $penalti->approvePenalti(Auth::id(), $request->notes);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Penalti sudah disetujui atau tidak dalam status yang tepat!'
                ], 422);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Penalti berhasil disetujui!'
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui penalti: ' . $e->getMessage()
            ], 500);
        }
    }

    public function changeStatus(Penalti $penalti, Request $request)
    {
        $request->validate([
            'status' => 'required|in:active,completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $penalti->update([
                'status' => $request->status,
                'notes' => $request->notes ? $penalti->notes . ' | ' . $request->notes : $penalti->notes,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status penalti berhasil diubah!'
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:penaltis,penalti_id'
        ]);

        try {
            DB::beginTransaction();

            $penaltis = Penalti::whereIn('penalti_id', $request->ids)->get();

            foreach ($penaltis as $penalti) {
                if ($penalti->tunjanganKaryawan()->exists()) {
                    throw new \Exception("Penalti untuk '{$penalti->karyawan->full_name}' sudah mempengaruhi tunjangan!");
                }
            }

            Penalti::whereIn('penalti_id', $request->ids)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' penalti berhasil dihapus!'
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkChangeStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:penaltis,penalti_id',
            'status' => 'required|in:active,completed,cancelled'
        ]);

        try {
            DB::beginTransaction();

            Penalti::whereIn('penalti_id', $request->ids)
                ->update(['status' => $request->status]);

            DB::commit();

            $statusText = [
                'active' => 'diaktifkan',
                'completed' => 'diselesaikan',
                'cancelled' => 'dibatalkan'
            ];

            return response()->json([
                'success' => true,
                'message' => count($request->ids) . " penalti berhasil {$statusText[$request->status]}!"
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get total hari potong untuk periode tertentu (API)
    public function getTotalHariPotongan(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,karyawan_id',
            'periode_awal' => 'required|date',
            'periode_akhir' => 'required|date|after_or_equal:periode_awal'
        ]);

        $totalHari = Penalti::getTotalHariPotongan(
            $request->karyawan_id,
            $request->periode_awal,
            $request->periode_akhir
        );

        return response()->json([
            'success' => true,
            'total_hari_potongan' => $totalHari
        ]);
    }

    // Export functionality
    public function export()
    {
        try {
            $penaltis = Penalti::with(['karyawan', 'createdBy', 'approvedBy'])->get();

            $filename = 'penalti_' . date('Y-m-d_H-i-s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($penaltis) {
                $file = fopen('php://output', 'w');

                // Header CSV
                fputcsv($file, [
                    'ID', 'Karyawan', 'Jenis Penalti', 'Deskripsi', 'Hari Potong',
                    'Tanggal Penalti', 'Periode Mulai', 'Periode Akhir', 'Status',
                    'Dibuat Oleh', 'Tanggal Dibuat'
                ]);

                // Data rows
                foreach ($penaltis as $penalti) {
                    fputcsv($file, [
                        $penalti->penalti_id,
                        $penalti->karyawan->full_name,
                        ucfirst(str_replace('_', ' ', $penalti->jenis_penalti)),
                        $penalti->deskripsi,
                        $penalti->hari_potong_uang_makan . ' hari',
                        $penalti->tanggal_penalti->format('d-m-Y'),
                        $penalti->periode_berlaku_mulai->format('d-m-Y'),
                        $penalti->periode_berlaku_akhir->format('d-m-Y'),
                        ucfirst($penalti->status),
                        $penalti->createdBy ? $penalti->createdBy->name : '-',
                        $penalti->created_at->format('d-m-Y H:i:s')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
    }
}

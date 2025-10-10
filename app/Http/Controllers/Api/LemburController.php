<?php

namespace App\Http\Controllers\Api;

use App\Models\Lembur;
use App\Models\Karyawan;
use App\Models\Absen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class LemburController extends BaseApiController
{
    /**
     * List lembur karyawan yang login
     * GET /api/lembur/my-list
     */
    public function myList(Request $request)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return $this->notFoundResponse('Data karyawan tidak ditemukan');
        }

        $status = $request->get('status');
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $perPage = $this->getPerPage($request);

        $query = Lembur::with(['absen.jadwal.shift'])
            ->where('karyawan_id', $karyawan->karyawan_id);

        if ($status) {
            $query->where('status', $status);
        }

        if ($month && $year) {
            $query->whereYear('tanggal_lembur', $year)
                  ->whereMonth('tanggal_lembur', $month);
        }

        $lemburs = $query->orderBy('tanggal_lembur', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->paginate($perPage);

        $summary = [
            'total' => Lembur::where('karyawan_id', $karyawan->karyawan_id)
                ->whereYear('tanggal_lembur', $year)
                ->whereMonth('tanggal_lembur', $month)
                ->count(),
            'draft' => Lembur::where('karyawan_id', $karyawan->karyawan_id)
                ->whereYear('tanggal_lembur', $year)
                ->whereMonth('tanggal_lembur', $month)
                ->where('status', 'draft')
                ->count(),
            'submitted' => Lembur::where('karyawan_id', $karyawan->karyawan_id)
                ->whereYear('tanggal_lembur', $year)
                ->whereMonth('tanggal_lembur', $month)
                ->where('status', 'submitted')
                ->count(),
            'approved' => Lembur::where('karyawan_id', $karyawan->karyawan_id)
                ->whereYear('tanggal_lembur', $year)
                ->whereMonth('tanggal_lembur', $month)
                ->where('status', 'approved')
                ->count(),
            'rejected' => Lembur::where('karyawan_id', $karyawan->karyawan_id)
                ->whereYear('tanggal_lembur', $year)
                ->whereMonth('tanggal_lembur', $month)
                ->where('status', 'rejected')
                ->count(),
            'total_jam' => Lembur::where('karyawan_id', $karyawan->karyawan_id)
                ->whereYear('tanggal_lembur', $year)
                ->whereMonth('tanggal_lembur', $month)
                ->approved()
                ->sum('total_jam'),
        ];

        return $this->paginatedResponse($lemburs, 'Data lembur berhasil diambil', [
            'summary' => $summary,
            'period' => [
                'month' => $month,
                'year' => $year,
                'month_name' => Carbon::createFromDate($year, $month)->format('F Y')
            ]
        ]);
    }

    /**
     * Detail lembur
     * GET /api/lembur/{id}
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;

        $lembur = Lembur::with(['absen.jadwal.shift', 'tunjanganKaryawan', 'approvedBy', 'rejectedBy'])
            ->where('lembur_id', $id)
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->first();

        if (!$lembur) {
            return $this->notFoundResponse('Data lembur tidak ditemukan');
        }

        return $this->successResponse([
            'lembur' => $lembur,
            'can_edit' => $lembur->canEdit(),
            'can_submit' => $lembur->canSubmit(),
            'can_delete' => $lembur->status === 'draft',
        ], 'Detail lembur berhasil diambil');
    }

    /**
     * Create lembur baru - TANPA kategori & multiplier
     * POST /api/lembur/submit
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return $this->notFoundResponse('Data karyawan tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
            'absen_id' => 'required|exists:absens,absen_id',
            'tanggal_lembur' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
            'deskripsi_pekerjaan' => 'required|string|max:500',
            'bukti_foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        // VALIDASI 1: Cek waktu pengajuan (max 1 jam dari habis shift)
        $validationCheck = Lembur::canSubmitLembur($request->absen_id);

        if (!$validationCheck['can_submit']) {
            return $this->errorResponse($validationCheck['message'], 422);
        }

        // VALIDASI 2: Karyawan harus sudah clock out
        $absen = Absen::find($request->absen_id);
        if (!$absen || !$absen->clock_out) {
            return $this->errorResponse(
                'Anda belum melakukan clock out. Silakan clock out terlebih dahulu sebelum mengajukan lembur.',
                422
            );
        }

        // VALIDASI 3: Cek apakah sudah ada pengajuan lembur untuk absen ini
        $existingLembur = Lembur::where('absen_id', $request->absen_id)
            ->whereIn('status', ['draft', 'submitted', 'approved', 'processed'])
            ->exists();

        if ($existingLembur) {
            return $this->errorResponse(
                'Sudah ada pengajuan lembur untuk absen ini',
                422
            );
        }

        try {
            // Upload photo
            $photoPath = null;
            if ($request->hasFile('bukti_foto')) {
                $photo = $request->file('bukti_foto');
                $filename = 'lembur_' . $karyawan->karyawan_id . '_' . time() . '.' . $photo->getClientOriginalExtension();
                $photoPath = $photo->storeAs('lembur_photos', $filename, 'public');
            }

            // Create lembur - TANPA kategori_lembur & multiplier
            $lembur = Lembur::create([
                'lembur_id' => Lembur::generateLemburId(),
                'karyawan_id' => $karyawan->karyawan_id,
                'absen_id' => $request->absen_id,
                'tanggal_lembur' => $request->tanggal_lembur,
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
                'deskripsi_pekerjaan' => $request->deskripsi_pekerjaan,
                'bukti_foto' => $photoPath,
                'status' => 'draft',
                'submitted_via' => 'mobile',
                'created_by_user_id' => $user->user_id,
            ]);

            return $this->createdResponse([
                'lembur' => $lembur->fresh(),
                'message_hint' => 'Lembur berhasil dibuat. Silakan submit untuk disetujui.'
            ], 'Lembur berhasil dibuat');

        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal menyimpan data lembur: ' . $e->getMessage());
        }
    }

    /**
     * Update lembur (hanya draft atau rejected)
     * PUT /api/lembur/{id}
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;

        $lembur = Lembur::where('lembur_id', $id)
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->first();

        if (!$lembur) {
            return $this->notFoundResponse('Data lembur tidak ditemukan');
        }

        if (!$lembur->canEdit()) {
            return $this->forbiddenResponse('Lembur dengan status ' . $lembur->status . ' tidak dapat diubah');
        }

        $validator = Validator::make($request->all(), [
            'tanggal_lembur' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
            'deskripsi_pekerjaan' => 'required|string|max:500',
            'bukti_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            // Upload foto baru jika ada
            $photoPath = $lembur->bukti_foto;
            if ($request->hasFile('bukti_foto')) {
                // Delete old photo
                if ($photoPath) {
                    Storage::disk('public')->delete($photoPath);
                }

                $photo = $request->file('bukti_foto');
                $filename = 'lembur_' . $karyawan->karyawan_id . '_' . time() . '.' . $photo->getClientOriginalExtension();
                $photoPath = $photo->storeAs('lembur_photos', $filename, 'public');
            }

            $lembur->update([
                'tanggal_lembur' => $request->tanggal_lembur,
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
                'deskripsi_pekerjaan' => $request->deskripsi_pekerjaan,
                'bukti_foto' => $photoPath,
            ]);

            return $this->successResponse(
                $lembur->fresh(),
                'Lembur berhasil diupdate'
            );

        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal update lembur: ' . $e->getMessage());
        }
    }

    /**
     * Submit lembur untuk approval
     * POST /api/lembur/{id}/submit
     */
    public function submitForApproval(Request $request, $id)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;

        $lembur = Lembur::where('lembur_id', $id)
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->first();

        if (!$lembur) {
            return $this->notFoundResponse('Data lembur tidak ditemukan');
        }

        if (!$lembur->canSubmit()) {
            return $this->forbiddenResponse('Lembur dengan status ' . $lembur->status . ' tidak dapat disubmit');
        }

        try {
            $lembur->submit('mobile');

            return $this->successResponse(
                $lembur->fresh(),
                'Lembur berhasil disubmit. Menunggu persetujuan.'
            );

        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal submit lembur: ' . $e->getMessage());
        }
    }

    /**
     * Delete lembur (hanya draft)
     * DELETE /api/lembur/{id}
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;

        $lembur = Lembur::where('lembur_id', $id)
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->first();

        if (!$lembur) {
            return $this->notFoundResponse('Data lembur tidak ditemukan');
        }

        if ($lembur->status !== 'draft') {
            return $this->forbiddenResponse('Hanya lembur dengan status draft yang dapat dihapus');
        }

        try {
            // Delete foto if exists
            if ($lembur->bukti_foto) {
                Storage::disk('public')->delete($lembur->bukti_foto);
            }

            $lembur->delete();

            return $this->successResponse(null, 'Lembur berhasil dihapus');

        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal menghapus lembur: ' . $e->getMessage());
        }
    }

    /**
     * Get summary lembur karyawan
     * GET /api/lembur/summary
     */
    public function summary(Request $request)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return $this->notFoundResponse('Data karyawan tidak ditemukan');
        }

        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $lemburs = Lembur::where('karyawan_id', $karyawan->karyawan_id)
            ->whereYear('tanggal_lembur', $year)
            ->whereMonth('tanggal_lembur', $month)
            ->get();

        $summary = [
            'total_lembur' => $lemburs->count(),
            'draft' => $lemburs->where('status', 'draft')->count(),
            'submitted' => $lemburs->where('status', 'submitted')->count(),
            'approved' => $lemburs->where('status', 'approved')->count(),
            'rejected' => $lemburs->where('status', 'rejected')->count(),
            'processed' => $lemburs->where('status', 'processed')->count(),
            'total_jam_approved' => $lemburs->whereIn('status', ['approved', 'processed'])->sum('total_jam'),
        ];

        return $this->successResponse([
            'summary' => $summary,
            'period' => [
                'month' => $month,
                'year' => $year,
                'month_name' => Carbon::createFromDate($year, $month)->format('F Y')
            ]
        ], 'Summary lembur berhasil diambil');
    }
}

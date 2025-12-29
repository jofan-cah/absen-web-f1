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

        $query = Lembur::with(['absen.jadwal.shift', 'coordinator'])
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

        $baseQuery = Lembur::where('karyawan_id', $karyawan->karyawan_id)
            ->whereYear('tanggal_lembur', $year)
            ->whereMonth('tanggal_lembur', $month);

        $summary = [
            'total' => (clone $baseQuery)->count(),
            'draft' => (clone $baseQuery)->where('status', 'draft')->count(),
            'submitted' => (clone $baseQuery)->where('status', 'submitted')->count(),
            'pending_koordinator' => (clone $baseQuery)->where('status', 'submitted')->where('koordinator_status', 'pending')->count(),
            'pending_admin' => (clone $baseQuery)->where('status', 'submitted')->where('koordinator_status', 'approved')->count(),
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
            'total_jam' => (clone $baseQuery)->approved()->sum('total_jam'),
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

        $lembur = Lembur::with([
            'absen.jadwal.shift',
            'tunjanganKaryawan',
            'approvedBy',
            'rejectedBy',
            'coordinator'
        ])
            ->where('lembur_id', $id)
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->first();

        if (!$lembur) {
            return $this->notFoundResponse('Data lembur tidak ditemukan');
        }

        // ✅ OPTION 1: Tambahkan bukti_foto_url ke array lembur
        $lemburArray = $lembur->toArray();
        $lemburArray['bukti_foto_url'] = $lembur->bukti_foto_url;

        return $this->successResponse([
            'lembur' => $lemburArray, // ✅ Sekarang bukti_foto_url ada di dalam
            'can_edit' => $lembur->canEdit(),
            'can_submit' => $lembur->canSubmit(),
            'can_delete' => $lembur->status === 'draft',
            'can_finish' => $lembur->status === 'draft' && $lembur->started_at && !$lembur->completed_at,
            'is_in_progress' => $lembur->started_at && !$lembur->completed_at,
            'koordinator_status' => $lembur->koordinator_status,
            'koordinator_info' => $lembur->coordinator ? [
                'name' => $lembur->coordinator->full_name ?? $lembur->coordinator->user->name ?? null,
                'approved_at' => $lembur->koordinator_approved_at?->format('Y-m-d H:i:s'),
                'notes' => $lembur->koordinator_notes,
            ] : null,
            'tracking' => [
                'started_at' => $lembur->started_at?->format('Y-m-d H:i:s'),
                'completed_at' => $lembur->completed_at?->format('Y-m-d H:i:s'),
                'duration_minutes' => $lembur->started_at && $lembur->completed_at
                    ? $lembur->started_at->diffInMinutes($lembur->completed_at)
                    : null,
            ],
        ], 'Detail lembur berhasil diambil');
    }

    /**
     * 🆕 Mulai lembur - buat draft dengan started_at
     * POST /api/lembur/start
     *
     * ✅ UPDATED: Support OnCall - skip validasi +1 jam
     */
    public function start(Request $request)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return $this->notFoundResponse('Data karyawan tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
            'absen_id' => 'required|exists:absens,absen_id',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        // Get absen data
        $absen = Absen::with('jadwal.shift')->find($request->absen_id);

        // VALIDASI 1: Karyawan harus sudah clock out
        if (!$absen || !$absen->clock_out) {
            return $this->errorResponse(
                'Anda belum melakukan clock out. Silakan clock out terlebih dahulu sebelum memulai lembur.',
                422
            );
        }

        // VALIDASI 2: Absen harus milik karyawan yang login
        if ($absen->karyawan_id !== $karyawan->karyawan_id) {
            return $this->forbiddenResponse('Absensi bukan milik Anda');
        }

        // VALIDASI 3: Cek apakah sudah ada pengajuan lembur untuk absen ini
        $existingLembur = Lembur::where('absen_id', $request->absen_id)
            ->whereIn('status', ['draft', 'submitted', 'approved', 'processed'])
            ->first();

        if ($existingLembur) {
            return $this->errorResponse(
                'Sudah ada pengajuan lembur untuk absen ini',
                422
            );
        }

        // Get shift end time
        if (!$absen->jadwal || !$absen->jadwal->shift) {
            return $this->errorResponse('Data jadwal shift tidak ditemukan', 404);
        }

        $shift = $absen->jadwal->shift;
        $shiftEnd = $shift->end_time;

        // ✅ CEK APAKAH INI ONCALL
        $isOnCall = $absen->type === 'oncall';

        // ✅ VALIDASI +1 JAM - SKIP UNTUK ONCALL!
        if (!$isOnCall) {
            // HANYA VALIDASI UNTUK LEMBUR REQUEST (bukan OnCall)
            $shiftEndCarbon = Carbon::createFromFormat('H:i:s', $shiftEnd);
            $maxStartTime = $shiftEndCarbon->copy()->addHour(); // Maksimal 1 jam dari shift end
            $now = Carbon::now();

            // Cek apakah pengajuan masih dalam waktu +1 jam dari shift end
            $tanggalAbsen = Carbon::parse($absen->date);
            $maxStartDateTime = $tanggalAbsen->copy()->setTimeFromTimeString($maxStartTime->format('H:i:s'));

            if ($now->greaterThan($maxStartDateTime)) {
                return $this->errorResponse(
                    "Waktu pengajuan lembur sudah melewati batas maksimal (shift end + 1 jam). Batas: {$maxStartDateTime->format('d/m/Y H:i')}",
                    422
                );
            }
        }
        // ✅ ONCALL: SKIP VALIDASI WAKTU! Bebas kapan aja

        try {
            // ✅ Determine jenis_lembur based on absen type
            $jenisLembur = $isOnCall ? 'oncall' : 'regular';

            // Create lembur draft dengan started_at
            $lembur = Lembur::create([
                'lembur_id' => Lembur::generateLemburId(),
                'karyawan_id' => $karyawan->karyawan_id,
                'absen_id' => $request->absen_id,
                'tanggal_lembur' => $absen->date->format('Y-m-d'),
                'jam_mulai' => $shiftEnd, // Otomatis dari shift_end
                'jam_selesai' => null, // Belum diisi
                'deskripsi_pekerjaan' => null,
                'bukti_foto' => null,
                'jenis_lembur' => $jenisLembur, // ✅ 'oncall' atau 'request'
                'status' => 'draft',
                'koordinator_status' => 'pending',
                'submitted_via' => 'mobile',
                'started_at' => now(), // Timestamp mulai
                'completed_at' => null, // Belum selesai
                'created_by_user_id' => $user->user_id,

                // ✅ ONCALL: Link ke jadwal OnCall
                'oncall_jadwal_id' => $isOnCall ? $absen->jadwal_id : null,
            ]);

            // ✅ Custom message based on type
            $messageHint = $isOnCall
                ? 'OnCall dimulai. Klik "Selesai Lembur" setelah pekerjaan selesai.'
                : 'Lembur dimulai. Klik "Selesai Lembur" setelah pekerjaan selesai.';

            return $this->createdResponse([
                'lembur' => $lembur->fresh(['absen.jadwal.shift']),
                'shift_info' => [
                    'shift_name' => $shift->name,
                    'shift_end' => substr($shiftEnd, 0, 5),
                    'is_oncall' => $isOnCall, // ✅ Info tambahan
                    'jenis_lembur' => $jenisLembur, // ✅ Info tambahan
                ],
                'message_hint' => $messageHint
            ], 'Lembur berhasil dimulai');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal memulai lembur: ' . $e->getMessage());
        }
    }

    /**
     * 🆕 Selesai lembur - update jam selesai, deskripsi, dan bukti foto
     * POST /api/lembur/{id}/finish
     *
     * ✅ TIDAK ADA VALIDASI +1 JAM - Fleksibel input jam selesai kapan saja
     */
    public function finish(Request $request, $id)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;

        $lembur = Lembur::with('absen.jadwal.shift')
            ->where('lembur_id', $id)
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->first();

        if (!$lembur) {
            return $this->notFoundResponse('Data lembur tidak ditemukan');
        }

        // VALIDASI 1: Harus status draft
        if ($lembur->status !== 'draft') {
            return $this->forbiddenResponse('Hanya lembur dengan status draft yang dapat diselesaikan');
        }

        // VALIDASI 2: Harus sudah started
        if (!$lembur->started_at) {
            return $this->errorResponse('Lembur belum dimulai', 422);
        }

        // VALIDASI 3: Belum completed
        if ($lembur->completed_at) {
            return $this->errorResponse('Lembur sudah diselesaikan sebelumnya', 422);
        }

        $validator = Validator::make($request->all(), [
            'jam_selesai' => 'nullable|date_format:H:i', // Optional, default NOW
            'deskripsi_pekerjaan' => 'required|string|max:500',
            'bukti_foto' => 'required|image|mimes:jpeg,png,jpg',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        try {
            // Get shift info
            if (!$lembur->absen->jadwal || !$lembur->absen->jadwal->shift) {
                return $this->errorResponse('Data jadwal shift tidak ditemukan', 404);
            }

            $shiftEnd = $lembur->absen->jadwal->shift->end_time;
            $shiftEndCarbon = Carbon::createFromFormat('H:i:s', $shiftEnd);

            // Jam selesai: dari input atau NOW
            if ($request->filled('jam_selesai')) {
                $jamSelesai = $request->jam_selesai;
            } else {
                // Default: gunakan waktu sekarang
                $jamSelesai = now()->format('H:i');
            }

            $jamSelesaiCarbon = Carbon::createFromFormat('H:i', $jamSelesai);

            // ✅ CEK APAKAH INI ONCALL
            $isOnCall = $lembur->jenis_lembur === 'oncall';

            // ✅ VALIDASI JAM SELESAI - SKIP UNTUK ONCALL!
            if (!$isOnCall) {
                // HANYA VALIDASI UNTUK LEMBUR REQUEST (bukan OnCall)
                // Jam selesai harus lebih besar dari shift end
                if ($jamSelesaiCarbon->lessThanOrEqualTo($shiftEndCarbon)) {
                    return $this->errorResponse(
                        "Jam selesai lembur harus lebih dari jam shift berakhir (" . substr($shiftEnd, 0, 5) . ")",
                        422
                    );
                }
            }
            // ✅ ONCALL: SKIP VALIDASI! Bebas jam berapa aja (karena bisa lewat tengah malam)

            // Upload bukti foto
            $photoPath = null;
            if ($request->hasFile('bukti_foto')) {
                $photo = $request->file('bukti_foto');
                $filename = 'lembur/' . $karyawan->karyawan_id . '/' . time() . '.' . $photo->getClientOriginalExtension();
                $photoPath = Storage::disk('s3')->putFileAs('', $photo, $filename, 'private');
            }

            // Update lembur
            $lembur->update([
                'jam_selesai' => $jamSelesai . ':00',
                'deskripsi_pekerjaan' => $request->deskripsi_pekerjaan,
                'bukti_foto' => $photoPath,
                'completed_at' => now(), // Timestamp selesai
            ]);

            return $this->successResponse([
                'lembur' => $lembur->fresh(['absen.jadwal.shift']),
                'duration_info' => [
                    'started_at' => $lembur->started_at->format('H:i'),
                    'completed_at' => $lembur->completed_at->format('H:i'),
                    'duration_minutes' => $lembur->started_at->diffInMinutes($lembur->completed_at),
                    'duration_hours' => round($lembur->started_at->diffInMinutes($lembur->completed_at) / 60, 2),
                ],
                'message_hint' => 'Lembur selesai. Silakan submit untuk approval.'
            ], 'Lembur berhasil diselesaikan');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal menyelesaikan lembur: ' . $e->getMessage());
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

        $lembur = Lembur::with('absen.jadwal.shift')
            ->where('lembur_id', $id)
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
            'jam_selesai' => 'required|date_format:H:i',
            'deskripsi_pekerjaan' => 'required|string|max:500',
            'bukti_foto' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        // VALIDASI: Jam selesai harus lebih besar dari shift end
        if (!$lembur->absen->jadwal || !$lembur->absen->jadwal->shift) {
            return $this->errorResponse('Data jadwal shift tidak ditemukan', 404);
        }

        $shiftEnd = $lembur->absen->jadwal->shift->end_time;
        $shiftEndCarbon = Carbon::createFromFormat('H:i:s', $shiftEnd);
        $jamSelesaiCarbon = Carbon::createFromFormat('H:i', $request->jam_selesai);

        if ($jamSelesaiCarbon->lessThanOrEqualTo($shiftEndCarbon)) {
            return $this->errorResponse(
                "Jam selesai lembur harus lebih dari jam shift berakhir (" . substr($shiftEnd, 0, 5) . ")",
                422
            );
        }

        try {
            // Upload foto baru jika ada
            $photoPath = $lembur->bukti_foto;
            if ($request->hasFile('bukti_foto')) {
                // Delete old photo
                if ($photoPath) {
                    Storage::disk('s3')->delete($photoPath);
                }

                $photo = $request->file('bukti_foto');
                $filename = 'lembur/' . $karyawan->karyawan_id . '/' . time() . '.' . $photo->getClientOriginalExtension();
                $photoPath = Storage::disk('s3')->putFileAs('', $photo, $filename, 'private');
            }

            $lembur->update([
                'tanggal_lembur' => $request->tanggal_lembur,
                'jam_selesai' => $request->jam_selesai,
                'deskripsi_pekerjaan' => $request->deskripsi_pekerjaan,
                'bukti_foto' => $photoPath,
            ]);

            return $this->successResponse(
                $lembur->fresh(['absen.jadwal.shift']),
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

        // VALIDASI: Harus sudah completed (ada jam_selesai, deskripsi, bukti foto)
        if (!$lembur->jam_selesai || !$lembur->deskripsi_pekerjaan || !$lembur->bukti_foto) {
            return $this->errorResponse(
                'Lembur belum lengkap. Pastikan sudah mengisi jam selesai, deskripsi pekerjaan, dan upload bukti foto.',
                422
            );
        }

        try {
            $lembur->submit('mobile');

            return $this->successResponse(
                $lembur->fresh(),
                'Lembur berhasil disubmit. Menunggu persetujuan Koordinator.'
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
                Storage::disk('s3')->delete($lembur->bukti_foto);
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
            'in_progress' => $lemburs->where('status', 'draft')
                ->filter(fn($l) => $l->started_at && !$l->completed_at)
                ->count(),
            'submitted' => $lemburs->where('status', 'submitted')->count(),
            'pending_koordinator' => $lemburs->where('status', 'submitted')->where('koordinator_status', 'pending')->count(),
            'pending_admin' => $lemburs->where('status', 'submitted')->where('koordinator_status', 'approved')->count(),
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

    /**
     * Get info untuk form lembur (shift end, max start time)
     * GET /api/lembur/form-info/{absenId}
     *
     * ✅ UPDATED: Support OnCall - skip validasi +1 jam
     */
    public function getFormInfo($absenId)
    {
        $user = request()->user();
        $karyawan = $user->karyawan;

        $absen = Absen::with(['jadwal.shift'])->find($absenId);

        if (!$absen) {
            return $this->notFoundResponse('Data absen tidak ditemukan');
        }

        if ($absen->karyawan_id !== $karyawan->karyawan_id) {
            return $this->forbiddenResponse('Anda tidak memiliki akses ke absen ini');
        }

        if (!$absen->clock_out) {
            return $this->errorResponse('Anda belum melakukan clock out', 422);
        }

        // Get shift info
        if (!$absen->jadwal || !$absen->jadwal->shift) {
            return $this->notFoundResponse('Data jadwal shift tidak ditemukan');
        }

        $shift = $absen->jadwal->shift;
        $shiftEnd = $shift->end_time;
        $shiftEndCarbon = Carbon::createFromFormat('H:i:s', $shiftEnd);
        $maxStartTime = $shiftEndCarbon->copy()->addHour(); // Maksimal +1 jam untuk START

        // Cek existing lembur
        $existingLembur = Lembur::where('absen_id', $absen->absen_id)
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->whereIn('status', ['draft', 'submitted', 'approved'])
            ->first();

        // ✅ CEK APAKAH INI ONCALL
        $isOnCall = $absen->type === 'oncall';

        // Cek apakah masih dalam waktu pengajuan (+1 jam dari shift end)
        $tanggalAbsen = Carbon::parse($absen->date);
        $maxStartDateTime = $tanggalAbsen->copy()->setTimeFromTimeString($maxStartTime->format('H:i:s'));
        $now = Carbon::now();

        // ✅ ONCALL: SKIP VALIDASI WAKTU! (selalu bisa start)
        $canStart = $isOnCall ? true : $now->lessThanOrEqualTo($maxStartDateTime);

        // ✅ ONCALL: Custom message
        $infoMessage = '';
        if ($isOnCall) {
            $infoMessage = "✅ OnCall - Tidak ada batasan waktu mulai lembur";
        } else {
            $infoMessage = $canStart
                ? "✅ Anda dapat memulai lembur hingga " . $maxStartDateTime->format('d/m/Y H:i')
                : "⚠️ Waktu pengajuan lembur sudah melewati batas (shift end + 1 jam)";
        }

        return $this->successResponse([
            'can_create_lembur' => !$existingLembur && $canStart,
            'has_existing_lembur' => (bool) $existingLembur,
            'existing_lembur_id' => $existingLembur->lembur_id ?? null,
            'can_start' => $canStart, // ✅ OnCall selalu true
            'is_oncall' => $isOnCall, // ✅ Tambahan info
            'max_start_datetime' => $isOnCall ? null : $maxStartDateTime->format('Y-m-d H:i:s'), // ✅ OnCall: null
            'shift_name' => $shift->name,
            'shift_start' => substr($shift->start_time, 0, 5),
            'shift_end' => substr($shiftEnd, 0, 5),
            'jam_mulai_lembur' => substr($shiftEnd, 0, 5),
            'clock_in' => $absen->clock_in,
            'clock_out' => $absen->clock_out,
            'work_hours' => $absen->work_hours,
            'info_message' => $infoMessage, // ✅ Custom message
        ], 'Info form lembur berhasil diambil');
    }


 /**
 * Update foto lembur dan langsung submit untuk approval
 * POST /api/lembur/{id}/update-photo
 */
public function updatePhoto(Request $request, $id)
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
        return $this->forbiddenResponse('Hanya lembur draft yang dapat diupdate fotonya');
    }

    $validator = Validator::make($request->all(), [
        'bukti_foto' => 'required|image|mimes:jpeg,png,jpg',
    ]);

    if ($validator->fails()) {
        return $this->validationErrorResponse($validator->errors());
    }

    try {
        // Upload foto baru
        if ($request->hasFile('bukti_foto')) {
            // Delete old photo
            if ($lembur->bukti_foto) {
                Storage::disk('s3')->delete($lembur->bukti_foto);
            }

            $photo = $request->file('bukti_foto');
            $filename = 'lembur/' . $karyawan->karyawan_id . '/' . time() . '.' . $photo->getClientOriginalExtension();
            $photoPath = Storage::disk('s3')->putFileAs('', $photo, $filename, 'private');
        }

        // ✅ UPDATE foto + langsung SUBMIT
        $lembur->update([
            'bukti_foto' => $photoPath,
            'status' => 'submitted',
            'submitted_at' => now(),
            'submitted_via' => 'mobile',
        ]);

        return $this->successResponse(
            $lembur->fresh(['absen.jadwal.shift']),
            'Foto berhasil diupdate dan otomatis disubmit untuk approval'
        );
    } catch (\Exception $e) {
        return $this->serverErrorResponse('Gagal update foto: ' . $e->getMessage());
    }
}
}

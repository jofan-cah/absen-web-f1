<?php

namespace App\Http\Controllers\Api;

use App\Models\Absen;
use App\Models\Jadwal;
use App\Models\Lembur;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * OncallController
 *
 * API Controller khusus untuk OnCall Lembur
 * Screen terpisah dari Absen biasa di Mobile
 *
 * Flow:
 * 1. GET  /api/oncall/today      - Cek jadwal oncall hari ini
 * 2. POST /api/oncall/clock-in   - Clock in (foto + lokasi) → Absen + Create Lembur
 * 3. PUT  /api/oncall/{id}/report - Isi laporan lembur → Update Lembur
 * 4. POST /api/oncall/clock-out  - Clock out (foto + lokasi) → Absen + Lembur → status 'submitted'
 * 5. GET  /api/oncall/{id}       - Detail oncall
 * 6. GET  /api/oncall/my-list    - List oncall saya
 */
class OncallController extends BaseApiController
{
    /**
     * Cek jadwal OnCall hari ini
     * GET /api/oncall/today
     */
    public function today(Request $request)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;
        $today = Carbon::today();

        if (!$karyawan) {
            return $this->notFoundResponse('Data karyawan tidak ditemukan');
        }

        // Cari jadwal OnCall hari ini
        $jadwalOncall = Jadwal::with(['shift', 'absen'])
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->where('type', 'oncall')
            ->whereDate('date', $today)
            ->first();

        if (!$jadwalOncall) {
            return $this->successResponse([
                'has_oncall' => false,
                'jadwal' => null,
                'absen' => null,
                'lembur' => null,
                'message' => 'Tidak ada jadwal OnCall untuk hari ini'
            ], 'Status OnCall hari ini');
        }

        $absen = $jadwalOncall->absen;

        // Cari lembur yang terkait
        $lembur = Lembur::where('oncall_jadwal_id', $jadwalOncall->jadwal_id)
            ->where('jenis_lembur', 'oncall')
            ->first();

        // Determine actions
        $canClockIn = $absen && !$absen->clock_in;
        $canClockOut = $absen && $absen->clock_in && !$absen->clock_out;
        $canFillReport = $lembur && $absen && $absen->clock_in;

        // Status flow
        $status = 'waiting_clock_in';
        if ($absen && $absen->clock_in && !$absen->clock_out) {
            $status = 'in_progress';
        } elseif ($absen && $absen->clock_out) {
            if ($lembur) {
                $status = $lembur->status; // submitted, approved, rejected
            } else {
                $status = 'completed';
            }
        }

        return $this->successResponse([
            'has_oncall' => true,
            'jadwal' => $jadwalOncall,
            'absen' => $absen,
            'lembur' => $lembur,
            'status' => $status,
            'actions' => [
                'can_clock_in' => $canClockIn,
                'can_clock_out' => $canClockOut,
                'can_fill_report' => $canFillReport,
            ],
            'shift_info' => $jadwalOncall->shift ? [
                'name' => $jadwalOncall->shift->name,
                'start_time' => substr($jadwalOncall->shift->start_time, 0, 5),
                'end_time' => substr($jadwalOncall->shift->end_time, 0, 5),
            ] : null,
        ], 'Status OnCall hari ini');
    }

    /**
     * Clock In OnCall
     * POST /api/oncall/clock-in
     *
     * → Update Absen.clock_in
     * → Create/Update Lembur
     */
    public function clockIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address' => 'required|string',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $user = $request->user();
        $karyawan = $user->karyawan;
        $today = Carbon::today();
        $now = Carbon::now();

        if (!$karyawan) {
            return $this->notFoundResponse('Data karyawan tidak ditemukan');
        }

        // Cari jadwal OnCall hari ini
        $jadwalOncall = Jadwal::with(['shift', 'absen'])
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->where('type', 'oncall')
            ->whereDate('date', $today)
            ->first();

        if (!$jadwalOncall) {
            return $this->notFoundResponse('Tidak ada jadwal OnCall untuk hari ini');
        }

        $absen = $jadwalOncall->absen;

        if (!$absen) {
            return $this->notFoundResponse('Data absen OnCall tidak ditemukan');
        }

        if ($absen->clock_in) {
            return $this->errorResponse('Sudah melakukan clock in OnCall hari ini', 400);
        }

        // Upload photo ke S3
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = 'oncall_clock_in_' . $karyawan->karyawan_id . '_' . $today->format('Y-m-d') . '_' . time() . '.' . $photo->getClientOriginalExtension();
            $photoPath = Storage::disk('s3')->putFileAs('absen_photos', $photo, $filename);
        }

        // Update Absen
        $absen->update([
            'clock_in' => $now->format('H:i:s'),
            'clock_in_photo' => $photoPath,
            'clock_in_latitude' => $request->latitude,
            'clock_in_longitude' => $request->longitude,
            'clock_in_address' => $request->address,
            'late_minutes' => 0,
            'status' => 'present',
        ]);

        // Cek/Create Lembur
        $lembur = Lembur::where('oncall_jadwal_id', $jadwalOncall->jadwal_id)
            ->where('jenis_lembur', 'oncall')
            ->first();

        if ($lembur) {
            $lembur->update([
                'absen_id' => $absen->absen_id,
                'started_at' => $now,
                'jam_mulai' => $now->format('H:i:s'),
            ]);
        } else {
            $lembur = Lembur::create([
                'lembur_id' => Lembur::generateLemburId(),
                'karyawan_id' => $karyawan->karyawan_id,
                'absen_id' => $absen->absen_id,
                'oncall_jadwal_id' => $jadwalOncall->jadwal_id,
                'tanggal_lembur' => $today->format('Y-m-d'),
                'jenis_lembur' => 'oncall',
                'jam_mulai' => $now->format('H:i:s'),
                'status' => 'draft',
                'koordinator_status' => 'pending',
                'started_at' => $now,
                'submitted_via' => 'mobile',
                'created_by_user_id' => $user->user_id,
            ]);
        }

        Log::info("OnCall Clock In", [
            'karyawan_id' => $karyawan->karyawan_id,
            'lembur_id' => $lembur->lembur_id,
        ]);

        return $this->successResponse([
            'absen' => $absen->fresh(),
            'lembur' => $lembur->fresh(),
            'clock_in_time' => $now->format('H:i:s'),
        ], 'Clock in OnCall berhasil');
    }

    /**
     * Isi/Update Laporan Lembur
     * PUT /api/oncall/{id}/report
     */
    public function updateReport(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'deskripsi_pekerjaan' => 'required|string|max:1000',
            'bukti_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $user = $request->user();
        $karyawan = $user->karyawan;

        $lembur = Lembur::where('lembur_id', $id)
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->where('jenis_lembur', 'oncall')
            ->first();

        if (!$lembur) {
            return $this->notFoundResponse('Data lembur OnCall tidak ditemukan');
        }

        // Upload bukti foto jika ada
        $photoPath = $lembur->bukti_foto;
        if ($request->hasFile('bukti_foto')) {
            if ($photoPath) {
                Storage::disk('s3')->delete($photoPath);
            }
            $photo = $request->file('bukti_foto');
            $filename = 'lembur/' . $karyawan->karyawan_id . '/oncall_' . time() . '.' . $photo->getClientOriginalExtension();
            $photoPath = Storage::disk('s3')->putFileAs('', $photo, $filename, 'private');
        }

        $lembur->update([
            'deskripsi_pekerjaan' => $request->deskripsi_pekerjaan,
            'bukti_foto' => $photoPath,
        ]);

        return $this->successResponse([
            'lembur' => $lembur->fresh(),
        ], 'Laporan OnCall berhasil disimpan');
    }

    /**
     * Clock Out OnCall
     * POST /api/oncall/clock-out
     *
     * → Update Absen.clock_out
     * → Update Lembur (jam_selesai, total_jam)
     * → Status langsung 'submitted'
     */
    public function clockOut(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address' => 'required|string',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            // Laporan bisa diisi saat clock out
            'deskripsi_pekerjaan' => 'required|string|max:1000',
            'bukti_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $user = $request->user();
        $karyawan = $user->karyawan;
        $today = Carbon::today();
        $now = Carbon::now();

        if (!$karyawan) {
            return $this->notFoundResponse('Data karyawan tidak ditemukan');
        }

        // Cari jadwal OnCall hari ini
        $jadwalOncall = Jadwal::with(['shift', 'absen'])
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->where('type', 'oncall')
            ->whereDate('date', $today)
            ->first();

        if (!$jadwalOncall) {
            return $this->notFoundResponse('Tidak ada jadwal OnCall untuk hari ini');
        }

        $absen = $jadwalOncall->absen;

        if (!$absen || !$absen->clock_in) {
            return $this->errorResponse('Belum melakukan clock in OnCall', 400);
        }

        if ($absen->clock_out) {
            return $this->errorResponse('Sudah melakukan clock out OnCall', 400);
        }

        // Upload photo clock out ke S3
        $clockOutPhotoPath = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = 'oncall_clock_out_' . $karyawan->karyawan_id . '_' . $today->format('Y-m-d') . '_' . time() . '.' . $photo->getClientOriginalExtension();
            $clockOutPhotoPath = Storage::disk('s3')->putFileAs('absen_photos', $photo, $filename);
        }

        // Calculate work hours
        $clockIn = Carbon::parse($absen->clock_in);
        $workMinutes = $now->diffInMinutes($clockIn);
        $workHours = round($workMinutes / 60, 2);

        // Update Absen
        $absen->update([
            'clock_out' => $now->format('H:i:s'),
            'clock_out_photo' => $clockOutPhotoPath,
            'clock_out_latitude' => $request->latitude,
            'clock_out_longitude' => $request->longitude,
            'clock_out_address' => $request->address,
            'early_checkout_minutes' => 0,
            'work_hours' => $workHours,
        ]);

        // Upload bukti foto lembur jika ada
        $buktiLemburPath = null;
        if ($request->hasFile('bukti_foto')) {
            $buktiFoto = $request->file('bukti_foto');
            $filename = 'lembur/' . $karyawan->karyawan_id . '/oncall_bukti_' . time() . '.' . $buktiFoto->getClientOriginalExtension();
            $buktiLemburPath = Storage::disk('s3')->putFileAs('', $buktiFoto, $filename, 'private');
        }

        // Update Lembur → langsung SUBMITTED
        $lembur = Lembur::where('oncall_jadwal_id', $jadwalOncall->jadwal_id)
            ->where('jenis_lembur', 'oncall')
            ->first();

        if ($lembur) {
            $updateData = [
                'jam_selesai' => $now->format('H:i:s'),
                'total_jam' => $workHours,
                'completed_at' => $now,
                'deskripsi_pekerjaan' => $request->deskripsi_pekerjaan,
                'status' => 'submitted', // Langsung submitted!
                'koordinator_status' => 'pending',
                'submitted_at' => $now,
                'submitted_via' => 'mobile',
            ];

            if ($buktiLemburPath) {
                // Delete old photo if exists
                if ($lembur->bukti_foto) {
                    Storage::disk('s3')->delete($lembur->bukti_foto);
                }
                $updateData['bukti_foto'] = $buktiLemburPath;
            }

            $lembur->update($updateData);
        }

        Log::info("OnCall Clock Out + Submitted", [
            'karyawan_id' => $karyawan->karyawan_id,
            'lembur_id' => $lembur->lembur_id ?? null,
            'work_hours' => $workHours,
        ]);

        return $this->successResponse([
            'absen' => $absen->fresh(),
            'lembur' => $lembur ? $lembur->fresh() : null,
            'clock_out_time' => $now->format('H:i:s'),
            'work_hours' => $workHours,
        ], 'Clock out OnCall berhasil. Lembur sudah disubmit untuk approval.');
    }

    /**
     * Detail OnCall
     * GET /api/oncall/{id}
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;

        $lembur = Lembur::with([
            'absen.jadwal.shift',
            'jadwalOnCall.shift',
            'tunjanganKaryawan',
            'approvedBy',
            'rejectedBy',
        ])
            ->where('lembur_id', $id)
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->where('jenis_lembur', 'oncall')
            ->first();

        if (!$lembur) {
            return $this->notFoundResponse('Data OnCall tidak ditemukan');
        }

        $lemburArray = $lembur->toArray();
        $lemburArray['bukti_foto_url'] = $lembur->bukti_foto_url;

        if ($lembur->absen) {
            $lemburArray['absen']['clock_in_photo_url'] = $lembur->absen->clock_in_photo_url;
            $lemburArray['absen']['clock_out_photo_url'] = $lembur->absen->clock_out_photo_url;
        }

        return $this->successResponse([
            'lembur' => $lemburArray,
            'tracking' => [
                'clock_in' => $lembur->absen->clock_in ?? null,
                'clock_out' => $lembur->absen->clock_out ?? null,
                'started_at' => $lembur->started_at?->format('Y-m-d H:i:s'),
                'completed_at' => $lembur->completed_at?->format('Y-m-d H:i:s'),
                'submitted_at' => $lembur->submitted_at?->format('Y-m-d H:i:s'),
                'approved_at' => $lembur->approved_at?->format('Y-m-d H:i:s'),
            ],
            'tunjangan_info' => $lembur->hasTunjangan() ? [
                'status' => $lembur->getTunjanganStatus(),
                'amount' => $lembur->tunjanganKaryawan->total_amount ?? 0,
            ] : null,
        ], 'Detail OnCall berhasil diambil');
    }

    /**
     * List OnCall
     * GET /api/oncall/my-list
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

        $query = Lembur::with(['absen.jadwal.shift', 'jadwalOnCall.shift'])
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->where('jenis_lembur', 'oncall');

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

        // Summary
        $baseQuery = Lembur::where('karyawan_id', $karyawan->karyawan_id)
            ->where('jenis_lembur', 'oncall')
            ->whereYear('tanggal_lembur', $year)
            ->whereMonth('tanggal_lembur', $month);

        $summary = [
            'total' => (clone $baseQuery)->count(),
            'draft' => (clone $baseQuery)->where('status', 'draft')->count(),
            'submitted' => (clone $baseQuery)->where('status', 'submitted')->count(),
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
            'total_jam' => (clone $baseQuery)->approved()->sum('total_jam'),
        ];

        return $this->paginatedResponse($lemburs, 'Data OnCall berhasil diambil', [
            'summary' => $summary,
            'period' => [
                'month' => $month,
                'year' => $year,
                'month_name' => Carbon::createFromDate($year, $month)->format('F Y'),
            ],
        ]);
    }
}

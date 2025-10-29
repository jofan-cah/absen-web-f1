<?php

namespace App\Http\Controllers\Api;

use App\Models\Absen;
use App\Models\Ijin;
use App\Models\Jadwal;
use App\Models\Lembur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AbsenController extends BaseApiController
{
    public function today(Request $request)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;
        $today = Carbon::today();

        if (!$karyawan) {
            return $this->notFoundResponse('Data karyawan tidak ditemukan');
        }

        $jadwal = Jadwal::with('shift')
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->whereDate('date', $today)
            ->first();

        if (!$jadwal) {
            return $this->notFoundResponse('Tidak ada jadwal untuk hari ini');
        }

        $absen = Absen::where('jadwal_id', $jadwal->jadwal_id)->first();

        return $this->successResponse([
            'has_jadwal' => true,
            'jadwal' => $jadwal,
            'absen' => $absen,
            'can_clock_in' => !$absen || !$absen->clock_in,
            'can_clock_out' => $absen && $absen->clock_in && !$absen->clock_out
        ], 'Status absen hari ini');
    }

    public function clockIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address' => 'required|string',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $user = $request->user();
        $karyawan = $user->karyawan;
        $today = Carbon::today();
        $now = Carbon::now();

        // ✅ CEK IJIN APPROVED
        $approvedIjin = Ijin::where('karyawan_id', $karyawan->karyawan_id)
            ->where('status', 'approved')
            ->where('date_from', '<=', $today)
            ->where('date_to', '>=', $today)
            ->first();

        if ($approvedIjin) {
            return $this->errorResponse(
                'Anda memiliki ijin yang sudah disetujui untuk hari ini (' . $approvedIjin->ijinType->name . '). Tidak dapat melakukan absensi.',
                403
            );
        }

        // Cari jadwal hari ini
        $jadwal = Jadwal::with('shift')
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->whereDate('date', $today)
            ->first();

        if (!$jadwal) {
            return $this->notFoundResponse('Tidak ada jadwal untuk hari ini');
        }

        // Cari atau buat absen
        $absen = Absen::where('jadwal_id', $jadwal->jadwal_id)->first();

        if (!$absen) {
            return $this->notFoundResponse('Data absen tidak ditemukan');
        }

        if ($absen->clock_in) {
            return $this->errorResponse('Sudah melakukan clock in hari ini', 400);
        }

        // Upload photo to S3
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = 'clock_in_' . $karyawan->karyawan_id . '_' . $today->format('Y-m-d') . '.' . $photo->getClientOriginalExtension();
            $photoPath = Storage::disk('s3')->putFileAs('absen_photos', $photo, $filename);
        }

        // Check lateness
        $shiftStart = Carbon::parse($jadwal->shift->start_time);
        $lateMinutes = 0;
        $status = 'present';

        if ($now->isAfter($shiftStart->copy()->addMinutes($jadwal->shift->late_tolerance ?? 15))) {
            $lateMinutes = $shiftStart->diffInMinutes($now);
            $status = 'late';
        }

        // Update absen
        $absen->update([
            'clock_in' => $now->format('H:i:s'),
            'clock_in_photo' => $photoPath,
            'clock_in_latitude' => $request->latitude,
            'clock_in_longitude' => $request->longitude,
            'clock_in_address' => $request->address,
            'late_minutes' => $lateMinutes,
            'status' => $status
        ]);

        $message = $status === 'late' ? 'Clock in berhasil (Terlambat)' : 'Clock in berhasil';

        return $this->successResponse([
            'absen' => $absen->fresh(),
            'status' => $status,
            'late_minutes' => $lateMinutes,
            'clock_in_time' => $now->format('H:i:s')
        ], $message);
    }

    public function clockOut(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address' => 'required|string',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $user = $request->user();
        $karyawan = $user->karyawan;
        $today = Carbon::today();
        $now = Carbon::now();

        // ✅ CEK IJIN APPROVED
        $approvedIjin = Ijin::where('karyawan_id', $karyawan->karyawan_id)
            ->where('status', 'approved')
            ->where('date_from', '<=', $today)
            ->where('date_to', '>=', $today)
            ->first();

        if ($approvedIjin) {
            return $this->errorResponse(
                'Anda memiliki ijin yang sudah disetujui untuk hari ini (' . $approvedIjin->ijinType->name . '). Tidak dapat melakukan absensi.',
                403
            );
        }

        // Cari absen hari ini
        $absen = Absen::with('jadwal.shift')
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->whereDate('date', $today)
            ->first();

        if (!$absen) {
            return $this->notFoundResponse('Data absen tidak ditemukan');
        }

        if (!$absen->clock_in) {
            return $this->errorResponse('Belum melakukan clock in', 400);
        }

        if ($absen->clock_out) {
            return $this->errorResponse('Sudah melakukan clock out hari ini', 400);
        }

        // Upload photo to S3
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = 'clock_out_' . $karyawan->karyawan_id . '_' . $today->format('Y-m-d') . '.' . $photo->getClientOriginalExtension();
            $photoPath = Storage::disk('s3')->putFileAs('absen_photos', $photo, $filename);
        }

        // Calculate work hours
        $clockIn = Carbon::parse($absen->clock_in);
        $workMinutes = $now->diffInMinutes($clockIn);

        // Subtract break duration
        $breakDuration = $absen->jadwal->shift->break_duration ?? 0;
        $workMinutes -= $breakDuration;

        $workHours = round($workMinutes / 60, 2);

        // Check early checkout
        $shiftEnd = Carbon::parse($absen->jadwal->shift->end_time);
        $earlyCheckoutMinutes = 0;
        $currentStatus = $absen->status;

        if ($now->isBefore($shiftEnd->copy()->subMinutes($absen->jadwal->shift->early_checkout_tolerance ?? 15))) {
            $earlyCheckoutMinutes = $shiftEnd->diffInMinutes($now);
            if ($currentStatus !== 'late') {
                $currentStatus = 'early_checkout';
            }
        }

        // Update absen
        $absen->update([
            'clock_out' => $now->format('H:i:s'),
            'clock_out_photo' => $photoPath,
            'clock_out_latitude' => $request->latitude,
            'clock_out_longitude' => $request->longitude,
            'clock_out_address' => $request->address,
            'work_hours' => $workHours,
            'early_checkout_minutes' => $earlyCheckoutMinutes,
            'status' => $currentStatus
        ]);

        // ✅ KHUSUS OnCall: Update lembur OnCall juga setelah clock out
        if ($absen->type === 'oncall') {
            $this->updateLemburOnCall($absen);
        }

        return $this->successResponse([
            'absen' => $absen->fresh(),
            'work_hours' => $workHours,
            'early_checkout_minutes' => $earlyCheckoutMinutes,
            'clock_out_time' => $now->format('H:i:s')
        ], 'Clock out berhasil');
    }

    /**
     * Update Lembur OnCall setelah clock out
     * Auto-calculate total_jam dari clock_in sampai clock_out
     */
    private function updateLemburOnCall($absen)
    {
        try {
            // Cari lembur OnCall yang terkait dengan absen ini
            $lembur = Lembur::where('oncall_jadwal_id', $absen->jadwal_id)
                ->where('jenis_lembur', 'oncall')
                ->first();

            if (!$lembur) {
                Log::warning("Lembur OnCall tidak ditemukan untuk absen: {$absen->absen_id}");
                return;
            }

            // Calculate total jam dari clock_in ke clock_out
            $clockIn = Carbon::parse($absen->clock_in);
            $clockOut = Carbon::parse($absen->clock_out);
            $totalMinutes = $clockIn->diffInMinutes($clockOut);
            $totalJam = round($totalMinutes / 60, 2); // Dalam jam (misal: 4.5 jam)

            // Update lembur OnCall
            $lembur->update([
                'jam_selesai' => $absen->clock_out, // Jam selesai dari absen
                'total_jam' => $totalJam, // ✅ AUTO CALCULATE!
                'status' => 'draft', // Status jadi draft (siap di-submit)
            ]);

            Log::info("Lembur OnCall berhasil diupdate", [
                'lembur_id' => $lembur->lembur_id,
                'total_jam' => $totalJam,
                'clock_in' => $clockIn->format('H:i:s'),
                'clock_out' => $clockOut->format('H:i:s'),
            ]);

        } catch (\Exception $e) {
            Log::error("Error update lembur OnCall: " . $e->getMessage());
        }
    }

    public function history(Request $request)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;

        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $perPage = $this->getPerPage($request);

        $absens = Absen::with(['jadwal.shift'])
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date', 'desc')
            ->paginate($perPage);

        // Summary stats
        $allAbsens = Absen::where('karyawan_id', $karyawan->karyawan_id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        $summary = [
            'total' => $allAbsens->count(),
            'hadir' => $allAbsens->whereIn('status', ['present', 'late'])->count(),
            'terlambat' => $allAbsens->where('status', 'late')->count(),
            'tidak_hadir' => $allAbsens->where('status', 'absent')->count(),
            'total_jam_kerja' => $allAbsens->sum('work_hours'),
        ];

        return $this->paginatedResponse($absens, 'Riwayat absen berhasil diambil', [
            'summary' => $summary,
            'month' => $month,
            'year' => $year
        ]);
    }
}

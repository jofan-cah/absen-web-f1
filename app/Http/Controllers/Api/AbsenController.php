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
        $yesterday = Carbon::yesterday();

        if (!$karyawan) {
            return $this->notFoundResponse('Data karyawan tidak ditemukan');
        }

        // PRIORITAS 1: Cek apakah ada sesi aktif (clock_in ada, clock_out belum) dari kemarin/hari ini
        // Sesi aktif harus ditampilkan duluan - jadwal baru di-hold sampai clock out selesai
        $jadwalOngoing = Jadwal::with(['shift', 'absen'])
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->whereBetween('date', [$yesterday, $today])
            ->whereHas('absen', fn($q) => $q->whereNotNull('clock_in')->whereNull('clock_out'))
            ->where(function ($q) {
                $q->whereNull('type')->orWhere('type', '!=', 'oncall');
            })
            ->orderByDesc('date')
            ->first();

        // PRIORITAS 2: Jadwal hari ini
        $jadwals = Jadwal::with(['shift', 'absen'])
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->whereDate('date', $today)
            ->get();

        if ($jadwals->isEmpty() && !$jadwalOngoing) {
            return $this->notFoundResponse('Tidak ada jadwal untuk hari ini');
        }

        // Pisahkan jadwal regular dan oncall
        // Jika ada sesi ongoing → pakai itu sebagai jadwal regular (hold jadwal baru)
        $jadwalRegular = $jadwalOngoing ?? $jadwals->where('type', '!=', 'oncall')->first();
        $jadwalOnCall = $jadwals->where('type', 'oncall')->first();

        // Flag apakah sedang hold (ada sesi aktif, jadwal baru ditunda)
        $isOngoingSession = $jadwalOngoing && $jadwals->where('type', '!=', 'oncall')->isNotEmpty()
            && $jadwalOngoing->jadwal_id !== $jadwals->where('type', '!=', 'oncall')->first()?->jadwal_id;

        // Build response untuk jadwal regular
        $regularData = null;
        if ($jadwalRegular) {
            $absenRegular = $jadwalRegular->absen;
            $regularData = [
                'jadwal' => $jadwalRegular,
                'absen' => $absenRegular,
                'can_clock_in' => !$absenRegular || !$absenRegular->clock_in,
                'can_clock_out' => $absenRegular && $absenRegular->clock_in && !$absenRegular->clock_out,
            ];
        }

        // Build response untuk jadwal oncall
        $oncallData = null;
        if ($jadwalOnCall) {
            $absenOnCall = $jadwalOnCall->absen;
            $oncallData = [
                'jadwal' => $jadwalOnCall,
                'absen' => $absenOnCall,
                'can_clock_in' => !$absenOnCall || !$absenOnCall->clock_in,
                'can_clock_out' => $absenOnCall && $absenOnCall->clock_in && !$absenOnCall->clock_out,
            ];
        }

        // Backward compatibility: jika hanya ada satu jadwal
        $primaryJadwal = $jadwalRegular ?? $jadwalOnCall;
        $primaryAbsen = $primaryJadwal->absen ?? null;

        return $this->successResponse([
            'has_jadwal' => true,
            'jadwal' => $primaryJadwal,
            'absen' => $primaryAbsen,
            'can_clock_in' => !$primaryAbsen || !$primaryAbsen->clock_in,
            'can_clock_out' => $primaryAbsen && $primaryAbsen->clock_in && !$primaryAbsen->clock_out,
            // Sesi aktif sedang berjalan - jadwal baru di-hold sampai clock out
            'is_ongoing_session' => (bool) $isOngoingSession,
            'has_multiple_jadwal' => $jadwalRegular && $jadwalOnCall,
            'regular' => $regularData,
            'oncall' => $oncallData,
        ], 'Status absen hari ini');
    }

    public function clockIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address' => 'required|string',
            'photo' => 'required|image|mimes:jpeg,png,jpg',
            'type' => 'nullable|in:normal,oncall', // Optional: pilih jadwal mana (normal/oncall)
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $user = $request->user();
        $karyawan = $user->karyawan;
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $now = Carbon::now();

        // Type yang diminta (default: regular, atau auto-detect jika tidak diisi)
        $requestedType = $request->get('type');

        // ✅ CEK IJIN APPROVED (hanya untuk jadwal REGULAR, oncall tetap bisa)
        if ($requestedType !== 'oncall') {
            $approvedIjin = Ijin::where('karyawan_id', $karyawan->karyawan_id)
                ->where('status', 'approved')
                ->where('date_from', '<=', $today)
                ->where('date_to', '>=', $today)
                ->first();

            if ($approvedIjin && $requestedType === 'normal') {
                return $this->errorResponse(
                    'Anda memiliki ijin yang sudah disetujui untuk hari ini (' . $approvedIjin->ijinType->name . '). Tidak dapat melakukan absensi regular.',
                    403
                );
            }
        }

        // Cari jadwal hari ini berdasarkan type
        $jadwalQuery = Jadwal::with('shift')
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->whereDate('date', $today);

        if ($requestedType === 'oncall') {
            $jadwalQuery->where('type', 'oncall');
        } elseif ($requestedType === 'normal') {
            $jadwalQuery->where(function($q) {
                $q->where('type', '!=', 'oncall')->orWhereNull('type');
            });
        } else {
            // Auto-detect: prioritaskan regular, kalau tidak ada cari oncall
            $jadwal = (clone $jadwalQuery)->where(function($q) {
                $q->where('type', '!=', 'oncall')->orWhereNull('type');
            })->first();

            if (!$jadwal) {
                $jadwal = (clone $jadwalQuery)->where('type', 'oncall')->first();
            }
        }

        if (!isset($jadwal)) {
            $jadwal = $jadwalQuery->first();
        }

        // Fallback: cek overnight shift dari kemarin yang belum clock in (jam dini hari)
        if (!$jadwal && $requestedType !== 'oncall') {
            $jadwal = Jadwal::with('shift')
                ->where('karyawan_id', $karyawan->karyawan_id)
                ->whereDate('date', $yesterday)
                ->whereHas('shift', fn($q) => $q->where('is_overnight', true))
                ->whereHas('absen', fn($q) => $q->whereNull('clock_in'))
                ->where(function ($q) {
                    $q->whereNull('type')->orWhere('type', '!=', 'oncall');
                })
                ->first();
        }

        if (!$jadwal) {
            $typeLabel = $requestedType === 'oncall' ? 'OnCall' : 'regular';
            return $this->notFoundResponse("Tidak ada jadwal {$typeLabel} untuk hari ini");
        }

        // Cari absen
        $absen = Absen::where('jadwal_id', $jadwal->jadwal_id)->first();

        if (!$absen) {
            return $this->notFoundResponse('Data absen tidak ditemukan');
        }

        if ($absen->clock_in) {
            $typeLabel = $jadwal->type === 'oncall' ? 'OnCall' : 'regular';
            return $this->errorResponse("Sudah melakukan clock in {$typeLabel} hari ini", 400);
        }

        // Upload photo to S3
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $typePrefix = $jadwal->type === 'oncall' ? 'oncall_' : '';
            $filename = $typePrefix . 'clock_in_' . $karyawan->karyawan_id . '_' . $today->format('Y-m-d') . '_' . time() . '.' . $photo->getClientOriginalExtension();
            $photoPath = Storage::disk('s3')->putFileAs('absen_photos', $photo, $filename);
        }

        // Check lateness (skip untuk oncall - oncall tidak ada konsep telat)
        $lateMinutes = 0;
        $status = 'present';

        if ($jadwal->type !== 'oncall' && $jadwal->shift) {
            // Untuk overnight shift, start_time mengacu ke tanggal jadwal
            $shiftStartDateTime = Carbon::parse($jadwal->date->format('Y-m-d') . ' ' . $jadwal->shift->start_time);
            if ($now->isAfter($shiftStartDateTime->copy()->addMinutes($jadwal->shift->late_tolerance ?? 15))) {
                $lateMinutes = $shiftStartDateTime->diffInMinutes($now);
                $status = 'late';
            }
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

        // ✅ Untuk OnCall: Update juga status di Lembur
        if ($jadwal->type === 'oncall') {
            $lemburOnCall = Lembur::where('oncall_jadwal_id', $jadwal->jadwal_id)
                ->where('jenis_lembur', 'oncall')
                ->first();

            if ($lemburOnCall) {
                $lemburOnCall->update([
                    'absen_id' => $absen->absen_id,
                    'started_at' => $now,
                    'status' => 'in_progress', // Update status dari waiting_checkin ke in_progress
                ]);
            }
        }

        $typeLabel = $jadwal->type === 'oncall' ? 'OnCall' : '';
        $message = $status === 'late' ? "Clock in {$typeLabel} berhasil (Terlambat)" : "Clock in {$typeLabel} berhasil";

        return $this->successResponse([
            'absen' => $absen->fresh(),
            'jadwal_type' => $jadwal->type ?? 'normal',
            'status' => $status,
            'late_minutes' => $lateMinutes,
            'clock_in_time' => $now->format('H:i:s')
        ], trim($message));
    }

    public function clockOut(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address' => 'required|string',
            'photo' => 'required|image|mimes:jpeg,png,jpg',
            'type' => 'nullable|in:normal,oncall', // Optional: pilih jadwal mana (normal/oncall)
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $user = $request->user();
        $karyawan = $user->karyawan;
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $now = Carbon::now();

        // Type yang diminta
        $requestedType = $request->get('type');

        // ✅ CEK IJIN APPROVED (hanya untuk jadwal REGULAR, oncall tetap bisa)
        if ($requestedType !== 'oncall') {
            $approvedIjin = Ijin::where('karyawan_id', $karyawan->karyawan_id)
                ->where('status', 'approved')
                ->where('date_from', '<=', $today)
                ->where('date_to', '>=', $today)
                ->first();

            if ($approvedIjin && $requestedType === 'normal') {
                return $this->errorResponse(
                    'Anda memiliki ijin yang sudah disetujui untuk hari ini (' . $approvedIjin->ijinType->name . '). Tidak dapat melakukan absensi regular.',
                    403
                );
            }
        }

        // Cari absen hari ini berdasarkan type
        $absenQuery = Absen::with('jadwal.shift')
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->whereDate('date', $today);

        if ($requestedType === 'oncall') {
            $absenQuery->where('type', 'oncall');
        } elseif ($requestedType === 'normal') {
            $absenQuery->where(function($q) {
                $q->where('type', '!=', 'oncall')->orWhereNull('type');
            });
        } else {
            // Auto-detect: cari absen yang sudah clock in tapi belum clock out
            // Prioritaskan regular dulu
            $absen = (clone $absenQuery)->where(function($q) {
                $q->where('type', '!=', 'oncall')->orWhereNull('type');
            })->whereNotNull('clock_in')->whereNull('clock_out')->first();

            if (!$absen) {
                // Kalau regular tidak ada yang siap clock out, cari oncall
                $absen = (clone $absenQuery)->where('type', 'oncall')
                    ->whereNotNull('clock_in')->whereNull('clock_out')->first();
            }
        }

        if (!isset($absen)) {
            $absen = $absenQuery->whereNotNull('clock_in')->whereNull('clock_out')->first();
        }

        // Fallback: overnight shift dari kemarin yang sudah clock in tapi belum clock out
        if (!$absen && $requestedType !== 'oncall') {
            $absen = Absen::with('jadwal.shift')
                ->where('karyawan_id', $karyawan->karyawan_id)
                ->whereDate('date', $yesterday)
                ->whereNotNull('clock_in')
                ->whereNull('clock_out')
                ->whereHas('jadwal.shift', fn($q) => $q->where('is_overnight', true))
                ->where(function ($q) {
                    $q->whereNull('type')->orWhere('type', '!=', 'oncall');
                })
                ->first();
        }

        if (!$absen) {
            $typeLabel = $requestedType === 'oncall' ? 'OnCall' : 'regular';
            return $this->notFoundResponse("Data absen {$typeLabel} tidak ditemukan atau belum clock in");
        }

        if (!$absen->clock_in) {
            $typeLabel = $absen->type === 'oncall' ? 'OnCall' : 'regular';
            return $this->errorResponse("Belum melakukan clock in {$typeLabel}", 400);
        }

        if ($absen->clock_out) {
            $typeLabel = $absen->type === 'oncall' ? 'OnCall' : 'regular';
            return $this->errorResponse("Sudah melakukan clock out {$typeLabel} hari ini", 400);
        }

        // Upload photo to S3
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $typePrefix = $absen->type === 'oncall' ? 'oncall_' : '';
            $filename = $typePrefix . 'clock_out_' . $karyawan->karyawan_id . '_' . $today->format('Y-m-d') . '_' . time() . '.' . $photo->getClientOriginalExtension();
            $photoPath = Storage::disk('s3')->putFileAs('absen_photos', $photo, $filename);
        }

        // Calculate work hours
        // Untuk overnight shift, clock_in bisa dari hari kemarin - gunakan tanggal absen
        $clockInDateTime = Carbon::parse($absen->date->format('Y-m-d') . ' ' . $absen->clock_in);
        $workMinutes = $clockInDateTime->diffInMinutes($now);

        // Subtract break duration (skip untuk oncall)
        if ($absen->type !== 'oncall' && $absen->jadwal && $absen->jadwal->shift) {
            $breakDuration = $absen->jadwal->shift->break_duration ?? 0;
            $workMinutes -= $breakDuration;
        }

        $workHours = round($workMinutes / 60, 2);

        // Check early checkout (skip untuk oncall dan overnight - overnight punya end_time hari berikutnya)
        $earlyCheckoutMinutes = 0;
        $currentStatus = $absen->status;
        $isOvernight = $absen->jadwal && $absen->jadwal->shift && $absen->jadwal->shift->is_overnight;

        if ($absen->type !== 'oncall' && !$isOvernight && $absen->jadwal && $absen->jadwal->shift) {
            $shiftEnd = Carbon::parse($absen->jadwal->shift->end_time);
            if ($now->isBefore($shiftEnd->copy()->subMinutes($absen->jadwal->shift->early_checkout_tolerance ?? 15))) {
                $earlyCheckoutMinutes = $shiftEnd->diffInMinutes($now);
                if ($currentStatus !== 'late') {
                    $currentStatus = 'early_checkout';
                }
            }
        } elseif (!$absen->type && $isOvernight && $absen->jadwal && $absen->jadwal->shift) {
            // Untuk overnight: end_time adalah hari berikutnya
            $shiftEnd = Carbon::parse($absen->date->format('Y-m-d') . ' ' . $absen->jadwal->shift->end_time)
                ->addDay();
            if ($now->isBefore($shiftEnd->copy()->subMinutes($absen->jadwal->shift->early_checkout_tolerance ?? 15))) {
                $earlyCheckoutMinutes = $shiftEnd->diffInMinutes($now);
                if ($currentStatus !== 'late') {
                    $currentStatus = 'early_checkout';
                }
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

        $typeLabel = $absen->type === 'oncall' ? 'OnCall ' : '';

        return $this->successResponse([
            'absen' => $absen->fresh(),
            'jadwal_type' => $absen->type ?? 'normal',
            'work_hours' => $workHours,
            'early_checkout_minutes' => $earlyCheckoutMinutes,
            'clock_out_time' => $now->format('H:i:s')
        ], "Clock out {$typeLabel}berhasil");
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

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Jadwal;
use App\Models\Absen;
use App\Models\Notification;
use App\Services\FCMService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckAbsenNotification extends Command
{
    protected $signature = 'notif:check-absen {--type=clock_in : Type notifikasi (clock_in, clock_out, absent)}';
    protected $description = 'Cek absen karyawan dan kirim notifikasi FCM';
    protected $fcmService;

    public function __construct(FCMService $fcmService)
    {
        parent::__construct();
        $this->fcmService = $fcmService;
    }

    public function handle()
    {
        $type = $this->option('type');

        $this->info("🔔 Check Absen Notification - Type: {$type}");
        $this->info("📅 Date: " . now()->format('Y-m-d H:i:s'));
        $this->newLine();

        switch ($type) {
            case 'clock_in':
                $this->checkClockIn();
                break;
            case 'clock_out':
                $this->checkClockOut();
                break;
            case 'absent':
                $this->checkAbsent();
                break;
            default:
                $this->error("❌ Invalid type: {$type}");
                $this->info("Valid types: clock_in, clock_out, absent");
                return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * ✅ FIXED: Cek karyawan yang belum clock in + FILTER SHIFT TIME + VALIDASI KETAT
     */
    protected function checkClockIn()
    {
        $this->info('🔍 Mengecek karyawan yang belum clock in...');

        $now = Carbon::now();
        $today = today();

        // Ambil jadwal hari ini yang statusnya normal
        $jadwals = Jadwal::where('date', $today)
            ->where('status', 'normal')
            ->with(['karyawan.deviceTokens', 'shift'])
            ->get();

        $this->info("👥 Total jadwal hari ini: {$jadwals->count()}");
        $this->info("🕐 Waktu sekarang: {$now->format('H:i:s')}");
        $this->newLine();

        $sentCount = 0;
        $skipCount = 0;

        foreach ($jadwals as $jadwal) {
            // ✅ VALIDASI KETAT: Cek absen by jadwal_id DAN karyawan_id + date
            $existingAbsen = Absen::where('jadwal_id', $jadwal->jadwal_id)
                ->where('karyawan_id', $jadwal->karyawan_id)
                ->whereDate('date', $today)
                ->whereNotNull('clock_in')
                ->first();

            if ($existingAbsen) {
                $this->line("⏭️  {$jadwal->karyawan->full_name} - Sudah clock in ({$existingAbsen->clock_in})");
                $skipCount++;
                continue;
            }

            // ✅ FILTER: Cek apakah shift sudah dimulai
            if (!$jadwal->shift || !$jadwal->shift->start_time) {
                $this->warn("⚠️  {$jadwal->karyawan->full_name} - No shift start time");
                $skipCount++;
                continue;
            }

            // Parse shift start time
            $shiftStart = Carbon::createFromFormat('H:i:s', $jadwal->shift->start_time);
            $shiftStartToday = Carbon::parse($today)->setTimeFrom($shiftStart);

            // ✅ CRITICAL: Kirim notif HANYA kalau shift sudah lewat
            if ($now->lessThan($shiftStartToday)) {
                $this->line("⏭️  {$jadwal->karyawan->full_name} - Shift belum dimulai ({$shiftStart->format('H:i')})");
                $skipCount++;
                continue;
            }

            // Skip kalau karyawan tidak ada device token
            $deviceTokens = $jadwal->karyawan->getActiveDeviceTokens();
            if (empty($deviceTokens)) {
                $this->warn("⚠️  {$jadwal->karyawan->full_name} - No device token");
                $skipCount++;
                continue;
            }

            $recentNotif = Notification::where('karyawan_id', $jadwal->karyawan_id)
                ->where('type', 'reminder_clock_in')
                ->whereDate('created_at', $today)
                ->where('created_at', '>=', $now->copy()->subMinutes(30)) // 30 menit terakhir
                ->exists();

            if ($recentNotif) {
                $this->line("⏭️  {$jadwal->karyawan->full_name} - Notif sudah dikirim (30 menit terakhir)");
                $skipCount++;
                continue;
            }

            // Buat notifikasi
            $notification = Notification::create([
                'karyawan_id' => $jadwal->karyawan_id,
                'type' => 'reminder_clock_in',
                'title' => 'Reminder Absen Masuk',
                'message' => "Jangan lupa absen masuk ya! Shift {$jadwal->shift->name} sudah dimulai.",
                'data' => [
                    'jadwal_id' => $jadwal->jadwal_id,
                    'shift_id' => $jadwal->shift_id,
                    'date' => $today->format('Y-m-d')
                ]
            ]);

            // Kirim FCM ke semua device
            $fcmSuccess = false;
            foreach ($deviceTokens as $token) {
                $result = $this->fcmService->sendToDevice(
                    $token,
                    $notification->title,
                    $notification->message,
                    [
                        'notification_id' => $notification->notification_id,
                        'type' => 'reminder_clock_in',
                        'jadwal_id' => $jadwal->jadwal_id
                    ]
                );

                if ($result) {
                    $fcmSuccess = true;
                }
            }

            if ($fcmSuccess) {
                $notification->markFCMSent();
                $this->info("✅ {$jadwal->karyawan->full_name} (NIP: {$jadwal->karyawan->nip}) - Shift: {$jadwal->shift->name}");
                $sentCount++;
            } else {
                $this->error("❌ {$jadwal->karyawan->full_name} - FCM failed");
            }
        }

        $this->newLine();
        $this->info("📊 SUMMARY:");
        $this->info("✅ Notifikasi terkirim: {$sentCount}");
        $this->info("⏭️  Di-skip: {$skipCount}");
        $this->info("🎉 Selesai!");
    }

    /**
     * ✅ FIXED: Cek karyawan yang belum clock out + FILTER SHIFT TIME + VALIDASI KETAT
     */
    protected function checkClockOut()
    {
        $this->info('🔍 Mengecek karyawan yang belum clock out...');

        $now = Carbon::now();
        $today = today();

        // Ambil absen hari ini yang sudah clock_in tapi belum clock_out
        $absens = Absen::where('date', $today)
            ->whereNotNull('clock_in')
            ->whereNull('clock_out')
            ->with(['karyawan.deviceTokens', 'jadwal.shift'])
            ->get();

        $this->info("👥 Total belum clock out: {$absens->count()}");
        $this->info("🕐 Waktu sekarang: {$now->format('H:i:s')}");
        $this->newLine();

        $sentCount = 0;
        $skipCount = 0;

        foreach ($absens as $absen) {
            // ✅ FILTER: Cek apakah shift sudah selesai
            if (!$absen->jadwal || !$absen->jadwal->shift || !$absen->jadwal->shift->end_time) {
                $this->warn("⚠️  {$absen->karyawan->full_name} - No shift end time");
                $skipCount++;
                continue;
            }

            // Parse shift end time
            $shiftEnd = Carbon::createFromFormat('H:i:s', $absen->jadwal->shift->end_time);
            $shiftEndToday = Carbon::parse($today)->setTimeFrom($shiftEnd);

            // ✅ CRITICAL: Kirim notif HANYA kalau shift sudah selesai
            if ($now->lessThan($shiftEndToday)) {
                $this->line("⏭️  {$absen->karyawan->full_name} - Shift belum selesai ({$shiftEnd->format('H:i')})");
                $skipCount++;
                continue;
            }

            // Skip kalau karyawan tidak ada device token
            $deviceTokens = $absen->karyawan->getActiveDeviceTokens();
            if (empty($deviceTokens)) {
                $this->warn("⚠️  {$absen->karyawan->full_name} - No device token");
                $skipCount++;
                continue;
            }

            $recentNotif = Notification::where('karyawan_id', $absen->karyawan_id)
                ->where('type', 'reminder_clock_out')
                ->whereDate('created_at', $today)
                ->where('created_at', '>=', $now->copy()->subMinutes(30)) // 30 menit terakhir
                ->exists();

            if ($recentNotif) {
                $this->line("⏭️  {$absen->karyawan->full_name} - Notif sudah dikirim (30 menit terakhir)");
                $skipCount++;
                continue;
            }

            // Buat notifikasi
            $notification = Notification::create([
                'karyawan_id' => $absen->karyawan_id,
                'type' => 'reminder_clock_out',
                'title' => 'Reminder Absen Pulang',
                'message' => "Jangan lupa absen pulang ya! Shift {$absen->jadwal->shift->name} sudah selesai.",
                'data' => [
                    'absen_id' => $absen->absen_id,
                    'jadwal_id' => $absen->jadwal_id,
                    'date' => $today->format('Y-m-d')
                ]
            ]);

            // Kirim FCM ke semua device
            $fcmSuccess = false;
            foreach ($deviceTokens as $token) {
                $result = $this->fcmService->sendToDevice(
                    $token,
                    $notification->title,
                    $notification->message,
                    [
                        'notification_id' => $notification->notification_id,
                        'type' => 'reminder_clock_out',
                        'absen_id' => $absen->absen_id
                    ]
                );

                if ($result) {
                    $fcmSuccess = true;
                }
            }

            if ($fcmSuccess) {
                $notification->markFCMSent();
                $this->info("✅ {$absen->karyawan->full_name} (NIP: {$absen->karyawan->nip}) - Shift: {$absen->jadwal->shift->name}");
                $sentCount++;
            } else {
                $this->error("❌ {$absen->karyawan->full_name} - FCM failed");
            }
        }

        $this->newLine();
        $this->info("📊 SUMMARY:");
        $this->info("✅ Notifikasi terkirim: {$sentCount}");
        $this->info("⏭️  Di-skip: {$skipCount}");
        $this->info("🎉 Selesai!");
    }

    /**
     * Cek karyawan yang tidak absen sama sekali
     */
    protected function checkAbsent()
    {
        $this->info('🔍 Mengecek karyawan yang tidak absen...');

        $today = today();

        // Ambil jadwal hari ini yang belum ada absen sama sekali
        $jadwals = Jadwal::where('date', $today)
            ->where('status', 'normal')
            ->whereDoesntHave('absen')
            ->with(['karyawan.deviceTokens', 'shift'])
            ->get();

        $this->info("👥 Total tidak absen: {$jadwals->count()}");
        $this->newLine();

        $sentCount = 0;
        $skipCount = 0;

        foreach ($jadwals as $jadwal) {
            // Skip kalau karyawan tidak ada device token
            $deviceTokens = $jadwal->karyawan->getActiveDeviceTokens();
            if (empty($deviceTokens)) {
                $this->warn("⚠️  {$jadwal->karyawan->full_name} - No device token");
                $skipCount++;
                continue;
            }

            // ✅ CEK DUPLIKAT NOTIFIKASI
            $recentNotif = Notification::where('karyawan_id', $jadwal->karyawan_id)
                ->where('type', 'absent_alert')
                ->whereDate('created_at', $today)
                ->exists();

            if ($recentNotif) {
                $this->line("⏭️  {$jadwal->karyawan->full_name} - Notif sudah dikirim hari ini");
                $skipCount++;
                continue;
            }

            // Buat notifikasi
            $notification = Notification::create([
                'karyawan_id' => $jadwal->karyawan_id,
                'type' => 'absent_alert',
                'title' => 'Kamu Belum Absen!',
                'message' => 'Kamu belum absen hari ini. Segera hubungi koordinator untuk konfirmasi.',
                'data' => [
                    'jadwal_id' => $jadwal->jadwal_id,
                    'date' => $today->format('Y-m-d')
                ]
            ]);

            // Kirim FCM ke semua device
            $fcmSuccess = false;
            foreach ($deviceTokens as $token) {
                $result = $this->fcmService->sendToDevice(
                    $token,
                    $notification->title,
                    $notification->message,
                    [
                        'notification_id' => $notification->notification_id,
                        'type' => 'absent_alert',
                        'jadwal_id' => $jadwal->jadwal_id
                    ]
                );

                if ($result) {
                    $fcmSuccess = true;
                }
            }

            if ($fcmSuccess) {
                $notification->markFCMSent();
                $this->info("✅ {$jadwal->karyawan->full_name} (NIP: {$jadwal->karyawan->nip})");
                $sentCount++;
            } else {
                $this->error("❌ {$jadwal->karyawan->full_name} - FCM failed");
            }
        }

        $this->newLine();
        $this->info("📊 SUMMARY:");
        $this->info("✅ Notifikasi terkirim: {$sentCount}");
        $this->info("⏭️  Di-skip: {$skipCount}");
        $this->info("🎉 Selesai!");
    }
}

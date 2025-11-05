<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Jadwal;
use App\Models\Absen;
use App\Models\Notification;
use App\Services\FCMService;
use App\Services\SlackNotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckAbsenNotification extends Command
{
    protected $signature = 'notif:check-absen {--type=clock_in : Type notifikasi (clock_in, clock_out, absent)}';
    protected $description = 'Cek absen karyawan dan kirim notifikasi FCM + Slack';

    protected $fcmService;
    protected $slackService;

    public function __construct(FCMService $fcmService, SlackNotificationService $slackService)
    {
        parent::__construct();
        $this->fcmService = $fcmService;
        $this->slackService = $slackService;
    }

    public function handle()
    {
        $type = $this->option('type');
        $startTime = now();

        $this->info("🔔 Check Absen Notification - Type: {$type}");
        $this->info("📅 Date: " . $startTime->format('Y-m-d H:i:s'));
        $this->newLine();

        // 🔔 Kirim notif START ke Slack
        $this->slackService->notifySuccess("🚀 Check Absen Started", [
            'type' => strtoupper($type),
            'timestamp' => $startTime->format('Y-m-d H:i:s'),
            'command' => 'notif:check-absen --type=' . $type,
        ]);

        switch ($type) {
            case 'clock_in':
                $result = $this->checkClockIn();
                break;
            case 'clock_out':
                $result = $this->checkClockOut();
                break;
            case 'absent':
                $result = $this->checkAbsent();
                break;
            default:
                $this->error("❌ Invalid type: {$type}");
                $this->slackService->notifyError(
                    "Check Absen Failed - Invalid Type",
                    new \Exception("Invalid type: {$type}"),
                    ['type' => $type]
                );
                return Command::FAILURE;
        }

        $endTime = now();
        $duration = $endTime->diffInSeconds($startTime);

        // ✅ Kirim DETAILED summary ke Slack
        $summaryData = [
            'type' => strtoupper($type),
            'total_notif_sent' => $result['sent'],
            'total_skipped' => $result['skip'],
            'duration' => "{$duration} seconds",
            'timestamp' => $endTime->format('Y-m-d H:i:s'),
        ];

        // Tambah list yang dapet notif
        if (!empty($result['sent_list'])) {
            $summaryData['✅_sent_to'] = implode(', ', array_slice($result['sent_list'], 0, 10));
            if (count($result['sent_list']) > 10) {
                $summaryData['sent_note'] = 'Showing first 10 of ' . count($result['sent_list']) . ' recipients';
            }
        }

        // Tambah list yang di-skip
        if (!empty($result['skip_list'])) {
            $summaryData['⏭️_skipped'] = implode(', ', array_slice($result['skip_list'], 0, 10));
            if (count($result['skip_list']) > 10) {
                $summaryData['skip_note'] = 'Showing first 10 of ' . count($result['skip_list']) . ' skipped';
            }
        }

        // Tambah list error kalau ada
        if (!empty($result['error_list'])) {
            $summaryData['❌_errors'] = implode(', ', array_slice($result['error_list'], 0, 5));
            if (count($result['error_list']) > 5) {
                $summaryData['error_note'] = 'Showing first 5 of ' . count($result['error_list']) . ' errors';
            }
        }

        $this->slackService->notifySuccess("✅ Check Absen Completed - " . strtoupper($type), $summaryData);

        return Command::SUCCESS;
    }

    /**
     * ✅ Cek karyawan yang belum clock in
     */
    protected function checkClockIn()
    {
        $this->info('🔍 Mengecek karyawan yang belum clock in...');

        $now = Carbon::now();
        $today = today();

        $jadwals = Jadwal::where('date', $today)
            ->where('status', 'normal')
            ->with(['karyawan.deviceTokens', 'shift'])
            ->get();

        $this->info("👥 Total jadwal hari ini: {$jadwals->count()}");
        $this->info("🕐 Waktu sekarang: {$now->format('H:i:s')}");
        $this->newLine();

        $sentCount = 0;
        $skipCount = 0;
        $sentList = [];
        $skipList = [];
        $errorList = [];

        foreach ($jadwals as $jadwal) {
            $karyawanName = $jadwal->karyawan->full_name;
            $karyawanNIP = $jadwal->karyawan->nip;

            // ✅ VALIDASI: Cek sudah clock in
            $existingAbsen = Absen::where('jadwal_id', $jadwal->jadwal_id)
                ->where('karyawan_id', $jadwal->karyawan_id)
                ->whereDate('date', $today)
                ->whereNotNull('clock_in')
                ->first();

            if ($existingAbsen) {
                $this->line("⏭️  {$karyawanName} - Sudah clock in ({$existingAbsen->clock_in})");
                $skipList[] = "{$karyawanName} (Sudah clock in)";
                $skipCount++;
                continue;
            }

            // ✅ VALIDASI: Shift harus ada
            if (!$jadwal->shift || !$jadwal->shift->start_time) {
                $this->warn("⚠️  {$karyawanName} - No shift start time");
                $skipList[] = "{$karyawanName} (No shift)";
                $skipCount++;
                continue;
            }

            // ✅ FILTER: Shift sudah dimulai
            $shiftStart = Carbon::createFromFormat('H:i:s', $jadwal->shift->start_time);
            $shiftStartToday = Carbon::parse($today)->setTimeFrom($shiftStart);

            if ($now->lessThan($shiftStartToday)) {
                $this->line("⏭️  {$karyawanName} - Shift belum dimulai ({$shiftStart->format('H:i')})");
                $skipList[] = "{$karyawanName} (Shift belum mulai)";
                $skipCount++;
                continue;
            }

            // ✅ VALIDASI: Device token harus ada
            $deviceTokens = $jadwal->karyawan->getActiveDeviceTokens();
            if (empty($deviceTokens)) {
                $this->warn("⚠️  {$karyawanName} - No device token");
                $errorList[] = "{$karyawanName} (No token)";
                $skipCount++;
                continue;
            }

            // ✅ VALIDASI: Cek duplikat notif
            $recentNotif = Notification::where('karyawan_id', $jadwal->karyawan_id)
                ->where('type', 'reminder_clock_in')
                ->whereDate('created_at', $today)
                ->where('created_at', '>=', $now->copy()->subMinutes(30))
                ->exists();

            if ($recentNotif) {
                $this->line("⏭️  {$karyawanName} - Notif sudah dikirim");
                $skipList[] = "{$karyawanName} (Sudah dapat notif)";
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

            // Kirim FCM
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
                $this->info("✅ {$karyawanName} (NIP: {$karyawanNIP})");
                $sentList[] = "{$karyawanName} ({$karyawanNIP})";
                $sentCount++;
            } else {
                $this->error("❌ {$karyawanName} - FCM failed");
                $errorList[] = "{$karyawanName} (FCM failed)";
            }
        }

        $this->newLine();
        $this->info("📊 SUMMARY:");
        $this->info("✅ Notifikasi terkirim: {$sentCount}");
        $this->info("⏭️  Di-skip: {$skipCount}");
        $this->info("🎉 Selesai!");

        return [
            'sent' => $sentCount,
            'skip' => $skipCount,
            'sent_list' => $sentList,
            'skip_list' => $skipList,
            'error_list' => $errorList,
        ];
    }

    /**
     * ✅ Cek karyawan yang belum clock out
     */
    protected function checkClockOut()
    {
        $this->info('🔍 Mengecek karyawan yang belum clock out...');

        $now = Carbon::now();
        $today = today();

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
        $sentList = [];
        $skipList = [];
        $errorList = [];

        foreach ($absens as $absen) {
            $karyawanName = $absen->karyawan->full_name;
            $karyawanNIP = $absen->karyawan->nip;

            // ✅ VALIDASI: Shift harus ada
            if (!$absen->jadwal || !$absen->jadwal->shift || !$absen->jadwal->shift->end_time) {
                $this->warn("⚠️  {$karyawanName} - No shift end time");
                $skipList[] = "{$karyawanName} (No shift)";
                $skipCount++;
                continue;
            }

            // ✅ FILTER: Shift sudah selesai
            $shiftEnd = Carbon::createFromFormat('H:i:s', $absen->jadwal->shift->end_time);
            $shiftEndToday = Carbon::parse($today)->setTimeFrom($shiftEnd);

            if ($now->lessThan($shiftEndToday)) {
                $this->line("⏭️  {$karyawanName} - Shift belum selesai ({$shiftEnd->format('H:i')})");
                $skipList[] = "{$karyawanName} (Shift belum selesai)";
                $skipCount++;
                continue;
            }

            // ✅ VALIDASI: Device token harus ada
            $deviceTokens = $absen->karyawan->getActiveDeviceTokens();
            if (empty($deviceTokens)) {
                $this->warn("⚠️  {$karyawanName} - No device token");
                $errorList[] = "{$karyawanName} (No token)";
                $skipCount++;
                continue;
            }

            // ✅ VALIDASI: Cek duplikat notif
            $recentNotif = Notification::where('karyawan_id', $absen->karyawan_id)
                ->where('type', 'reminder_clock_out')
                ->whereDate('created_at', $today)
                ->where('created_at', '>=', $now->copy()->subMinutes(30))
                ->exists();

            if ($recentNotif) {
                $this->line("⏭️  {$karyawanName} - Notif sudah dikirim");
                $skipList[] = "{$karyawanName} (Sudah dapat notif)";
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

            // Kirim FCM
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
                $this->info("✅ {$karyawanName} (NIP: {$karyawanNIP})");
                $sentList[] = "{$karyawanName} ({$karyawanNIP})";
                $sentCount++;
            } else {
                $this->error("❌ {$karyawanName} - FCM failed");
                $errorList[] = "{$karyawanName} (FCM failed)";
            }
        }

        $this->newLine();
        $this->info("📊 SUMMARY:");
        $this->info("✅ Notifikasi terkirim: {$sentCount}");
        $this->info("⏭️  Di-skip: {$skipCount}");
        $this->info("🎉 Selesai!");

        return [
            'sent' => $sentCount,
            'skip' => $skipCount,
            'sent_list' => $sentList,
            'skip_list' => $skipList,
            'error_list' => $errorList,
        ];
    }

    /**
     * ✅ Cek karyawan yang tidak absen
     */
    protected function checkAbsent()
    {
        $this->info('🔍 Mengecek karyawan yang tidak absen...');

        $today = today();

        $jadwals = Jadwal::where('date', $today)
            ->where('status', 'normal')
            ->whereDoesntHave('absen')
            ->with(['karyawan.deviceTokens', 'shift'])
            ->get();

        $this->info("👥 Total tidak absen: {$jadwals->count()}");
        $this->newLine();

        $sentCount = 0;
        $skipCount = 0;
        $sentList = [];
        $skipList = [];
        $errorList = [];

        foreach ($jadwals as $jadwal) {
            $karyawanName = $jadwal->karyawan->full_name;
            $karyawanNIP = $jadwal->karyawan->nip;

            // ✅ VALIDASI: Device token harus ada
            $deviceTokens = $jadwal->karyawan->getActiveDeviceTokens();
            if (empty($deviceTokens)) {
                $this->warn("⚠️  {$karyawanName} - No device token");
                $errorList[] = "{$karyawanName} (No token)";
                $skipCount++;
                continue;
            }

            // ✅ VALIDASI: Cek duplikat notif
            $recentNotif = Notification::where('karyawan_id', $jadwal->karyawan_id)
                ->where('type', 'absent_alert')
                ->whereDate('created_at', $today)
                ->exists();

            if ($recentNotif) {
                $this->line("⏭️  {$karyawanName} - Notif sudah dikirim");
                $skipList[] = "{$karyawanName} (Sudah dapat notif)";
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

            // Kirim FCM
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
                $this->info("✅ {$karyawanName} (NIP: {$karyawanNIP})");
                $sentList[] = "{$karyawanName} ({$karyawanNIP})";
                $sentCount++;
            } else {
                $this->error("❌ {$karyawanName} - FCM failed");
                $errorList[] = "{$karyawanName} (FCM failed)";
            }
        }

        $this->newLine();
        $this->info("📊 SUMMARY:");
        $this->info("✅ Notifikasi terkirim: {$sentCount}");
        $this->info("⏭️  Di-skip: {$skipCount}");
        $this->info("🎉 Selesai!");

        return [
            'sent' => $sentCount,
            'skip' => $skipCount,
            'sent_list' => $sentList,
            'skip_list' => $skipList,
            'error_list' => $errorList,
        ];
    }
}

<?php

namespace App\Console\Commands;

use App\Models\DeviceToken;
use App\Models\Karyawan;
use App\Models\Notification;
use App\Services\FCMService;
use Illuminate\Console\Command;

class TestNotification extends Command
{
    protected $signature = 'notif:test
                            {nip? : NIP karyawan (kosongkan untuk broadcast ke semua)}
                            {--title=Test Notifikasi : Judul notifikasi}
                            {--message= : Isi pesan}
                            {--all : Kirim ke semua karyawan aktif yang punya device token}';

    protected $description = 'Test kirim FCM notification ke karyawan berdasarkan NIP, atau broadcast ke semua';

    public function __construct(protected FCMService $fcmService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if ($this->option('all') || !$this->argument('nip')) {
            return $this->handleBroadcast();
        }

        return $this->handleSingle($this->argument('nip'));
    }

    protected function handleBroadcast(): int
    {
        $title   = $this->option('title');
        $message = $this->option('message') ?: 'Halo! Ini broadcast test notifikasi dari server. ✅';

        $this->info("══════════════════════════════════════════");
        $this->info("  NOTIF BROADCAST - SEMUA KARYAWAN");
        $this->info("══════════════════════════════════════════");
        $this->line("  Judul : {$title}");
        $this->line("  Pesan : {$message}");
        $this->newLine();

        $tokens = DeviceToken::with('karyawan')
            ->where('is_active', true)
            ->get();

        if ($tokens->isEmpty()) {
            $this->warn("⚠️  Tidak ada device token aktif sama sekali.");
            return Command::FAILURE;
        }

        $this->line("📱 Total device aktif: <info>{$tokens->count()}</info>");
        $this->newLine();

        $success = 0;
        $fail    = 0;

        foreach ($tokens as $t) {
            $nama   = $t->karyawan?->full_name ?? 'Unknown';
            $result = $this->fcmService->sendToDevice(
                $t->device_token,
                $title,
                $message,
                ['type' => 'general']
            );

            if ($result) {
                $success++;
                $this->line("  ✅ {$nama}");
            } else {
                $fail++;
                $this->error("  ❌ {$nama} - FCM gagal");
            }
        }

        $this->newLine();
        $this->info("── Summary ────────────────────────────────────");
        $this->line("  ✅ Berhasil : {$success}");
        $this->line("  ❌ Gagal    : {$fail}");
        $this->line("  📊 Total    : " . ($success + $fail));

        return $success > 0 ? Command::SUCCESS : Command::FAILURE;
    }

    protected function handleSingle(string $nip): int
    {
        $this->info("══════════════════════════════════════════");
        $this->info("  NOTIF TEST - NIP: {$nip}");
        $this->info("══════════════════════════════════════════");
        $this->newLine();

        // ── 1. Cek karyawan ──────────────────────────────────────────────────
        $karyawan = Karyawan::where('nip', $nip)->first();

        if (!$karyawan) {
            $this->error("❌ Karyawan dengan NIP {$nip} tidak ditemukan.");
            return Command::FAILURE;
        }

        $this->line("👤 Nama   : <info>{$karyawan->full_name}</info>");
        $this->line("🪪 NIP    : {$karyawan->nip}");
        $this->line("💼 Status : {$karyawan->employment_status}");
        $this->line("🆔 ID     : {$karyawan->karyawan_id}");
        $this->newLine();

        // ── 2. Cek device token ───────────────────────────────────────────────
        $this->info("── Device Tokens ──────────────────────────────");

        $tokens = DeviceToken::where('karyawan_id', $karyawan->karyawan_id)->get();

        if ($tokens->isEmpty()) {
            $this->warn("⚠️  Tidak ada device token terdaftar.");
            $this->line("   → Karyawan belum login di aplikasi mobile, atau token dihapus saat logout.");
            return Command::FAILURE;
        }

        foreach ($tokens as $t) {
            $status    = $t->is_active ? '<info>AKTIF</info>' : '<comment>NONAKTIF</comment>';
            $lastUsed  = $t->last_used_at?->diffForHumans() ?? 'belum pernah';
            $tokenSnip = substr($t->device_token, 0, 40) . '...';

            $this->line("  [{$t->device_type}] {$status} | last used: {$lastUsed}");
            $this->line("         token: {$tokenSnip}");
        }

        $activeTokens = $tokens->where('is_active', true)->pluck('device_token')->toArray();

        if (empty($activeTokens)) {
            $this->newLine();
            $this->error("❌ Semua token NONAKTIF. Karyawan perlu login ulang di aplikasi.");
            return Command::FAILURE;
        }

        $this->line("  ✅ Active token: " . count($activeTokens) . " buah");
        $this->newLine();

        // ── 3. Kirim FCM ──────────────────────────────────────────────────────
        $this->info("── Kirim FCM ──────────────────────────────────");

        $title   = $this->option('title');
        $message = $this->option('message') ?: "Halo {$karyawan->full_name}! Ini test notifikasi dari server. ✅";

        $this->line("  Judul  : {$title}");
        $this->line("  Pesan  : {$message}");
        $this->newLine();

        $successCount = 0;
        $failCount    = 0;

        foreach ($activeTokens as $token) {
            $result = $this->fcmService->sendToDevice(
                $token,
                $title,
                $message,
                ['type' => 'test', 'karyawan_id' => $karyawan->karyawan_id]
            );

            if ($result) {
                $successCount++;
                $this->line("  ✅ Berhasil → " . substr($token, 0, 30) . "...");
            } else {
                $failCount++;
                $this->error("  ❌ Gagal    → " . substr($token, 0, 30) . "...");
            }
        }

        $this->newLine();

        // ── 4. Simpan ke tabel notifications ─────────────────────────────────
        if ($successCount > 0) {
            Notification::create([
                'karyawan_id' => $karyawan->karyawan_id,
                'type'        => 'general',
                'title'       => $title,
                'message'     => $message,
                'data'        => ['source' => 'artisan notif:test'],
            ]);
        }

        // ── 5. Summary ────────────────────────────────────────────────────────
        $this->info("── Summary ────────────────────────────────────");
        $this->line("  ✅ Berhasil : {$successCount}");
        $this->line("  ❌ Gagal    : {$failCount}");
        $this->newLine();

        if ($successCount > 0) {
            $this->info("🎉 Notifikasi berhasil dikirim ke {$karyawan->full_name}!");
            $this->line("   Cek HP karyawan apakah notif muncul.");
        } else {
            $this->error("Semua pengiriman FCM gagal.");
            $this->line("   Cek: storage/logs/laravel.log untuk detail error FCM.");
        }

        return $successCount > 0 ? Command::SUCCESS : Command::FAILURE;
    }
}

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
                            {nip : NIP karyawan yang akan ditest}
                            {--title=Test Notifikasi : Judul notifikasi}
                            {--message= : Isi pesan (default: "Ini test notifikasi dari server untuk <nama>")}';

    protected $description = 'Test kirim FCM notification ke karyawan berdasarkan NIP';

    public function __construct(protected FCMService $fcmService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $nip = $this->argument('nip');

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

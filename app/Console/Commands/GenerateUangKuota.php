<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Karyawan;
use App\Models\TunjanganKaryawan;
use App\Services\SlackNotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GenerateUangKuota extends Command
{
    protected $signature = 'tunjangan:generate-kuota {--month=} {--year=}';
    protected $description = '[DISABLED] Generate uang kuota';
    protected $hidden = true;
    protected $slackService;

    public function __construct(SlackNotificationService $slackService)
    {
        parent::__construct();
        $this->slackService = $slackService;
    }

    public function handle()
    {
        $this->warn('⚠️  Command ini sedang dinonaktifkan.');
        return Command::SUCCESS;

        $startTime = now();
        $this->info('🚀 Memulai generate uang kuota...');

        // Ambil bulan dan tahun
        $month = $this->option('month') ?? now()->month;
        $year = $this->option('year') ?? now()->year;

        $monthName = Carbon::create($year, $month, 1)->format('F Y');

        $this->info("📅 Periode: {$monthName}");

        // 🔔 Kirim START ke Slack
        $this->slackService->notifySuccess("📦 Generate Uang Kuota Started", [
            'periode' => $monthName,
            'month' => $month,
            'year' => $year,
            'timestamp' => $startTime->format('Y-m-d H:i:s'),
            'command' => 'tunjangan:generate-kuota',
        ]);

        // Ambil karyawan yang berhak
        $karyawans = Karyawan::where('uang_kuota', true)
            ->where('employment_status', 'active')
            ->whereNotIn('karyawan_id', ['KAR001', 'KAR010'])
            ->with(['user', 'department'])
            ->get();

        $this->info("👥 Total karyawan yang berhak: {$karyawans->count()}");

        $successCount = 0;
        $skipCount = 0;
        $errorCount = 0;

        $successList = [];
        $skipList = [];
        $errorList = [];

        foreach ($karyawans as $karyawan) {
            try {
                // Cek duplikat
                $exists = TunjanganKaryawan::where('karyawan_id', $karyawan->karyawan_id)
                    ->whereHas('tunjanganType', function ($query) {
                        $query->where('code', 'UANG_KUOTA');
                    })
                    ->whereYear('period_start', $year)
                    ->whereMonth('period_start', $month)
                    ->exists();

                if ($exists) {
                    $this->warn("⚠️  Skip: {$karyawan->full_name} (NIP: {$karyawan->nip}) - Sudah ada");
                    $skipList[] = "{$karyawan->full_name} ({$karyawan->nip})";
                    $skipCount++;
                    continue;
                }

                // Generate tunjangan
                $tunjangan = TunjanganKaryawan::generateTunjanganKuota(
                    $karyawan->karyawan_id,
                    $month,
                    $year
                );

                if ($tunjangan) {
                    $this->info("✅ Berhasil: {$karyawan->full_name} (NIP: {$karyawan->nip}) - Rp " . number_format($tunjangan->total_amount, 0, ',', '.'));
                    $successList[] = "{$karyawan->full_name} ({$karyawan->nip}, Rp " . number_format($tunjangan->total_amount, 0, ',', '.') . ")";
                    $successCount++;

                    Log::info("Uang kuota generated", [
                        'karyawan_id' => $karyawan->karyawan_id,
                        'nip' => $karyawan->nip,
                        'name' => $karyawan->full_name,
                        'amount' => $tunjangan->total_amount,
                        'month' => $month,
                        'year' => $year,
                    ]);
                } else {
                    $this->error("❌ Gagal: {$karyawan->full_name} (NIP: {$karyawan->nip})");
                    $errorList[] = "{$karyawan->full_name} ({$karyawan->nip}, Failed to generate)";
                    $errorCount++;
                }

            } catch (\Exception $e) {
                $this->error("❌ Error: {$karyawan->full_name} - {$e->getMessage()}");
                $errorList[] = "{$karyawan->full_name} ({$e->getMessage()})";
                $errorCount++;

                Log::error("Generate uang kuota error", [
                    'karyawan_id' => $karyawan->karyawan_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $endTime = now();
        $duration = $endTime->diffInSeconds($startTime);

        // Summary
        $this->newLine();
        $this->info("📊 SUMMARY:");
        $this->info("✅ Berhasil: {$successCount}");
        $this->warn("⚠️  Di-skip: {$skipCount}");
        $this->error("❌ Gagal: {$errorCount}");
        $this->info("🎉 Selesai!");

        // ✅ Kirim DETAILED summary ke Slack
        $summaryData = [
            'periode' => $monthName,
            'total_berhasil' => $successCount,
            'total_skipped' => $skipCount,
            'total_error' => $errorCount,
            'duration' => "{$duration} seconds",
            'timestamp' => $endTime->format('Y-m-d H:i:s'),
        ];

        // Tambah list success (max 10)
        if (!empty($successList)) {
            $summaryData['✅_generated'] = implode(', ', array_slice($successList, 0, 10));
            if (count($successList) > 10) {
                $summaryData['generated_note'] = 'Showing first 10 of ' . count($successList);
            }
        }

        // Tambah list skip (max 10)
        if (!empty($skipList)) {
            $summaryData['⏭️_skipped'] = implode(', ', array_slice($skipList, 0, 10));
            if (count($skipList) > 10) {
                $summaryData['skip_note'] = 'Showing first 10 of ' . count($skipList);
            }
        }

        // Tambah list error (max 5)
        if (!empty($errorList)) {
            $summaryData['❌_errors'] = implode(', ', array_slice($errorList, 0, 5));
        }

        $this->slackService->notifySuccess("✅ Generate Uang Kuota Completed", $summaryData);

        return Command::SUCCESS;
    }
}

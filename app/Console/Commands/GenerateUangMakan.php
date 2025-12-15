<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Karyawan;
use App\Models\TunjanganKaryawan;
use App\Models\TunjanganType;
use App\Models\TunjanganDetail;
use App\Models\Absen;
use App\Models\Penalti;
use App\Services\SlackNotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class GenerateUangMakan extends Command
{
    protected $signature = 'tunjangan:generate-makan {--week-start=}';
    protected $description = 'Generate uang makan mingguan dengan delay system + Slack notification';
    protected $slackService;

    public function __construct(SlackNotificationService $slackService)
    {
        parent::__construct();
        $this->slackService = $slackService;
    }

    public function handle()
    {
        $startTime = now();
        $this->info('🚀 Memulai generate uang makan mingguan...');

        // Tentukan periode minggu
        if ($this->option('week-start')) {
            $weekStart = Carbon::parse($this->option('week-start'))->startOfWeek(Carbon::MONDAY);
        } else {
            $weekStart = Carbon::now()->subWeek()->startOfWeek(Carbon::MONDAY);
        }

        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        $this->info("📅 Periode: {$weekStart->format('d/m/Y')} - {$weekEnd->format('d/m/Y')}");
        $this->newLine();

        // 🔔 Kirim START ke Slack
        $this->slackService->notifySuccess("🍱 Generate Uang Makan Started", [
            'periode' => "{$weekStart->format('d/m/Y')} - {$weekEnd->format('d/m/Y')}",
            'timestamp' => $startTime->format('Y-m-d H:i:s'),
            'command' => 'tunjangan:generate-makan',
        ]);

        // Get tunjangan type
        $tunjanganType = TunjanganType::where('code', 'UANG_MAKAN')
            ->where('is_active', true)
            ->first();

        if (!$tunjanganType) {
            $this->error('❌ Tunjangan type UANG_MAKAN tidak ditemukan!');

            // 🔴 Kirim error ke Slack
            $this->slackService->notifyError(
                "Generate Uang Makan Failed",
                new \Exception("Tunjangan type UANG_MAKAN tidak ditemukan"),
                ['periode' => "{$weekStart->format('d/m/Y')} - {$weekEnd->format('d/m/Y')}"]
            );

            return Command::FAILURE;
        }

        // Ambil karyawan aktif
        $karyawans = Karyawan::where('employment_status', 'active')
            ->whereNotIn('karyawan_id', ['KAR001'])
            ->with(['user', 'department'])
            ->get();

        $this->info("👥 Total karyawan: {$karyawans->count()}");
        $this->newLine();

        $successCount = 0;
        $skipCount = 0;
        $errorCount = 0;

        $successList = [];
        $skipList = [];
        $errorList = [];
        $delayList = [];

        foreach ($karyawans as $karyawan) {
            try {
                // Cek duplikat
                $exists = TunjanganKaryawan::where('karyawan_id', $karyawan->karyawan_id)
                    ->where('tunjangan_type_id', $tunjanganType->tunjangan_type_id)
                    ->where('period_start', $weekStart->format('Y-m-d'))
                    ->where('period_end', $weekEnd->format('Y-m-d'))
                    ->exists();

                if ($exists) {
                    $this->warn("⏭️  Skip: {$karyawan->full_name} - Sudah ada");
                    $skipList[] = "{$karyawan->full_name} (Sudah ada)";
                    $skipCount++;
                    continue;
                }

                // Hitung hari kerja
                $hariKerjaAsli = Absen::where('karyawan_id', $karyawan->karyawan_id)
                    ->whereBetween('date', [$weekStart, $weekEnd])
                    ->whereNotNull('clock_in')
                    ->where('type', '!=', 'oncall')
                    ->count();

                if ($hariKerjaAsli === 0) {
                    $this->line("⏭️  Skip: {$karyawan->full_name} - Tidak ada hari kerja");
                    $skipList[] = "{$karyawan->full_name} (Tidak ada hari kerja)";
                    $skipCount++;
                    continue;
                }

                // Hitung penalti
                $hariPotongPenalti = Penalti::getTotalHariPotongan(
                    $karyawan->karyawan_id,
                    $weekStart->format('Y-m-d'),
                    $weekEnd->format('Y-m-d')
                );

                // Hitung delay
                $hariTidakLogout = Absen::where('karyawan_id', $karyawan->karyawan_id)
                    ->whereBetween('date', [$weekStart, $weekEnd])
                    ->whereNotNull('clock_in')
                    ->whereNull('clock_out')
                    ->where('type', '!=', 'oncall')
                    ->count();

                $delayDays = $hariTidakLogout;
                $availableRequestDate = Carbon::parse($weekEnd)->addDays($delayDays);

                // Get amount
                $amount = TunjanganDetail::getAmountByStaffStatus(
                    $tunjanganType->tunjangan_type_id,
                    $karyawan->staff_status
                );

                if ($amount <= 0) {
                    $this->error("❌ {$karyawan->full_name} - Nominal tidak ditemukan");
                    $errorList[] = "{$karyawan->full_name} (Nominal tidak ditemukan)";
                    $errorCount++;
                    continue;
                }

                // Create tunjangan
                $tunjangan = TunjanganKaryawan::create([
                    'tunjangan_karyawan_id' => TunjanganKaryawan::generateTunjanganKaryawanId(),
                    'karyawan_id' => $karyawan->karyawan_id,
                    'tunjangan_type_id' => $tunjanganType->tunjangan_type_id,
                    'period_start' => $weekStart->format('Y-m-d'),
                    'period_end' => $weekEnd->format('Y-m-d'),
                    'amount' => $amount,
                    'quantity' => $hariKerjaAsli,
                    'hari_kerja_asli' => $hariKerjaAsli,
                    'hari_potong_penalti' => $hariPotongPenalti,
                    'delay_days' => $delayDays,
                    'available_request_date' => $availableRequestDate,
                    'status' => 'pending',
                    'notes' => "Uang makan {$weekStart->format('d/m/Y')} - {$weekEnd->format('d/m/Y')} ({$hariKerjaAsli} hari kerja)",
                ]);

                $delayInfo = $delayDays > 0 ? " | ⏰ {$delayDays}d" : "";
                $penaltiInfo = $hariPotongPenalti > 0 ? " | ⚠️ -{$hariPotongPenalti}d" : "";

                $this->info("✅ {$karyawan->full_name} - {$hariKerjaAsli}d - Rp " . number_format($tunjangan->total_amount, 0, ',', '.') . $delayInfo . $penaltiInfo);

                $successList[] = "{$karyawan->full_name} ({$hariKerjaAsli}d, Rp " . number_format($tunjangan->total_amount, 0, ',', '.') . ")";

                if ($delayDays > 0) {
                    $delayList[] = "{$karyawan->full_name} (Delay {$delayDays} hari - bisa request {$availableRequestDate->format('d/m')})";
                }

                $successCount++;

                Log::info("Uang makan generated", [
                    'karyawan_id' => $karyawan->karyawan_id,
                    'name' => $karyawan->full_name,
                    'hari_kerja_asli' => $hariKerjaAsli,
                    'delay_days' => $delayDays,
                    'total_amount' => $tunjangan->total_amount,
                ]);

            } catch (\Exception $e) {
                $this->error("❌ Error: {$karyawan->full_name} - {$e->getMessage()}");
                $errorList[] = "{$karyawan->full_name} ({$e->getMessage()})";
                $errorCount++;

                Log::error("Generate uang makan error", [
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
        $this->warn("⏭️  Di-skip: {$skipCount}");
        $this->error("❌ Gagal: {$errorCount}");
        $this->info("🎉 Selesai!");

        // ✅ Kirim DETAILED summary ke Slack
        $summaryData = [
            'periode' => "{$weekStart->format('d/m/Y')} - {$weekEnd->format('d/m/Y')}",
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

        // Tambah list delay (max 10)
        if (!empty($delayList)) {
            $summaryData['⏰_with_delay'] = implode(', ', array_slice($delayList, 0, 10));
            if (count($delayList) > 10) {
                $summaryData['delay_note'] = 'Showing first 10 of ' . count($delayList);
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

        $this->slackService->notifySuccess("✅ Generate Uang Makan Completed", $summaryData);

        return Command::SUCCESS;
    }
}

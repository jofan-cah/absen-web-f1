<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Karyawan;
use App\Models\TunjanganKaryawan;
use App\Models\TunjanganType;
use App\Models\TunjanganDetail;
use App\Models\Absen;
use App\Models\Penalti;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class GenerateUangMakan extends Command
{
    protected $signature = 'tunjangan:generate-makan {--week-start=}';

    protected $description = 'Generate uang makan mingguan dengan delay system (auto setiap Senin)';

    public function handle()
    {
        $this->info('🚀 Memulai generate uang makan mingguan...');

        // Tentukan periode minggu
        if ($this->option('week-start')) {
            $weekStart = Carbon::parse($this->option('week-start'))->startOfWeek(Carbon::MONDAY);
        } else {
            // Default: minggu kemarin (Senin-Minggu)
            $weekStart = Carbon::now()->subWeek()->startOfWeek(Carbon::MONDAY);
        }

        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        $this->info("📅 Periode: {$weekStart->format('d/m/Y')} - {$weekEnd->format('d/m/Y')}");
        $this->newLine();

        // Get tunjangan type UANG_MAKAN
        $tunjanganType = TunjanganType::where('code', 'UANG_MAKAN')
            ->where('is_active', true)
            ->first();

        if (!$tunjanganType) {
            $this->error('❌ Tunjangan type UANG_MAKAN tidak ditemukan!');
            return Command::FAILURE;
        }

        // Ambil semua karyawan aktif (exclude admin)
        $karyawans = Karyawan::where('employment_status', 'active')
            ->whereNotIn('karyawan_id', ['KAR001', 'KAR010'])
            ->with(['user', 'department'])
            ->get();

        $this->info("👥 Total karyawan: {$karyawans->count()}");
        $this->newLine();

        $successCount = 0;
        $skipCount = 0;
        $errorCount = 0;

        foreach ($karyawans as $karyawan) {
            try {
                // Cek apakah sudah ada tunjangan untuk periode ini
                $exists = TunjanganKaryawan::where('karyawan_id', $karyawan->karyawan_id)
                    ->where('tunjangan_type_id', $tunjanganType->tunjangan_type_id)
                    ->where('period_start', $weekStart->format('Y-m-d'))
                    ->where('period_end', $weekEnd->format('Y-m-d'))
                    ->exists();

                if ($exists) {
                    $this->warn("⏭️  Skip: {$karyawan->full_name} - Sudah ada");
                    $skipCount++;
                    continue;
                }

                // ✅ HITUNG HARI KERJA ASLI (yang ada clock_in)
                $hariKerjaAsli = Absen::where('karyawan_id', $karyawan->karyawan_id)
                    ->whereBetween('date', [$weekStart, $weekEnd])
                    ->whereNotNull('clock_in')
                    ->where('type', '!=', 'oncall')
                    ->count();

                if ($hariKerjaAsli === 0) {
                    $this->line("⏭️  Skip: {$karyawan->full_name} - Tidak ada hari kerja");
                    $skipCount++;
                    continue;
                }

                // ✅ HITUNG PENALTI
                $hariPotongPenalti = Penalti::getTotalHariPotongan(
                    $karyawan->karyawan_id,
                    $weekStart->format('Y-m-d'),
                    $weekEnd->format('Y-m-d')
                );

                // ✅ HITUNG DELAY (hari tidak logout/clock_out)
                $hariTidakLogout = Absen::where('karyawan_id', $karyawan->karyawan_id)
                    ->whereBetween('date', [$weekStart, $weekEnd])
                    ->whereNotNull('clock_in')
                    ->whereNull('clock_out')
                    ->where('type', '!=', 'oncall')
                    ->count();

                $delayDays = $hariTidakLogout;
                $availableRequestDate = Carbon::parse($weekEnd)->addDays($delayDays);

                // Get amount berdasarkan staff_status
                $amount = TunjanganDetail::getAmountByStaffStatus(
                    $tunjanganType->tunjangan_type_id,
                    $karyawan->staff_status
                );

                if ($amount <= 0) {
                    $this->error("❌ {$karyawan->full_name} - Nominal tidak ditemukan untuk {$karyawan->staff_status}");
                    $errorCount++;
                    continue;
                }

                // ✅ CREATE TUNJANGAN dengan DELAY SYSTEM
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

                // Display info
                $delayInfo = $delayDays > 0 ? " | ⏰ Delay: {$delayDays} hari (bisa request: {$availableRequestDate->format('d/m/Y')})" : "";
                $penaltiInfo = $hariPotongPenalti > 0 ? " | ⚠️ Penalti: -{$hariPotongPenalti} hari" : "";

                $this->info("✅ {$karyawan->full_name} - {$hariKerjaAsli} hari - Rp " . number_format($tunjangan->total_amount, 0, ',', '.') . $delayInfo . $penaltiInfo);
                $successCount++;

                // Log
                Log::info("Uang makan generated with delay", [
                    'karyawan_id' => $karyawan->karyawan_id,
                    'name' => $karyawan->full_name,
                    'hari_kerja_asli' => $hariKerjaAsli,
                    'hari_tidak_logout' => $hariTidakLogout,
                    'delay_days' => $delayDays,
                    'available_date' => $availableRequestDate->format('Y-m-d'),
                    'hari_potong_penalti' => $hariPotongPenalti,
                    'total_amount' => $tunjangan->total_amount,
                    'period' => "{$weekStart->format('Y-m-d')} - {$weekEnd->format('Y-m-d')}",
                ]);

            } catch (\Exception $e) {
                $this->error("❌ Error: {$karyawan->full_name} - {$e->getMessage()}");
                $errorCount++;

                Log::error("Generate uang makan error", [
                    'karyawan_id' => $karyawan->karyawan_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        // Summary
        $this->newLine();
        $this->info("📊 SUMMARY:");
        $this->info("✅ Berhasil: {$successCount}");
        $this->warn("⏭️  Di-skip: {$skipCount}");
        $this->error("❌ Gagal: {$errorCount}");
        $this->info("🎉 Selesai!");

        return Command::SUCCESS;
    }
}

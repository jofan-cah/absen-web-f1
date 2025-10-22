<?php



namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Karyawan;
use App\Models\TunjanganKaryawan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GenerateUangKuota extends Command
{
    protected $signature = 'tunjangan:generate-kuota {--month=} {--year=}';

    protected $description = 'Generate uang kuota untuk semua karyawan yang berhak';

    public function handle()
    {
        $this->info('🚀 Memulai generate uang kuota...');

        // Ambil bulan dan tahun (default: bulan sekarang)
        $month = $this->option('month') ?? now()->month;
        $year = $this->option('year') ?? now()->year;

        $this->info("📅 Periode: {$month}/{$year}");

        // Ambil semua karyawan yang berhak dapat uang kuota
        $karyawans = Karyawan::where('uang_kuota', true)
            ->where('employment_status', 'active')
            ->whereNotIn('karyawan_id', ['KAR001', 'KAR010']) // Exclude admin
            ->with(['user', 'department'])
            ->get();

        $this->info("👥 Total karyawan yang berhak: {$karyawans->count()}");

        $successCount = 0;
        $skipCount = 0;
        $errorCount = 0;

        foreach ($karyawans as $karyawan) {
            try {
                // Cek apakah sudah ada tunjangan kuota untuk bulan ini
                $exists = TunjanganKaryawan::where('karyawan_id', $karyawan->karyawan_id)
                    ->whereHas('tunjanganType', function ($query) {
                        $query->where('code', 'UANG_KUOTA');
                    })
                    ->whereYear('period_start', $year)
                    ->whereMonth('period_start', $month)
                    ->exists();

                if ($exists) {
                    $this->warn("⚠️  Skip: {$karyawan->full_name} (NIP: {$karyawan->nip}) - Sudah ada");
                    $skipCount++;
                    continue;
                }

                // Generate tunjangan kuota
                $tunjangan = TunjanganKaryawan::generateTunjanganKuota(
                    $karyawan->karyawan_id,
                    $month,
                    $year
                );

                if ($tunjangan) {
                    $this->info("✅ Berhasil: {$karyawan->full_name} (NIP: {$karyawan->nip}) - Rp " . number_format($tunjangan->total_amount, 0, ',', '.'));
                    $successCount++;

                    // Log ke database
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
                    $errorCount++;
                }

            } catch (\Exception $e) {
                $this->error("❌ Error: {$karyawan->full_name} - {$e->getMessage()}");
                $errorCount++;

                Log::error("Generate uang kuota error", [
                    'karyawan_id' => $karyawan->karyawan_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Summary
        $this->newLine();
        $this->info("📊 SUMMARY:");
        $this->info("✅ Berhasil: {$successCount}");
        $this->warn("⚠️  Di-skip: {$skipCount}");
        $this->error("❌ Gagal: {$errorCount}");
        $this->info("🎉 Selesai!");

        return Command::SUCCESS;
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OnCallShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cek apakah shift OnCall sudah ada
        $existingShift = DB::table('shifts')
            ->where('shift_id', 'SHIFT-ONCALL')
            ->first();

        if ($existingShift) {
            $this->command->warn('⚠️  Shift OnCall sudah ada, skip insert.');
            return;
        }

        // Insert shift khusus OnCall (SESUAI KOLOM ASLI)
        DB::table('shifts')->insert([
            'shift_id' => 'SHIFT-ONCALL',
            'name' => 'OnCall Shift',
            'code' => 'ONCALL',
            'start_time' => '00:00:00',
            'end_time' => '23:59:59',
            'break_start' => null, // OnCall gak ada break formal
            'break_end' => null,
            'break_duration' => 0,
            'late_tolerance' => 0, // Gak ada konsep telat
            'early_checkout_tolerance' => 0,
            'is_overnight' => true, // Bisa lintas hari (misal 23:00-03:00)
            'is_active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $this->command->info('✅ Shift OnCall berhasil dibuat!');
        $this->command->info('   Shift ID: SHIFT-ONCALL');
        $this->command->info('   Shift Code: ONCALL');
        $this->command->info('   Name: OnCall Shift');
        $this->command->info('   Time: 00:00:00 - 23:59:59 (24 jam fleksibel)');
        $this->command->info('   Is Overnight: Yes (bisa lintas hari)');
    }
}

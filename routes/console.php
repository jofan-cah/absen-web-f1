<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


// Cek clock in setiap 15 menit (06:00 - 22:00)
Schedule::command('notif:check-absen --type=clock_in')
    ->everyFifteenMinutes()
    ->between('06:00', '22:00');

Schedule::command('notif:check-absen --type=clock_out')
    ->everyFifteenMinutes()
    ->when(function () {
        $hour = now()->format('H');
        return $hour >= 12 || $hour < 4; // 12:00–23:59 atau 00:00–03:59
    });

// Cek absent sekali di malam (22:00)
Schedule::command('notif:check-absen --type=absent')
    ->dailyAt('22:00');

// Generate uang kuota setiap Senin pertama di bulan berjalan
Schedule::command('tunjangan:generate-kuota')
    ->monthlyOn(1, '00:01') // Tanggal 1 setiap bulan jam 00:01
    ->when(function () {
        // Cek apakah tanggal 1 adalah hari Senin
        return now()->day === 1 && now()->dayOfWeek === Carbon::MONDAY;
    })
    ->onSuccess(function () {
        Log::info('✅ Scheduled job: Generate uang kuota berhasil dijalankan');
    })
    ->onFailure(function () {
        Log::error('❌ Scheduled job: Generate uang kuota gagal');
    });


// Check absen clock in (TAMBAH INI)
Schedule::command('notif:check-absen --type=clock_in')
    ->everyFifteenMinutes()
    ->between('06:00', '22:00');

// Check absen clock out (TAMBAH INI)
Schedule::command('notif:check-absen --type=clock_out')
    ->everyFifteenMinutes()
    ->between('12:00', '04:00');

// Check absent (TAMBAH INI)
Schedule::command('notif:check-absen --type=absent')
    ->dailyAt('22:00');


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

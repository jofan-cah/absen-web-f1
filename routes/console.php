<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

// ================================
// NOTIFIKASI ABSENSI
// ================================

// Cek clock in setiap 15 menit (06:00 - 22:00)
Schedule::command('notif:check-absen --type=clock_in')
    ->everyFifteenMinutes()
    ->between('06:00', '22:00')
    ->onSuccess(function () {
        Log::info('✅ Check clock_in berhasil dijalankan');
    })
    ->onFailure(function () {
        Log::error('❌ Check clock_in gagal');
    });

// Cek clock out setiap 15 menit (12:00-23:59 dan 00:00-03:59)
Schedule::command('notif:check-absen --type=clock_out')
    ->everyFifteenMinutes()
    ->when(function () {
        $hour = (int) now()->format('H');
        // Jalan antara jam 12:00-23:59 ATAU 00:00-03:59
        return $hour >= 12 || $hour < 4;
    })
    ->onSuccess(function () {
        Log::info('✅ Check clock_out berhasil dijalankan');
    })
    ->onFailure(function () {
        Log::error('❌ Check clock_out gagal');
    });

// Cek absent sekali di malam hari (22:00)
Schedule::command('notif:check-absen --type=absent')
    ->dailyAt('22:00')
    ->onSuccess(function () {
        Log::info('✅ Check absent berhasil dijalankan');
    })
    ->onFailure(function () {
        Log::error('❌ Check absent gagal');
    });

// ================================
// GENERATE TUNJANGAN
// ================================

// Generate uang kuota setiap tanggal 1 yang jatuh di hari Senin
Schedule::command('tunjangan:generate-kuota')
    ->monthlyOn(1, '00:01')
    ->when(function () {
        // Hanya jalan jika tanggal 1 adalah hari Senin
        return now()->dayOfWeek === Carbon::MONDAY;
    })
    ->onSuccess(function () {
        Log::info('✅ Generate uang kuota berhasil dijalankan');
    })
    ->onFailure(function () {
        Log::error('❌ Generate uang kuota gagal');
    });

// ================================
// ARTISAN COMMANDS
// ================================

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

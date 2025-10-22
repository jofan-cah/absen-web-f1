<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

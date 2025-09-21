<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\JadwalController;
use App\Http\Controllers\Api\AbsenController;
use App\Http\Controllers\Api\RiwayatController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// ============================================
// PUBLIC ROUTES (No Authentication Required)
// ============================================

// Test endpoint
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is working',
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
})->middleware('throttle:5,1');

// Authentication
Route::post('/login', [AuthController::class, 'login']);

// ============================================
// PROTECTED ROUTES (Authentication Required)
// ============================================

Route::middleware(['auth:sanctum','throttle:500,1'])->group(function () {

    // ========================================
    // AUTH & USER MANAGEMENT
    // ========================================
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // ========================================
    // DASHBOARD
    // ========================================
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/notifications', [DashboardController::class, 'notifications']);

    // ========================================
    // PROFILE MANAGEMENT
    // ========================================
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::post('/profile/photo', [ProfileController::class, 'uploadPhoto']);
    Route::get('/profile/stats', [ProfileController::class, 'stats']);

    // ========================================
    // JADWAL MANAGEMENT
    // ========================================
    Route::prefix('jadwal')->group(function () {
        Route::get('/', [JadwalController::class, 'index']); // Monthly jadwal
        Route::get('/weekly', [JadwalController::class, 'weekly']); // Weekly jadwal
        Route::get('/today', [JadwalController::class, 'today']); // Jadwal hari ini
        Route::get('/tomorrow', [JadwalController::class, 'tomorrow']); // Jadwal besok
        Route::get('/range', [JadwalController::class, 'byDateRange']); // By date range
    });

    // ========================================
    // ABSENSI MANAGEMENT
    // ========================================
    Route::prefix('absen')->group(function () {
        Route::get('/today', [AbsenController::class, 'today']); // Status absen hari ini
        Route::post('/clock-in', [AbsenController::class, 'clockIn']); // Clock in
        Route::post('/clock-out', [AbsenController::class, 'clockOut']); // Clock out
        Route::get('/history', [AbsenController::class, 'history']); // Riwayat simple
    });

    // ========================================
    // RIWAYAT MANAGEMENT
    // ========================================
    Route::prefix('riwayat')->group(function () {
        Route::get('/absen', [RiwayatController::class, 'absen']); // Riwayat absen bulanan
        Route::get('/jadwal', [RiwayatController::class, 'jadwal']); // Riwayat jadwal bulanan
        Route::get('/detail/{date}', [RiwayatController::class, 'detail']); // Detail harian
        Route::get('/yearly', [RiwayatController::class, 'yearly']); // Summary tahunan
        Route::get('/photos', [RiwayatController::class, 'photos']); // Galeri foto absen
    });

});

// ============================================
// DEVELOPMENT ROUTES (Only for local/testing)
// ============================================

if (app()->environment('local', 'staging')) {
    Route::get('/dev/user/{userId}', function ($userId) {
        $user = \App\Models\User::with('karyawan.department')->find($userId);
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    });

    Route::get('/dev/clear-tokens', function () {
        \Laravel\Sanctum\PersonalAccessToken::truncate();
        return response()->json([
            'success' => true,
            'message' => 'All tokens cleared'
        ]);
    });
}

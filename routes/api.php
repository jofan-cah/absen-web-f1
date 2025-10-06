<?php

use App\Http\Controllers\Api\AbsenController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\IjinController;
use App\Http\Controllers\Api\JadwalController;
use App\Http\Controllers\Api\LemburController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RiwayatController;
use App\Http\Controllers\Api\TunjanganController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
        'version' => '1.0.0',
    ]);
})->middleware('throttle:5,1');

// Authentication
Route::post('/login', [AuthController::class, 'login']);

// ============================================
// PROTECTED ROUTES (Authentication Required)
// ============================================

Route::middleware(['auth:sanctum', 'throttle:500,1'])->group(function () {

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

    Route::prefix('lembur')->group(function () {
        Route::get('/my-list', [LemburController::class, 'myList']); // List lembur karyawan
        Route::get('/summary', [LemburController::class, 'summary']); // Summary lembur
        Route::get('/{id}', [LemburController::class, 'show']); // Detail lembur
        Route::post('/submit', [LemburController::class, 'store']); // Submit lembur baru
        Route::put('/{id}', [LemburController::class, 'update']); // Update lembur
        Route::delete('/{id}', [LemburController::class, 'destroy']); // Delete lembur
        Route::post('/{id}/submit', [LemburController::class, 'submitForApproval']); // Submit untuk approval
    });

    // ========================================
    // TUNJANGAN MANAGEMENT (NEW - BELUM ADA)
    // ========================================
    Route::prefix('tunjangan')->group(function () {
        // Report by Type
        Route::get('/uang-makan/report', [TunjanganController::class, 'uangMakanReport']);
        Route::get('/uang-kuota/report', [TunjanganController::class, 'uangKuotaReport']);
        Route::get('/uang-lembur/report', [TunjanganController::class, 'uangLemburReport']);

        // All Tunjangan
        Route::get('/my-list', [TunjanganController::class, 'myList']); // List semua tunjangan
        Route::get('/summary', [TunjanganController::class, 'summary']); // Summary tunjangan
        Route::get('/{id}', [TunjanganController::class, 'show']); // Detail tunjangan

        // Workflow Actions
        Route::post('/{id}/request', [TunjanganController::class, 'requestTunjangan']); // Request pencairan
        Route::post('/{id}/confirm-received', [TunjanganController::class, 'confirmReceived']); // Konfirmasi terima
    });

    // ============================
    // 📂 Ijin Routes
    // ============================
    Route::prefix('ijin')->group(function () {
        // 📋 Dropdown & Data Tersedia
        Route::get('/types', [IjinController::class, 'getIjinTypes'])
            ->name('ijin.types');
        Route::get('/available-piket-dates', [IjinController::class, 'getAvailablePiketDates'])
            ->name('ijin.available-piket-dates');

        // 🧑‍💼 User-specific Data
        Route::get('/my-history', [IjinController::class, 'myHistory'])
            ->name('ijin.my-history');
        Route::get('/{id}', [IjinController::class, 'show'])
            ->name('ijin.show');

        // 📝 Submission Endpoints
        Route::post('/submit', [IjinController::class, 'submitIjin'])
            ->name('ijin.submit'); // Sakit, Cuti, Pribadi
        Route::post('/shift-swap', [IjinController::class, 'submitShiftSwap'])
            ->name('ijin.shift-swap');
        Route::post('/compensation-leave', [IjinController::class, 'submitCompensationLeave'])
            ->name('ijin.compensation-leave');

        // ❌ Cancel (Pending Only)
        Route::delete('/{id}', [IjinController::class, 'cancel'])
            ->name('ijin.cancel');
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
            'data' => $user,
        ]);
    });

    Route::get('/dev/clear-tokens', function () {
        \Laravel\Sanctum\PersonalAccessToken::truncate();

        return response()->json([
            'success' => true,
            'message' => 'All tokens cleared',
        ]);
    });
}

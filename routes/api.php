<?php

use App\Http\Controllers\Api\AbsenController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\IjinController;
use App\Http\Controllers\Api\JadwalController;
use App\Http\Controllers\Api\LemburController;
use App\Http\Controllers\Api\OncallController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RiwayatController;
use App\Http\Controllers\Api\ShiftSwapController;
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

// Test ActivityLog endpoint
Route::get('/test-activity-log', function () {
    $results = [];

    // 1. Check table exists
    $tableExists = \Illuminate\Support\Facades\Schema::hasTable('activity_logs');
    $results['table_exists'] = $tableExists;

    if (!$tableExists) {
        return response()->json([
            'success' => false,
            'message' => 'Table activity_logs not exists. Run: php artisan migrate',
            'results' => $results,
        ]);
    }

    // 2. Try insert
    try {
        $log = \App\Models\ActivityLog::create([
            'action' => 'other',
            'description' => 'Test from API endpoint',
            'ip_address' => request()->ip(),
            'platform' => 'api',
        ]);
        $results['insert_direct'] = 'OK, ID: ' . $log->id;
    } catch (\Exception $e) {
        $results['insert_direct'] = 'ERROR: ' . $e->getMessage();
    }

    // 3. Try logLoginFailed
    try {
        $log = \App\Models\ActivityLog::logLoginFailed('TEST_NIP', 'Test from API');
        $results['logLoginFailed'] = $log ? 'OK, ID: ' . $log->id : 'NULL returned';
    } catch (\Exception $e) {
        $results['logLoginFailed'] = 'ERROR: ' . $e->getMessage();
    }

    // 4. Count
    $results['total_logs'] = \App\Models\ActivityLog::count();

    return response()->json([
        'success' => true,
        'message' => 'ActivityLog test completed',
        'results' => $results,
    ]);
});


Route::get('/app-version', function () {
    return response()->json([
        'success' => true,
        'data' => [
            'minimum_version' => '1.4.0',  // Versi minimum yang harus dipakai
            'latest_version' => '1.4.0',    // Versi terbaru
            'force_update' => true,         // true = wajib update, false = optional
            'message' => 'Aplikasi Anda perlu diupdate ke versi terbaru'
        ]
    ]);
});


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
        // List & Summary
        Route::get('/my-list', [LemburController::class, 'myList']);
        Route::get('/summary', [LemburController::class, 'summary']);
        Route::get('/form-info/{absenId}', [LemburController::class, 'getFormInfo']);
        Route::get('/{id}', [LemburController::class, 'show']);

        // 🆕 OPSI 3 - HYBRID FLOW (RECOMMENDED)
        Route::post('/start', [LemburController::class, 'start']);                    // Mulai lembur
        Route::post('/{id}/finish', [LemburController::class, 'finish']);             // Selesai lembur
        Route::post('/{id}/submit', [LemburController::class, 'submitForApproval']);  // Submit
        Route::post('/{id}/update-photo', [LemburController::class, 'updatePhoto']);
        // ✅ BACKWARD COMPATIBILITY (untuk app lama yang belum update)
        // Route::post('/submit', [LemburController::class, 'store']);                   // Versi lama

        // Update & Delete
        Route::put('/{id}', [LemburController::class, 'update']);
        Route::delete('/{id}', [LemburController::class, 'destroy']);
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


    // Device Token Management (FCM)
    Route::prefix('device')->group(function () {
        Route::post('/device-token', [App\Http\Controllers\Api\DeviceTokenController::class, 'store']);
        Route::get('/device-tokens', [App\Http\Controllers\Api\DeviceTokenController::class, 'index']);
        Route::delete('/device-token', [App\Http\Controllers\Api\DeviceTokenController::class, 'destroy']);
        Route::post('/device-token/activate', [App\Http\Controllers\Api\DeviceTokenController::class, 'activate']);
        Route::post('/device-token/deactivate', [App\Http\Controllers\Api\DeviceTokenController::class, 'deactivate']);
    });

    // Notification Management
    Route::prefix('notif')->group(function () {
        // Notifications
        Route::get('/notifications', [App\Http\Controllers\Api\NotificationController::class, 'index']);
        Route::get('/notifications/unread', [App\Http\Controllers\Api\NotificationController::class, 'unread']);
        Route::get('/notifications/unread-count', [App\Http\Controllers\Api\NotificationController::class, 'unreadCount']);
        Route::get('/notifications/{id}', [App\Http\Controllers\Api\NotificationController::class, 'show']);
        Route::post('/notifications/{id}/read', [App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
        Route::post('/notifications/read-all', [App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead']);
        Route::delete('/notifications/{id}', [App\Http\Controllers\Api\NotificationController::class, 'destroy']);
        Route::delete('/notifications/clear-read', [App\Http\Controllers\Api\NotificationController::class, 'clearRead']);
    });

    // ========================================
    // ONCALL LEMBUR (Screen Terpisah)
    // ========================================
    Route::prefix('oncall')->group(function () {
        Route::get('/today', [OncallController::class, 'today']);           // Cek jadwal oncall hari ini
        Route::post('/clock-in', [OncallController::class, 'clockIn']);     // Clock in oncall
        Route::put('/{id}/report', [OncallController::class, 'updateReport']); // Isi/update laporan
        Route::post('/clock-out', [OncallController::class, 'clockOut']);   // Clock out + submit
        Route::get('/my-list', [OncallController::class, 'myList']);        // List oncall saya
        Route::get('/{id}', [OncallController::class, 'show']);             // Detail oncall
    });

    Route::prefix('shift-swap')->group(function () {

    // Create swap request
    Route::post('/request', [ShiftSwapController::class, 'createRequest']);

    // Respond to swap request (approve/reject)
    Route::post('/respond/{swap_id}', [ShiftSwapController::class, 'respondToRequest']);

    // Cancel swap request
    Route::post('/cancel/{swap_id}', [ShiftSwapController::class, 'cancelRequest']);

    // Get swap history
    Route::get('/history', [ShiftSwapController::class, 'getHistory']);

    // Get pending requests (yang perlu di-approve user)
    Route::get('/pending', [ShiftSwapController::class, 'getPendingRequests']);

    // Get available jadwals for swap
    Route::get('/available-jadwals', [ShiftSwapController::class, 'getAvailableJadwals']);
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

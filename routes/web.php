<?php

use App\Http\Controllers\Admin\AbsenController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\IjinController;
use App\Http\Controllers\Admin\IjinTypeController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\KaryawanController;
use App\Http\Controllers\Admin\LemburController;
use App\Http\Controllers\Admin\LiburController;
use App\Http\Controllers\Admin\OnCallController;
use App\Http\Controllers\Admin\PenaltiController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\Admin\ShiftSwapAdminController;
use App\Http\Controllers\Admin\TunjanganDetailController;
use App\Http\Controllers\Admin\TunjanganKaryawanController;
use App\Http\Controllers\Admin\TunjanganTypeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Koordinator\LemburKoorController;
use App\Http\Controllers\ActivityLogController;
use App\Models\EventAttendance;
use Illuminate\Support\Facades\Route;

// Redirect root to admin login
Route::get('/', function () {
    return redirect('/login');
});

// Privacy Policy (Public - untuk mobile app WebView)
Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy-policy');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {

    // Authenticated routes
    Route::middleware(['auth'])->group(function () {

        // Profile & Logout - Semua role bisa akses
        Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
        Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        // Dashboard - Semua role bisa akses
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Jadwal Calendar - Admin & Coordinator bisa akses
        Route::middleware(['role:admin,coordinator'])->group(function () {
            Route::get('/jadwal/calendar', [JadwalController::class, 'calendar'])->name('jadwal.calendar');
                Route::get('/jadwal/export-pdf', [JadwalController::class, 'exportPdf'])->name('jadwal.export-pdf'); // ← TAMBAH INI
            Route::post('/jadwal/bulk-store', [JadwalController::class, 'bulkStore'])->name('jadwal.bulk-store');
            Route::get('/jadwal/{jadwal}/check-editable', [JadwalController::class, 'checkEditable'])->name('jadwal.check-editable');

            // CRUD Jadwal untuk coordinator
            Route::post('/jadwal', [JadwalController::class, 'store'])->name('jadwal.store');
            Route::put('/jadwal/{jadwal}', [JadwalController::class, 'update'])->name('jadwal.update');
            Route::delete('/jadwal/{jadwal}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');
        });


        Route::middleware(['role:admin,coordinator'])->prefix('oncall')->name('oncall.')->group(function () {
            Route::get('/', [OnCallController::class, 'index'])->name('index');
            Route::get('/create', [OnCallController::class, 'create'])->name('create');
            Route::post('/', [OnCallController::class, 'store'])->name('store');
            Route::get('/{id}', [OnCallController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [OnCallController::class, 'edit'])->name('edit');
            Route::put('/{id}', [OnCallController::class, 'update'])->name('update');
            Route::delete('/{id}', [OnCallController::class, 'destroy'])->name('destroy');
        });

        Route::middleware(['role:admin,coordinator'])->prefix('shift-swap')->name('shift-swap.')->group(function () {
            Route::get('/', [ShiftSwapAdminController::class, 'index'])->name('indexSw');
            Route::get('/history', [ShiftSwapAdminController::class, 'history'])->name('historySw');
            Route::get('/{swap_id}', [ShiftSwapAdminController::class, 'show'])->name('showSw');
            Route::post('/{swap_id}/approve', [ShiftSwapAdminController::class, 'approve'])->name('approveSw');
            Route::post('/{swap_id}/reject', [ShiftSwapAdminController::class, 'reject'])->name('rejectSw');
        });



        // Routes yang HANYA bisa diakses ADMIN
        Route::middleware(['role:admin'])->group(function () {

            // ─── Event Management ─────────────────────────────────────────────
            Route::resource('event', EventController::class);
            Route::prefix('event')->name('event.')->group(function () {
                Route::post('/{event}/update-status',              [EventController::class, 'updateStatus'])->name('update-status');
                Route::get('/{event}/qr',                          [EventController::class, 'showQr'])->name('qr');
                Route::get('/{event}/qr-otp',                      [EventController::class, 'generateQrOtp'])->name('qr-otp');
                Route::get('/{event}/scan',                        [EventController::class, 'scanPage'])->name('scan');
                Route::post('/{event}/scan',                       [EventController::class, 'processScan'])->name('process-scan');
                Route::get('/{event}/manual',                      [EventController::class, 'manualPage'])->name('manual');
                Route::post('/{event}/manual',                     [EventController::class, 'processManual'])->name('process-manual');
                Route::delete('/{event}/attendance/{attendance}',  [EventController::class, 'removeAttendance'])->name('remove-attendance');
                Route::get('/{event}/pdf',                         [EventController::class, 'downloadPdf'])->name('pdf');
                Route::get('/{event}/pdf-preview',                 [EventController::class, 'previewPdf'])->name('pdf-preview');
            });

            // Department Management
            Route::resource('department', DepartmentController::class);
            Route::get('/department/{department}/karyawans', [DepartmentController::class, 'getKaryawans'])->name('department.karyawans');
            Route::post('/department/{department}/toggle-status', [DepartmentController::class, 'toggleStatus'])->name('department.toggle-status');
            Route::post('department/bulk-delete', [DepartmentController::class, 'bulkDelete'])->name('department.bulk-delete');
            Route::post('department/bulk-toggle-status', [DepartmentController::class, 'bulkToggleStatus'])->name('department.bulk-toggle-status');
            Route::get('department/export', [DepartmentController::class, 'export'])->name('department.export');

            // Karyawan Management
            Route::resource('karyawan', KaryawanController::class);
            Route::post('karyawan/bulk-delete', [KaryawanController::class, 'bulkDelete'])->name('karyawan.bulk-delete');
            Route::post('karyawan/{id}/toggle-status', [KaryawanController::class, 'toggleStatus'])->name('karyawan.toggle-status');
            Route::post('karyawan/{id}/reset-password', [KaryawanController::class, 'resetPassword'])->name('karyawan.reset-password');
            Route::match(['get', 'post'], 'karyawan/export', [KaryawanController::class, 'export'])->name('karyawan.export');

            // Shift Management
            Route::resource('shift', ShiftController::class);
            Route::get('/shift-active/list', [ShiftController::class, 'getActiveShifts'])->name('shift.active');
            Route::post('/shift/{shift}/toggle-status', [ShiftController::class, 'toggleStatus'])->name('shift.toggle-status');
            Route::post('shift/bulk-delete', [ShiftController::class, 'bulkDelete'])->name('shift.bulk-delete');
            Route::post('shift/bulk-toggle-status', [ShiftController::class, 'bulkToggleStatus'])->name('shift.bulk-toggle-status');
            Route::get('shift/export', [ShiftController::class, 'export'])->name('shift.export');

            // Libur Management
            Route::resource('libur', LiburController::class);
            Route::post('libur/{libur}/toggle-status', [LiburController::class, 'toggleStatus'])->name('libur.toggle-status');
            Route::post('libur/bulk-delete', [LiburController::class, 'bulkDelete'])->name('libur.bulk-delete');
            Route::post('libur/bulk-toggle-status', [LiburController::class, 'bulkToggleStatus'])->name('libur.bulk-toggle-status');

            // Jadwal Management (Admin only routes)
            Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');

            // Absen Management & Reports
            Route::get('/absen/report', [AbsenController::class, 'report'])->name('absen.report');
            Route::get('/absen/daily-report', [AbsenController::class, 'dailyReport'])->name('absen.daily-report');
            Route::get('/absen/export-report', [AbsenController::class, 'exportReport'])->name('absen.export-report');
            Route::get('/absen/export-pdf-report', [AbsenController::class, 'exportPdfReport'])->name('absen.export-pdf-report');
            Route::resource('absen', AbsenController::class)->only(['index', 'show']);

            // User Management
            Route::resource('user', UserController::class);
            Route::post('user/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('user.toggle-status');
            Route::post('user/{user}/reset-password', [UserController::class, 'resetPassword'])->name('user.reset-password');
            Route::post('user/bulk-delete', [UserController::class, 'bulkDelete'])->name('user.bulk-delete');
            Route::post('user/bulk-toggle-status', [UserController::class, 'bulkToggleStatus'])->name('user.bulk-toggle-status');
            Route::get('user/export', [UserController::class, 'export'])->name('user.export');

            // Tunjangan Type
            Route::resource('tunjangan-type', TunjanganTypeController::class);
            Route::post('tunjangan-type/{tunjanganType}/toggle-status', [TunjanganTypeController::class, 'toggleStatus'])->name('tunjangan-type.toggle-status');
            Route::post('tunjangan-type/bulk-delete', [TunjanganTypeController::class, 'bulkDelete'])->name('tunjangan-type.bulk-delete');
            Route::post('tunjangan-type/bulk-toggle-status', [TunjanganTypeController::class, 'bulkToggleStatus'])->name('tunjangan-type.bulk-toggle-status');
            Route::get('tunjangan-type/export', [TunjanganTypeController::class, 'export'])->name('tunjangan-type.export');

            // Tunjangan Detail
            Route::resource('tunjangan-detail', TunjanganDetailController::class);
            Route::post('tunjangan-detail/{tunjanganDetail}/toggle-status', [TunjanganDetailController::class, 'toggleStatus'])->name('tunjangan-detail.toggle-status');
            Route::post('tunjangan-detail/bulk-delete', [TunjanganDetailController::class, 'bulkDelete'])->name('tunjangan-detail.bulk-delete');
            Route::get('tunjangan-detail/get-amount', [TunjanganDetailController::class, 'getAmountByStaffStatus'])->name('tunjangan-detail.get-amount');
            Route::post('tunjangan-detail/bulk-toggle-status', [TunjanganDetailController::class, 'bulkToggleStatus'])->name('tunjangan-detail.bulk-toggle-status');
            Route::post('tunjangan-detail/export', [TunjanganDetailController::class, 'export'])->name('tunjangan-detail.export');

            // Penalti
            Route::resource('penalti', PenaltiController::class);
            Route::post('penalti/{penalti}/approve', [PenaltiController::class, 'approve'])->name('penalti.approve');
            Route::post('penalti/{penalti}/change-status', [PenaltiController::class, 'changeStatus'])->name('penalti.change-status');
            Route::post('penalti/bulk-delete', [PenaltiController::class, 'bulkDelete'])->name('penalti.bulk-delete');
            Route::get('penalti/total-potongan', [PenaltiController::class, 'getTotalHariPotongan'])->name('penalti.total-potongan');
            Route::post('penalti/bulk-change-status', [PenaltiController::class, 'changeStatus'])->name('penalti.bulk-change-status');
            Route::post('penalti/export', [PenaltiController::class, 'export'])->name('penalti.export');

            // Tunjangan Karyawan
            Route::prefix('tunjangan-karyawan')->name('tunjangan-karyawan.')->group(function () {
                Route::get('/', [TunjanganKaryawanController::class, 'index'])->name('index');
                Route::get('/generate/form', [TunjanganKaryawanController::class, 'generateForm'])->name('generate.form');
                Route::get('/report-form', [TunjanganKaryawanController::class, 'reportForm'])->name('report-form');
                Route::get('/single-week-form', [TunjanganKaryawanController::class, 'singleWeekReportForm'])->name('single-week-form');
                Route::post('/single-week-report', [TunjanganKaryawanController::class, 'generateSingleWeekReport'])->name('single-week-report');
                Route::get('/report/analytics', [TunjanganKaryawanController::class, 'report'])->name('report');
                Route::get('/export/data', [TunjanganKaryawanController::class, 'export'])->name('export');
                Route::post('/generate', [TunjanganKaryawanController::class, 'generateTunjangan'])->name('generate');
                Route::post('/all-employee-report', [TunjanganKaryawanController::class, 'allEmployeeReport'])->name('all-employee-report');
                Route::post('/bulk-delete', [TunjanganKaryawanController::class, 'bulkDelete'])->name('bulk-delete');
                Route::post('/bulk-approve', [TunjanganKaryawanController::class, 'bulkApprove'])->name('bulk-approve');
                Route::get('/{tunjanganKaryawan}', [TunjanganKaryawanController::class, 'show'])->name('show');
                Route::post('/{tunjanganKaryawan}/request', [TunjanganKaryawanController::class, 'requestTunjangan'])->name('request');
                Route::post('/{tunjanganKaryawan}/approve', [TunjanganKaryawanController::class, 'approveTunjangan'])->name('approve');
                Route::post('/{tunjanganKaryawan}/confirm', [TunjanganKaryawanController::class, 'confirmReceived'])->name('confirm');
                Route::post('/{tunjanganKaryawan}/apply-penalti', [TunjanganKaryawanController::class, 'applyPenalti'])->name('apply-penalti');
                Route::delete('/{tunjanganKaryawan}', [TunjanganKaryawanController::class, 'destroy'])->name('destroy');
            });

            // Lembur
            Route::prefix('lembur')->name('lembur.')->group(function () {
                Route::get('/', [LemburController::class, 'index'])->name('index');
                Route::get('/{lembur}', [LemburController::class, 'show'])->name('show');
                Route::post('/{lembur}/approve', [LemburController::class, 'approve'])->name('approve');
                Route::post('/{lembur}/reject', [LemburController::class, 'reject'])->name('reject');
                Route::post('/bulk-approve', [LemburController::class, 'bulkApprove'])->name('bulk-approve');
                Route::post('/bulk-delete', [LemburController::class, 'bulkDelete'])->name('bulk-delete');
                Route::get('/generate-tunjangan/form', [LemburController::class, 'generateTunjanganForm'])->name('generate-tunjangan.form');
                Route::post('/generate-tunjangan/mingguan', [LemburController::class, 'generateTunjanganMingguan'])->name('generate-tunjangan.mingguan');
                Route::get('/report/analytics', [LemburController::class, 'report'])->name('report');
                Route::get('/export/data', [LemburController::class, 'export'])->name('export');
            });

            // Activity Logs
            Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
                Route::get('/', [ActivityLogController::class, 'index'])->name('index');
                Route::get('/api', [ActivityLogController::class, 'apiIndex'])->name('api');
                Route::get('/api/stats', [ActivityLogController::class, 'apiStats'])->name('api.stats');
                Route::get('/api/logins', [ActivityLogController::class, 'apiLoginHistory'])->name('api.logins');
                Route::get('/api/errors', [ActivityLogController::class, 'apiErrors'])->name('api.errors');
                Route::post('/clear-old', [ActivityLogController::class, 'clearOldLogs'])->name('clear-old');
                Route::get('/{id}', [ActivityLogController::class, 'show'])->name('show');
            });
        }); // End Admin only routes

        // Ijin Type Management - Admin only
        Route::middleware(['role:admin'])->group(function () {
            Route::resource('ijin-type', IjinTypeController::class);
            Route::post('ijin-type/{ijinType}/toggle-status', [IjinTypeController::class, 'toggleStatus'])
                ->name('ijin-type.toggle-status');
        });

        // Ijin Management
        Route::prefix('ijin')->name('ijin.')->group(function () {

            // List & Detail - All authenticated users
            Route::get('/', [IjinController::class, 'index'])->name('index');
            Route::get('/{ijin}/show', [IjinController::class, 'show'])->name('show');

            // Statistics - Admin & Coordinator
            Route::middleware(['role:admin,coordinator'])->group(function () {
                Route::get('/statistics', [IjinController::class, 'statistics'])->name('statistics');
            });

            // Coordinator Review
            Route::middleware(['role:admin,coordinator'])->group(function () {
                Route::get('/coordinator/pending', [IjinController::class, 'coordinatorPending'])
                    ->name('coordinator-pending');
                Route::get('/{ijin}/coordinator/review', [IjinController::class, 'coordinatorReviewForm'])
                    ->name('coordinator-review-form');
                Route::post('/{ijin}/coordinator/review', [IjinController::class, 'coordinatorReview'])
                    ->name('coordinator-review');
            });

            // Admin Review - BISA BYPASS COORDINATOR
            Route::middleware(['role:admin'])->group(function () {
                Route::get('/admin/pending', [IjinController::class, 'adminPending'])
                    ->name('admin-pending');
                Route::get('/{ijin}/admin/review', [IjinController::class, 'adminReviewForm'])
                    ->name('admin-review-form');
                Route::post('/{ijin}/admin/review', [IjinController::class, 'adminReview'])
                    ->name('admin-review');
            });
        });
    }); // End authenticated routes
});
Route::middleware(['auth', 'role:koordinator'])->prefix('koordinator')->name('koordinator.')->group(function () {

    // Lembur Management untuk Koordinator
    Route::prefix('lembur')->name('lembur.')->group(function () {
        Route::get('/', [LemburKoorController::class, 'index'])->name('index');
        Route::get('/{lembur}', [LemburKoorController::class, 'show'])->name('show');
        Route::post('/{lembur}/approve', [LemburKoorController::class, 'approve'])->name('approve');
        Route::post('/{lembur}/reject', [LemburKoorController::class, 'reject'])->name('reject');
    });
});





// Public: Verifikasi Tiket Digital
Route::get('/ticket/{token}', function (string $token) {
    $attendance = EventAttendance::with(['event', 'karyawan.department'])
        ->where('ticket_token', $token)
        ->first();
    return view('ticket.verify', compact('attendance'));
})->name('ticket.verify');

Route::get('/test-slack-respondprefast', function () {
    $slack = app(\App\Services\SlackNotificationService::class);

    // ✅ Test Success
    $slack->notifySuccess('🎉 Test Slack Integration - respondprefast', [
        'app_name' => config('app.name'),
        'environment' => config('app.env'),
        'timestamp' => now()->format('Y-m-d H:i:s'),
        'message' => 'Webhook berhasil terhubung ke channel #respondprefast!',
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Test notification sent to #respondprefast! Check your Slack.',
    ]);
});

Route::get('/test-slack-error', function () {
    $slack = app(\App\Services\SlackNotificationService::class);

    // 🔴 Test Error
    try {
        throw new \Exception('This is a simulated error for testing!');
    } catch (\Exception $e) {
        $slack->notifyError('Test Error Notification', $e, [
            'endpoint' => request()->path(),
            'user' => 'Test User',
            'ip' => request()->ip(),
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Error notification sent to #respondprefast!',
    ]);
});

<?php

use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\KaryawanController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\AbsenController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect root to admin login
Route::get('/', function () {
    return redirect('/login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {

    // Guest routes (not authenticated)


    // Authenticated admin routes
    Route::middleware(['auth', 'admin'])->group(function () {

        // Logout
        Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
        Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Department Management
        Route::resource('department', DepartmentController::class);
        Route::get('/department/{department}/karyawans', [DepartmentController::class, 'getKaryawans'])->name('department.karyawans');
        Route::post('/department/{department}/toggle-status', [DepartmentController::class, 'toggleStatus'])->name('department.toggle-status');
        Route::post('department/bulk-delete', [DepartmentController::class, 'bulkDelete'])->name('department.bulk-delete');
        Route::post('department/bulk-toggle-status', [DepartmentController::class, 'bulkToggleStatus'])->name('department.bulk-toggle-status');
        // Route::post('department/{department}/toggle-status', [DepartmentController::class, 'toggleStatus'])->name('department.toggle-status');
        Route::get('department/export', [DepartmentController::class, 'export'])->name('department.export');
        // Karyawan Management
        Route::resource('karyawan', KaryawanController::class);
        Route::post('karyawan/bulk-delete', [KaryawanController::class, 'bulkDelete'])->name('karyawan.bulk-delete');
        // Route::post('karyawan/export', [KaryawanController::class, 'export'])->name('karyawan.export');
        // Route::get('karyawan/export', [KaryawanController::class, 'export'])->name('karyawan.export');
        // Additional karyawan routes
        Route::post('karyawan/{id}/toggle-status', [KaryawanController::class, 'toggleStatus'])
            ->name('karyawan.toggle-status');
        Route::post('karyawan/{id}/reset-password', [KaryawanController::class, 'resetPassword'])
            ->name('karyawan.reset-password');
        Route::post('karyawan/bulk-delete', [KaryawanController::class, 'bulkDelete'])
            ->name('karyawan.bulk-delete');
        Route::match(['get', 'post'], 'karyawan/export', [KaryawanController::class, 'export'])
            ->name('karyawan.export');

        // Shift Management
        Route::resource('shift', ShiftController::class);
        Route::get('/shift-active/list', [ShiftController::class, 'getActiveShifts'])->name('shift.active');
        Route::post('/shift/{shift}/toggle-status', [ShiftController::class, 'toggleStatus'])->name('shift.toggle-status');
        Route::post('shift/bulk-delete', [ShiftController::class, 'bulkDelete'])->name('shift.bulk-delete');
        Route::post('shift/bulk-toggle-status', [ShiftController::class, 'bulkToggleStatus'])->name('shift.bulk-toggle-status');
        Route::post('shift/{shift}/toggle-status', [ShiftController::class, 'toggleStatus'])->name('shift.toggle-status');
        Route::get('shift/export', [ShiftController::class, 'export'])->name('shift.export');

        // Jadwal Management
        Route::resource('jadwal', JadwalController::class)->except(['create', 'edit', 'show']);
        Route::get('/jadwal/calendar', [JadwalController::class, 'calendar'])->name('jadwal.calendar');
        Route::post('/jadwal/bulk-store', [JadwalController::class, 'bulkStore'])->name('jadwal.bulk-store');
        Route::get('/jadwal/{jadwal}/check-editable', [JadwalController::class, 'checkEditable'])->name('jadwal.check-editable');

        // Absen Management & Reports
        Route::get('/absen/report', [AbsenController::class, 'report'])->name('absen.report');
        Route::get('/absen/daily-report', [AbsenController::class, 'dailyReport'])->name('absen.daily-report');
        Route::get('/absen/export-report', [AbsenController::class, 'exportReport'])->name('absen.export-report');
        Route::resource('absen', AbsenController::class)->only(['index', 'show']);

        Route::resource('user', UserController::class);
        Route::post('user/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('user.toggle-status');
        Route::post('user/{user}/reset-password', [UserController::class, 'resetPassword'])->name('user.reset-password');
        Route::post('user/bulk-delete', [UserController::class, 'bulkDelete'])->name('user.bulk-delete');
        Route::post('user/bulk-toggle-status', [UserController::class, 'bulkToggleStatus'])->name('user.bulk-toggle-status');
        Route::get('user/export', [UserController::class, 'export'])->name('user.export');
    });
});

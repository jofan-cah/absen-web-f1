<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absen;
use App\Models\Department;
use App\Models\Karyawan;
use Carbon\Carbon;

class DashboardController extends Controller
{
public function index()
{
    $today = Carbon::today();

    // Excluded department code
    $excludedDeptCode = 'DEPT001';

    // Stats cards - exclude DEPT001
    $stats = [
        'total_karyawan' => Karyawan::where('employment_status', 'active')
            ->whereHas('department', function ($query) use ($excludedDeptCode) {
                $query->where('department_id', '!=', $excludedDeptCode);
            })
            ->count(),

        'total_department' => Department::where('is_active', true)
            ->where('department_id', '!=', $excludedDeptCode)
            ->count(),

        // Yang sudah clock_in (present/late)
        'today_present' => Absen::whereDate('date', $today)
            ->whereIn('status', ['present', 'late'])
            ->whereNotNull('clock_in') // pastikan sudah absen masuk
            ->whereHas('karyawan.department', function ($query) use ($excludedDeptCode) {
                $query->where('department_id', '!=', $excludedDeptCode);
            })
            ->count(),

        // Yang absent (punya record tapi belum clock_in atau status absent)
        'today_absent' => Absen::whereDate('date', $today)
            ->where(function ($query) {
                $query->where('status', 'absent')
                    ->orWhereNull('clock_in'); // belum absen masuk
            })
            ->whereHas('karyawan.department', function ($query) use ($excludedDeptCode) {
                $query->where('department_id', '!=', $excludedDeptCode);
            })
            ->count(),
    ];

    // Chart data - absen per hari (7 hari terakhir) - exclude DEPT001
    $chartData = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = $today->copy()->subDays($i);
        $chartData[] = [
            'date' => $date->format('M d'),
            // Yang sudah clock_in dengan status present/late
            'present' => Absen::whereDate('date', $date)
                ->whereIn('status', ['present', 'late'])
                ->whereNotNull('clock_in') // tambahkan ini
                ->whereHas('karyawan.department', function ($query) use ($excludedDeptCode) {
                    $query->where('department_id', '!=', $excludedDeptCode);
                })
                ->count(),
            // Yang absent atau belum clock_in
            'absent' => Absen::whereDate('date', $date)
                ->where(function ($query) {
                    $query->where('status', 'absent')
                        ->orWhereNull('clock_in'); // tambahkan ini
                })
                ->whereHas('karyawan.department', function ($query) use ($excludedDeptCode) {
                    $query->where('department_id', '!=', $excludedDeptCode);
                })
                ->count(),
        ];
    }

    // Recent absens - exclude DEPT001
    $recentAbsens = Absen::with([
        'karyawan:karyawan_id,full_name,position,department_id',
        'karyawan.department:department_id,department_id',
        'jadwal.shift:shift_id,name',
    ])
        ->whereDate('date', $today)
        ->whereNotNull('clock_in') // hanya yang sudah absen
        ->whereHas('karyawan.department', function ($query) use ($excludedDeptCode) {
            $query->where('department_id', '!=', $excludedDeptCode);
        })
        ->orderBy('clock_in', 'desc')
        ->take(10)
        ->get();

    // Departments stats - exclude DEPT001
    $departmentStats = Department::withCount([
        'karyawans as total_karyawan' => function ($query) {
            $query->where('employment_status', 'active');
        },
        'karyawans as present_today' => function ($query) use ($today) {
            $query->where('employment_status', 'active')
                ->whereHas('absens', function ($q) use ($today) {
                    $q->whereDate('date', $today)
                        ->whereIn('status', ['present', 'late'])
                        ->whereNotNull('clock_in'); // tambahkan ini
                });
        },
    ])
        ->where('is_active', true)
        ->where('department_id', '!=', $excludedDeptCode)
        ->get();

    return view('admin.dashboard', compact(
        'stats',
        'chartData',
        'recentAbsens',
        'departmentStats'
    ));
}
}

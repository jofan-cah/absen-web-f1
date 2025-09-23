<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Department;
use App\Models\Absen;
use App\Models\Jadwal;
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
                ->whereHas('department', function($query) use ($excludedDeptCode) {
                    $query->where('code', '!=', $excludedDeptCode);
                })
                ->count(),

            'total_department' => Department::where('is_active', true)
                ->where('code', '!=', $excludedDeptCode)
                ->count(),

            'today_present' => Absen::whereDate('date', $today)
                ->whereIn('status', ['present', 'late'])
                ->whereHas('karyawan.department', function($query) use ($excludedDeptCode) {
                    $query->where('code', '!=', $excludedDeptCode);
                })
                ->count(),

            'today_absent' => Absen::whereDate('date', $today)
                ->where('status', 'absent')
                ->whereHas('karyawan.department', function($query) use ($excludedDeptCode) {
                    $query->where('code', '!=', $excludedDeptCode);
                })
                ->count(),
        ];

        // Chart data - absen per hari (7 hari terakhir) - exclude DEPT001
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $chartData[] = [
                'date' => $date->format('M d'),
                'present' => Absen::whereDate('date', $date)
                    ->whereIn('status', ['present', 'late'])
                    ->whereHas('karyawan.department', function($query) use ($excludedDeptCode) {
                        $query->where('code', '!=', $excludedDeptCode);
                    })
                    ->count(),
                'absent' => Absen::whereDate('date', $date)
                    ->where('status', 'absent')
                    ->whereHas('karyawan.department', function($query) use ($excludedDeptCode) {
                        $query->where('code', '!=', $excludedDeptCode);
                    })
                    ->count(),
            ];
        }

        // Recent absens - exclude DEPT001
        $recentAbsens = Absen::with([
                'karyawan:karyawan_id,full_name,position,department_id',
                'karyawan.department:department_id,code',
                'jadwal.shift:shift_id,name'
            ])
            ->whereDate('date', $today)
            ->whereHas('karyawan.department', function($query) use ($excludedDeptCode) {
                $query->where('code', '!=', $excludedDeptCode);
            })
            ->orderBy('clock_in', 'desc')
            ->take(10)
            ->get();

        // Departments stats - exclude DEPT001
        $departmentStats = Department::withCount([
            'karyawans as total_karyawan' => function($query) {
                $query->where('employment_status', 'active');
            },
            'karyawans as present_today' => function($query) use ($today) {
                $query->where('employment_status', 'active')
                      ->whereHas('absens', function($q) use ($today) {
                          $q->whereDate('date', $today)
                            ->whereIn('status', ['present', 'late']);
                      });
            }
        ])
        ->where('is_active', true)
        ->where('code', '!=', $excludedDeptCode)
        ->get();

        return view('admin.dashboard', compact(
            'stats',
            'chartData',
            'recentAbsens',
            'departmentStats'
        ));
    }
}

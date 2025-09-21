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

        // Stats cards
        $stats = [
            'total_karyawan' => Karyawan::where('employment_status', 'active')->count(),
            'total_department' => Department::where('is_active', true)->count(),
            'today_present' => Absen::whereDate('date', $today)
                ->whereIn('status', ['present', 'late'])
                ->count(),
            'today_absent' => Absen::whereDate('date', $today)
                ->where('status', 'absent')
                ->count(),
        ];

        // Chart data - absen per hari (7 hari terakhir)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $chartData[] = [
                'date' => $date->format('M d'),
                'present' => Absen::whereDate('date', $date)
                    ->whereIn('status', ['present', 'late'])
                    ->count(),
                'absent' => Absen::whereDate('date', $date)
                    ->where('status', 'absent')
                    ->count(),
            ];
        }

        // Recent absens
        $recentAbsens = Absen::with(['karyawan:karyawan_id,full_name,position', 'jadwal.shift:shift_id,name'])
            ->whereDate('date', $today)
            ->orderBy('clock_in', 'desc')
            ->take(10)
            ->get();

        // Departments stats
        $departmentStats = Department::withCount([
            'karyawans as total_karyawan',
            'karyawans as present_today' => function($query) use ($today) {
                $query->whereHas('absens', function($q) use ($today) {
                    $q->whereDate('date', $today)
                      ->whereIn('status', ['present', 'late']);
                });
            }
        ])->where('is_active', true)->get();

        return view('admin.dashboard', compact(
            'stats',
            'chartData',
            'recentAbsens',
            'departmentStats'
        ));
    }
}

<?php
namespace App\Http\Controllers\Api;

use App\Models\Jadwal;
use App\Models\Absen;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends BaseApiController
{
    public function index(Request $request)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;
        $today = Carbon::today();

        if (!$karyawan) {
            return $this->notFoundResponse('Data karyawan tidak ditemukan');
        }

        // Jadwal hari ini
        $todayJadwal = Jadwal::with('shift')
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->whereDate('date', $today)
            ->first();

        // Absen hari ini
        $todayAbsen = null;
        if ($todayJadwal) {
            $todayAbsen = Absen::where('jadwal_id', $todayJadwal->jadwal_id)->first();
        }

        // Stats bulan ini
        $thisMonth = Carbon::now()->startOfMonth();
        $monthlyStats = [
            'total_jadwal' => Jadwal::where('karyawan_id', $karyawan->karyawan_id)
                ->whereBetween('date', [$thisMonth, $today])
                ->count(),
            'hadir' => Absen::where('karyawan_id', $karyawan->karyawan_id)
                ->whereBetween('date', [$thisMonth, $today])
                ->whereIn('status', ['present', 'late'])
                ->count(),
            'terlambat' => Absen::where('karyawan_id', $karyawan->karyawan_id)
                ->whereBetween('date', [$thisMonth, $today])
                ->where('status', 'late')
                ->count(),
            'tidak_hadir' => Absen::where('karyawan_id', $karyawan->karyawan_id)
                ->whereBetween('date', [$thisMonth, $today])
                ->where('status', 'absent')
                ->count(),
        ];

        // Jadwal minggu ini
        $weeklyJadwals = Jadwal::with(['shift', 'absen'])
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->whereBetween('date', [$today, $today->copy()->addDays(6)])
            ->orderBy('date')
            ->get();

        return $this->successResponse([
            'karyawan' => $karyawan->load('department'),
            'today' => [
                'date' => $today->format('Y-m-d'),
                'jadwal' => $todayJadwal,
                'absen' => $todayAbsen
            ],
            'monthly_stats' => $monthlyStats,
            'weekly_jadwals' => $weeklyJadwals
        ], 'Dashboard data retrieved successfully');
    }

    public function notifications(Request $request)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;
        $today = Carbon::today();

        $notifications = [];

        // Check jadwal hari ini
        $todayJadwal = Jadwal::with('shift')
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->whereDate('date', $today)
            ->first();

        if ($todayJadwal) {
            $todayAbsen = Absen::where('jadwal_id', $todayJadwal->jadwal_id)->first();

            if ($todayAbsen && $todayAbsen->status === 'scheduled') {
                $notifications[] = [
                    'type' => 'reminder',
                    'title' => 'Jangan Lupa Absen!',
                    'message' => "Hari ini kamu jadwal {$todayJadwal->shift->name} ({$todayJadwal->shift->start_time} - {$todayJadwal->shift->end_time})",
                    'priority' => 'high'
                ];
            }
        }

        // Check jadwal besok
        $tomorrow = $today->copy()->addDay();
        $tomorrowJadwal = Jadwal::with('shift')
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->whereDate('date', $tomorrow)
            ->first();

        if ($tomorrowJadwal) {
            $notifications[] = [
                'type' => 'info',
                'title' => 'Jadwal Besok',
                'message' => "Besok kamu jadwal {$tomorrowJadwal->shift->name} ({$tomorrowJadwal->shift->start_time} - {$tomorrowJadwal->shift->end_time})",
                'priority' => 'normal'
            ];
        }

        return $this->successResponse([
            'notifications' => $notifications,
            'count' => count($notifications)
        ], 'Notifications retrieved successfully');
    }
}

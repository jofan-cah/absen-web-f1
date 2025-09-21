<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absen;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RiwayatController extends BaseApiController
{
    public function absen(Request $request)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return $this->notFoundResponse('Data karyawan tidak ditemukan');
        }

        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $perPage = $this->getPerPage($request);

        $absens = Absen::with(['jadwal.shift'])
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date', 'desc')
            ->paginate($perPage);

        // Summary stats untuk seluruh bulan
        $allAbsens = Absen::where('karyawan_id', $karyawan->karyawan_id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        $summary = [
            'total_jadwal' => $allAbsens->count(),
            'hadir' => $allAbsens->whereIn('status', ['present', 'late'])->count(),
            'terlambat' => $allAbsens->where('status', 'late')->count(),
            'tidak_hadir' => $allAbsens->where('status', 'absent')->count(),
            'total_jam_kerja' => round($allAbsens->sum('work_hours'), 2),
            'rata_rata_jam_kerja' => $allAbsens->count() > 0 ? round($allAbsens->avg('work_hours'), 2) : 0,
            'total_terlambat_menit' => $allAbsens->sum('late_minutes'),
        ];

        // Persentase kehadiran
        $summary['persentase_kehadiran'] = $summary['total_jadwal'] > 0
            ? round(($summary['hadir'] / $summary['total_jadwal']) * 100, 1)
            : 0;

        return $this->paginatedResponse($absens, 'Riwayat absen berhasil diambil', [
            'summary' => $summary,
            'period' => [
                'month' => $month,
                'year' => $year,
                'month_name' => Carbon::createFromDate($year, $month)->format('F Y')
            ]
        ]);
    }

    public function jadwal(Request $request)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return $this->notFoundResponse('Data karyawan tidak ditemukan');
        }

        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $jadwals = Jadwal::with(['shift', 'absen'])
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date')
            ->get();

        // Group by week
        $weeklyData = [];
        foreach ($jadwals as $jadwal) {
            $weekNumber = $jadwal->date->weekOfMonth;
            $weekKey = "Minggu ke-{$weekNumber}";

            if (!isset($weeklyData[$weekKey])) {
                $weeklyData[$weekKey] = [];
            }

            $weeklyData[$weekKey][] = $jadwal;
        }

        return $this->successResponse([
            'jadwals' => $jadwals,
            'weekly_data' => $weeklyData,
            'total_jadwal' => $jadwals->count(),
            'period' => [
                'month' => $month,
                'year' => $year,
                'month_name' => Carbon::createFromDate($year, $month)->format('F Y')
            ]
        ], 'Riwayat jadwal berhasil diambil');
    }

    public function detail(Request $request, $date)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return $this->notFoundResponse('Data karyawan tidak ditemukan');
        }

        try {
            $targetDate = Carbon::parse($date);
        } catch (\Exception $e) {
            return $this->errorResponse('Format tanggal tidak valid', 400);
        }

        $jadwal = Jadwal::with(['shift', 'absen'])
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->whereDate('date', $targetDate)
            ->first();

        if (!$jadwal) {
            return $this->notFoundResponse('Tidak ada jadwal pada tanggal tersebut');
        }

        // Additional info
        $info = [
            'day_name' => $targetDate->format('l'),
            'date_formatted' => $targetDate->format('d F Y'),
            'is_weekend' => $targetDate->isWeekend(),
            'days_ago' => $targetDate->diffInDays(now()),
        ];

        return $this->successResponse([
            'jadwal' => $jadwal,
            'info' => $info
        ], 'Detail riwayat harian berhasil diambil');
    }

    public function yearly(Request $request)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return $this->notFoundResponse('Data karyawan tidak ditemukan');
        }

        $year = $request->get('year', now()->year);

        $absens = Absen::where('karyawan_id', $karyawan->karyawan_id)
            ->whereYear('date', $year)
            ->get();

        // Group by month
        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthAbsens = $absens->filter(function($absen) use ($month) {
                return $absen->date->month == $month;
            });

            $monthName = Carbon::createFromDate($year, $month)->format('F');

            $monthlyData[] = [
                'month' => $month,
                'month_name' => $monthName,
                'total_jadwal' => $monthAbsens->count(),
                'hadir' => $monthAbsens->whereIn('status', ['present', 'late'])->count(),
                'terlambat' => $monthAbsens->where('status', 'late')->count(),
                'tidak_hadir' => $monthAbsens->where('status', 'absent')->count(),
                'total_jam_kerja' => round($monthAbsens->sum('work_hours'), 2),
            ];
        }

        // Yearly summary
        $yearlySummary = [
            'total_jadwal' => $absens->count(),
            'hadir' => $absens->whereIn('status', ['present', 'late'])->count(),
            'terlambat' => $absens->where('status', 'late')->count(),
            'tidak_hadir' => $absens->where('status', 'absent')->count(),
            'total_jam_kerja' => round($absens->sum('work_hours'), 2),
            'rata_rata_jam_kerja_per_bulan' => $absens->count() > 0 ? round($absens->sum('work_hours') / 12, 2) : 0,
        ];

        // Persentase kehadiran
        $yearlySummary['persentase_kehadiran'] = $yearlySummary['total_jadwal'] > 0
            ? round(($yearlySummary['hadir'] / $yearlySummary['total_jadwal']) * 100, 1)
            : 0;

        return $this->successResponse([
            'monthly_data' => $monthlyData,
            'yearly_summary' => $yearlySummary,
            'year' => $year
        ], 'Summary tahunan berhasil diambil');
    }

    public function photos(Request $request)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return $this->notFoundResponse('Data karyawan tidak ditemukan');
        }

        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $perPage = $this->getPerPage($request);

        $absensQuery = Absen::where('karyawan_id', $karyawan->karyawan_id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where(function($query) {
                $query->whereNotNull('clock_in_photo')
                      ->orWhereNotNull('clock_out_photo');
            })
            ->orderBy('date', 'desc');

        // For pagination, we need to get all and then process
        $absens = $absensQuery->get();

        $photos = [];
        foreach ($absens as $absen) {
            if ($absen->clock_in_photo) {
                $photos[] = [
                    'id' => $absen->absen_id . '_in',
                    'absen_id' => $absen->absen_id,
                    'date' => $absen->date->format('Y-m-d'),
                    'type' => 'clock_in',
                    'photo_url' => url('storage/' . $absen->clock_in_photo),
                    'time' => $absen->clock_in,
                    'location' => $absen->clock_in_address,
                    'created_at' => $absen->date->format('Y-m-d') . ' ' . $absen->clock_in
                ];
            }

            if ($absen->clock_out_photo) {
                $photos[] = [
                    'id' => $absen->absen_id . '_out',
                    'absen_id' => $absen->absen_id,
                    'date' => $absen->date->format('Y-m-d'),
                    'type' => 'clock_out',
                    'photo_url' => url('storage/' . $absen->clock_out_photo),
                    'time' => $absen->clock_out,
                    'location' => $absen->clock_out_address,
                    'created_at' => $absen->date->format('Y-m-d') . ' ' . $absen->clock_out
                ];
            }
        }

        // Sort photos by date desc
        usort($photos, function($a, $b) {
            return strcmp($b['created_at'], $a['created_at']);
        });

        // Manual pagination for photos array
        $currentPage = $this->getCurrentPage($request);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedPhotos = array_slice($photos, $offset, $perPage);

        // Create pagination meta
        $total = count($photos);
        $lastPage = (int) ceil($total / $perPage);

        return $this->responseWithMeta($paginatedPhotos, [
            'current_page' => $currentPage,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => $lastPage,
            'has_more_pages' => $currentPage < $lastPage,
            'period' => [
                'month' => $month,
                'year' => $year,
                'month_name' => Carbon::createFromDate($year, $month)->format('F Y')
            ]
        ], 'Galeri foto absen berhasil diambil');
    }
}

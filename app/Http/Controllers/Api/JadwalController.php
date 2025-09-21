<?php


namespace App\Http\Controllers\Api;

use App\Models\Jadwal;
use Illuminate\Http\Request;
use Carbon\Carbon;

class JadwalController extends BaseApiController
{
    public function index(Request $request)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;

        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $jadwals = Jadwal::with(['shift', 'absen'])
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date')
            ->get();

        return $this->successResponse([
            'jadwals' => $jadwals,
            'month' => $month,
            'year' => $year
        ], 'Jadwal bulanan berhasil diambil');
    }

    public function weekly(Request $request)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;

        $startDate = $request->get('start_date', now()->startOfWeek());
        $endDate = Carbon::parse($startDate)->endOfWeek();

        $jadwals = Jadwal::with(['shift', 'absen'])
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();

        return $this->successResponse([
            'jadwals' => $jadwals,
            'start_date' => Carbon::parse($startDate)->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d')
        ], 'Jadwal mingguan berhasil diambil');
    }

    public function today(Request $request)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;
        $today = Carbon::today();

        $jadwal = Jadwal::with(['shift', 'absen'])
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->whereDate('date', $today)
            ->first();

        if (!$jadwal) {
            return $this->notFoundResponse('Tidak ada jadwal untuk hari ini');
        }

        return $this->successResponse([
            'jadwal' => $jadwal,
            'date' => $today->format('Y-m-d')
        ], 'Jadwal hari ini berhasil diambil');
    }

    public function tomorrow(Request $request)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;
        $tomorrow = Carbon::tomorrow();

        $jadwal = Jadwal::with(['shift', 'absen'])
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->whereDate('date', $tomorrow)
            ->first();

        return $this->successResponse([
            'jadwal' => $jadwal,
            'date' => $tomorrow->format('Y-m-d'),
            'has_jadwal' => $jadwal !== null
        ], 'Jadwal besok berhasil diambil');
    }

    public function byDateRange(Request $request)
    {
        $user = $request->user();
        $karyawan = $user->karyawan;

        $startDate = $request->get('start_date', now()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->addDays(7)->format('Y-m-d'));

        $jadwals = Jadwal::with(['shift', 'absen'])
            ->where('karyawan_id', $karyawan->karyawan_id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();

        return $this->successResponse([
            'jadwals' => $jadwals,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total' => $jadwals->count()
        ], 'Jadwal berdasarkan range tanggal berhasil diambil');
    }
}

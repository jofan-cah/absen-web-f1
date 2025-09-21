<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absen;
use App\Models\Karyawan;
use App\Models\Department;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AbsenController extends Controller
{
    public function index(Request $request)
    {
        $query = Absen::with(['karyawan.department', 'jadwal.shift']);

        // Filter by date
        if ($request->date) {
            $query->whereDate('date', $request->date);
        } else {
            $query->whereDate('date', today());
        }

        // Filter by department
        if ($request->department_id) {
            $query->whereHas('karyawan', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by karyawan
        if ($request->karyawan_id) {
            $query->where('karyawan_id', $request->karyawan_id);
        }

        $absens = $query->orderBy('date', 'desc')
                       ->orderBy('clock_in', 'desc')
                       ->paginate(20);

        $departments = Department::where('is_active', true)->get();
        $karyawans = Karyawan::where('employment_status', 'active')->get();

        return view('admin.absen.indexAbsen', compact(
            'absens',
            'departments',
            'karyawans'
        ));
    }

    public function show(Absen $absen)
    {
        $absen->load(['karyawan.department', 'jadwal.shift']);
        return view('admin.absen.showExport', compact('absen'));
    }

    public function report(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $department_id = $request->get('department_id');

        $query = Absen::with(['karyawan.department', 'jadwal.shift'])
            ->whereYear('date', $year)
            ->whereMonth('date', $month);

        if ($department_id) {
            $query->whereHas('karyawan', function($q) use ($department_id) {
                $q->where('department_id', $department_id);
            });
        }

        $absens = $query->get();

        // Group by karyawan
        $reportData = [];
        foreach ($absens as $absen) {
            $karyawan_id = $absen->karyawan_id;

            if (!isset($reportData[$karyawan_id])) {
                $reportData[$karyawan_id] = [
                    'karyawan' => $absen->karyawan,
                    'total_jadwal' => 0,
                    'hadir' => 0,
                    'terlambat' => 0,
                    'pulang_cepat' => 0,
                    'tidak_hadir' => 0,
                    'total_jam_kerja' => 0,
                    'total_terlambat_menit' => 0,
                ];
            }

            $reportData[$karyawan_id]['total_jadwal']++;

            switch ($absen->status) {
                case 'present':
                    $reportData[$karyawan_id]['hadir']++;
                    break;
                case 'late':
                    $reportData[$karyawan_id]['terlambat']++;
                    $reportData[$karyawan_id]['total_terlambat_menit'] += $absen->late_minutes;
                    break;
                case 'early_checkout':
                    $reportData[$karyawan_id]['pulang_cepat']++;
                    break;
                case 'absent':
                    $reportData[$karyawan_id]['tidak_hadir']++;
                    break;
            }

            if ($absen->work_hours) {
                $reportData[$karyawan_id]['total_jam_kerja'] += $absen->work_hours;
            }
        }

        $departments = Department::where('is_active', true)->get();

        return view('admin.absen.reportExport', compact(
            'reportData',
            'departments',
            'month',
            'year',
            'department_id'
        ));
    }

    public function exportReport(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $department_id = $request->get('department_id');

        $query = Absen::with(['karyawan.department', 'jadwal.shift'])
            ->whereYear('date', $year)
            ->whereMonth('date', $month);

        if ($department_id) {
            $query->whereHas('karyawan', function($q) use ($department_id) {
                $q->where('department_id', $department_id);
            });
        }

        $absens = $query->orderBy('karyawan_id')->orderBy('date')->get();

        // Set headers for CSV download
        $filename = "laporan_absensi_{$year}_{$month}.csv";

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // CSV Headers
        fputcsv($output, [
            'NIP',
            'Nama Karyawan',
            'Department',
            'Tanggal',
            'Shift',
            'Jam Masuk',
            'Jam Keluar',
            'Status',
            'Terlambat (menit)',
            'Jam Kerja',
            'Lokasi Masuk',
            'Lokasi Keluar'
        ]);

        // CSV Data
        foreach ($absens as $absen) {
            fputcsv($output, [
                $absen->karyawan->nip,
                $absen->karyawan->full_name,
                $absen->karyawan->department->name,
                $absen->date->format('Y-m-d'),
                $absen->jadwal->shift->name,
                $absen->clock_in ?? '-',
                $absen->clock_out ?? '-',
                ucfirst($absen->status),
                $absen->late_minutes,
                $absen->work_hours ?? 0,
                $absen->clock_in_address ?? '-',
                $absen->clock_out_address ?? '-'
            ]);
        }

        fclose($output);
        exit;
    }

    public function dailyReport(Request $request)
    {
        $date = $request->get('date', today());

        $absens = Absen::with(['karyawan.department', 'jadwal.shift'])
            ->whereDate('date', $date)
            ->orderBy('karyawan_id')
            ->get();

        // Statistics
        $stats = [
            'total_jadwal' => $absens->count(),
            'hadir' => $absens->whereIn('status', ['present', 'late'])->count(),
            'terlambat' => $absens->where('status', 'late')->count(),
            'tidak_hadir' => $absens->where('status', 'absent')->count(),
            'belum_absen' => $absens->where('status', 'scheduled')->count(),
        ];

        return view('admin.absen.daily-report', compact('absens', 'stats', 'date'));
    }
}

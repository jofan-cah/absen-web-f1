<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Karyawan;
use App\Models\Shift;
use App\Models\Absen;
use Illuminate\Http\Request;
use Carbon\Carbon;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $jadwals = Jadwal::with(['karyawan', 'shift'])
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date')
            ->get();

        return view('admin.jadwal.indexJadwal', compact('jadwals', 'month', 'year'));
    }

    public function calendar(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $departmentId = $request->get('department_id'); // Tambah filter departemen

        // Get jadwals dengan filter departemen
        $jadwalsQuery = Jadwal::with(['karyawan', 'shift', 'absen'])
            ->whereYear('date', $year)
            ->whereMonth('date', $month);

        // Filter berdasarkan departemen jika ada
        if ($departmentId) {
            $jadwalsQuery->whereHas('karyawan', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            });
        }

        $jadwals = $jadwalsQuery->get();

        // Filter karyawan juga berdasarkan departemen
        $karyawansQuery = Karyawan::where('employment_status', 'active')
            ->with('department');

        if ($departmentId) {
            $karyawansQuery->where('department_id', $departmentId);
        }

        $karyawans = $karyawansQuery->get();

        $shifts = Shift::where('is_active', true)->get();

        // Format data untuk calendar
        $calendarData = [];
        foreach ($jadwals as $jadwal) {
            $date = $jadwal->date->format('Y-m-d');
            if (!isset($calendarData[$date])) {
                $calendarData[$date] = [];
            }

            $calendarData[$date][] = [
                'jadwal_id' => $jadwal->jadwal_id,
                'karyawan_id' => $jadwal->karyawan_id,
                'karyawan_name' => $jadwal->karyawan->full_name,
                'shift_id' => $jadwal->shift_id,
                'shift_name' => $jadwal->shift->name,
                'shift_time' => $jadwal->shift->start_time . ' - ' . $jadwal->shift->end_time,
                'absen_status' => $jadwal->absen ? $jadwal->absen->status : 'scheduled',
                'is_editable' => $jadwal->absen ? $jadwal->absen->isEditable() : true,
            ];
        }

        return view('admin.jadwal.calendar', compact(
            'calendarData',
            'karyawans',
            'shifts',
            'month',
            'year',
            'departmentId' // Pass ke view
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,karyawan_id',
            'shift_id' => 'required|exists:shifts,shift_id',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // Check if jadwal already exists
        $existingJadwal = Jadwal::where('karyawan_id', $request->karyawan_id)
            ->where('date', $request->date)
            ->first();

        if ($existingJadwal) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal sudah ada untuk karyawan ini di tanggal tersebut'
            ], 422);
        }

        $jadwal = Jadwal::create([
            'jadwal_id' => Jadwal::generateJadwalId(),
            'karyawan_id' => $request->karyawan_id,
            'shift_id' => $request->shift_id,
            'date' => $request->date,
            'is_active' => true,
            'notes' => $request->notes,
            'created_by_user_id' => auth()->user()->user_id,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil ditambahkan',
                'data' => $jadwal->load(['karyawan', 'shift'])
            ]);
        }

        return redirect()->back()->with('success', 'Jadwal berhasil ditambahkan');
    }

    public function update(Request $request, Jadwal $jadwal)
    {
        $request->validate([
            'shift_id' => 'required|exists:shifts,shift_id',
            'notes' => 'nullable|string',
        ]);

        // Check if absen already has clock_in
        if ($jadwal->absen && !$jadwal->absen->isEditable()) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal tidak bisa diubah, karyawan sudah melakukan absensi'
            ], 422);
        }

        $jadwal->update([
            'shift_id' => $request->shift_id,
            'notes' => $request->notes,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil diupdate',
                'data' => $jadwal->load(['karyawan', 'shift'])
            ]);
        }

        return redirect()->back()->with('success', 'Jadwal berhasil diupdate');
    }

    // public function destroy(Jadwal $jadwal)
    // {
    //     // Check if absen already has clock_in
    //     if ($jadwal->absen && !$jadwal->absen->isEditable()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Jadwal tidak bisa dihapus, karyawan sudah melakukan absensi'
    //         ], 422);
    //     }

    //     // Delete absen record first (if exists)
    //     if ($jadwal->absen) {
    //         $jadwal->absen->delete();
    //     }

    //     $jadwal->delete();

    //     if (request()->expectsJson()) {
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Jadwal berhasil dihapus'
    //         ]);
    //     }

    //     return redirect()->back()->with('success', 'Jadwal berhasil dihapus');
    // }

    public function checkEditable(Jadwal $jadwal)
    {
        $isEditable = $jadwal->absen ? $jadwal->absen->isEditable() : true;
        $reason = $isEditable ? null : 'Karyawan sudah melakukan absensi';

        return response()->json([
            'editable' => $isEditable,
            'reason' => $reason
        ]);
    }

    // Bulk operations for drag & drop
    public function bulkStore(Request $request)
    {
        $request->validate([
            'jadwals' => 'required|array',
            'jadwals.*.karyawan_id' => 'required|exists:karyawans,karyawan_id',
            'jadwals.*.shift_id' => 'required|exists:shifts,shift_id',
            'jadwals.*.date' => 'required|date',
        ]);

        $createdJadwals = [];
        $errors = [];

        foreach ($request->jadwals as $jadwalData) {
            // Check if already exists
            $exists = Jadwal::where('karyawan_id', $jadwalData['karyawan_id'])
                ->where('date', $jadwalData['date'])
                ->exists();

            if ($exists) {
                $errors[] = "Jadwal sudah ada untuk {$jadwalData['karyawan_id']} pada {$jadwalData['date']}";
                continue;
            }

            $jadwal = Jadwal::create([
                'jadwal_id' => Jadwal::generateJadwalId(),
                'karyawan_id' => $jadwalData['karyawan_id'],
                'shift_id' => $jadwalData['shift_id'],
                'date' => $jadwalData['date'],
                'is_active' => true,
                'created_by_user_id' => auth()->user()->user_id,
            ]);

            $createdJadwals[] = $jadwal->load(['karyawan', 'shift']);
        }

        return response()->json([
            'success' => true,
            'message' => count($createdJadwals) . ' jadwal berhasil ditambahkan',
            'data' => $createdJadwals,
            'errors' => $errors
        ]);
    }

    // Tambahkan method ini di JadwalController
    public function destroy(Jadwal $jadwal)
    {
        // Check if absen has actual attendance data (clock_in exists)
        if ($jadwal->absen && $jadwal->absen->clock_in) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal tidak bisa dihapus karena karyawan sudah melakukan absensi'
                ], 422);
            }

            return back()->withErrors([
                'delete' => 'Jadwal tidak bisa dihapus karena karyawan sudah melakukan absensi'
            ]);
        }

        // Delete absen record first if it exists and still 'scheduled'
        if ($jadwal->absen && $jadwal->absen->status === 'scheduled') {
            $jadwal->absen->delete();
        }

        $jadwal->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil dihapus'
            ]);
        }

        return redirect()->back()->with('success', 'Jadwal berhasil dihapus');
    }
}

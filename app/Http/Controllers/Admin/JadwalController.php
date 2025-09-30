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
    private function getAccessibleDepartments()
    {
        $user = auth()->user();

        // Admin bisa akses semua department
        if ($user->role === 'admin') {
            return null;
        }

        // PERBAIKAN: Support bahasa Indonesia dan Inggris
        $coordinatorRoles = ['coordinator', 'koordinator', 'wakil_coordinator', 'wakil_koordinator'];

        if (in_array($user->role, $coordinatorRoles)) {
            $user->load('karyawan.department');
            $karyawan = $user->karyawan;

            if (!$karyawan || !$karyawan->department_id) {
                return [];
            }

            return [$karyawan->department_id];
        }

        return [];
    }

    private function canManageDepartment($departmentId)
    {
        $user = auth()->user();

        // Admin bisa manage semua department
        if ($user->role === 'admin') {
            return true;
        }

        // PERBAIKAN: Support bahasa Indonesia dan Inggris
        $coordinatorRoles = ['coordinator', 'koordinator', 'wakil_coordinator', 'wakil_koordinator'];

        if (in_array($user->role, $coordinatorRoles)) {
            $karyawan = $user->karyawan;
            return $karyawan && $karyawan->department_id === $departmentId;
        }

        return false;
    }

    public function index(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $query = Jadwal::with(['karyawan', 'shift'])
            ->whereYear('date', $year)
            ->whereMonth('date', $month);

        // Filter by accessible departments
        $accessibleDepts = $this->getAccessibleDepartments();
        if ($accessibleDepts !== null) {
            $query->whereHas('karyawan', function ($q) use ($accessibleDepts) {
                $q->whereIn('department_id', $accessibleDepts);
            });
        }

        $jadwals = $query->orderBy('date')->get();

        return view('admin.jadwal.indexJadwal', compact('jadwals', 'month', 'year'));
    }

    public function calendar(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $departmentId = $request->get('department_id');


        // Get accessible departments
        $accessibleDepts = $this->getAccessibleDepartments();

        \Log::info('=== CALENDAR METHOD ===');
        \Log::info('Accessible Departments: ' . json_encode($accessibleDepts));
        \Log::info('Selected Department Filter: ' . ($departmentId ?? 'none'));

        // ===== QUERY KARYAWAN =====
        $karyawansQuery = Karyawan::where('employment_status', 'active')
            ->with('department');

        // CRITICAL: Apply department filter for non-admin
        if ($accessibleDepts !== null) {
            if (empty($accessibleDepts)) {
                // Jika array kosong, tidak ada akses sama sekali
                \Log::warning('Empty accessible departments - no karyawan will be shown');
                $karyawansQuery->whereRaw('1 = 0'); // Force no results
            } else {
                // Filter by accessible departments
                \Log::info('Filtering karyawan by departments: ' . implode(', ', $accessibleDepts));
                $karyawansQuery->whereIn('department_id', $accessibleDepts);
            }
        }

        // Additional filter if user selects specific department
        if ($departmentId) {
            $karyawansQuery->where('department_id', $departmentId);
        }

        $karyawans = $karyawansQuery->get();

        \Log::info('Karyawan Query Result: ' . $karyawans->count() . ' records');
        if ($karyawans->count() > 0) {
            \Log::info('Karyawan Names: ' . $karyawans->pluck('full_name')->implode(', '));
            \Log::info('Karyawan Departments: ' . $karyawans->pluck('department_id')->unique()->implode(', '));
        }

        // ===== QUERY JADWAL =====
        $jadwalsQuery = Jadwal::with(['karyawan.department', 'shift', 'absen'])
            ->whereYear('date', $year)
            ->whereMonth('date', $month);

        // Apply same department filter
        if ($accessibleDepts !== null) {
            if (empty($accessibleDepts)) {
                $jadwalsQuery->whereRaw('1 = 0');
            } else {
                $jadwalsQuery->whereHas('karyawan', function ($query) use ($accessibleDepts) {
                    $query->whereIn('department_id', $accessibleDepts);
                });
            }
        }

        if ($departmentId) {
            $jadwalsQuery->whereHas('karyawan', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            });
        }

        $jadwals = $jadwalsQuery->get();

        \Log::info('Jadwal Query Result: ' . $jadwals->count() . ' records');

        // Get shifts
        $shifts = Shift::where('is_active', true)->get();

        // Format calendar data
        $calendarData = [];
        foreach ($jadwals as $jadwal) {
            $date = $jadwal->date->format('Y-m-d');
            if (!isset($calendarData[$date])) {
                $calendarData[$date] = [];
            }

            $canEdit = $this->canManageDepartment($jadwal->karyawan->department_id);

            $calendarData[$date][] = [
                'jadwal_id' => $jadwal->jadwal_id,
                'karyawan_id' => $jadwal->karyawan_id,
                'karyawan_name' => $jadwal->karyawan->full_name,
                'department_id' => $jadwal->karyawan->department_id,
                'department_name' => $jadwal->karyawan->department->name ?? '-',
                'shift_id' => $jadwal->shift_id,
                'shift_name' => $jadwal->shift->name,
                'shift_time' => $jadwal->shift->start_time . ' - ' . $jadwal->shift->end_time,
                'absen_status' => $jadwal->absen ? $jadwal->absen->status : 'scheduled',
                'is_editable' => $jadwal->absen ? $jadwal->absen->isEditable() : true,
                'can_edit' => $canEdit,
            ];
        }

        return view('admin.jadwal.calendar', compact(
            'calendarData',
            'karyawans',
            'shifts',
            'month',
            'year',
            'departmentId',
            'accessibleDepts'
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

        // Check permission
        $karyawan = Karyawan::findOrFail($request->karyawan_id);
        if (!$this->canManageDepartment($karyawan->department_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengelola jadwal department ini'
            ], 403);
        }

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

        // Check permission
        if (!$this->canManageDepartment($jadwal->karyawan->department_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengubah jadwal department ini'
            ], 403);
        }

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

    public function destroy(Jadwal $jadwal)
    {
        // Check permission
        if (!$this->canManageDepartment($jadwal->karyawan->department_id)) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menghapus jadwal department ini'
                ], 403);
            }

            return back()->withErrors([
                'delete' => 'Anda tidak memiliki akses untuk menghapus jadwal department ini'
            ]);
        }

        // Check if absen has actual attendance data
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
            // Check permission for each karyawan
            $karyawan = Karyawan::find($jadwalData['karyawan_id']);
            if (!$karyawan || !$this->canManageDepartment($karyawan->department_id)) {
                $errors[] = "Tidak memiliki akses untuk {$jadwalData['karyawan_id']}";
                continue;
            }

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

    public function checkEditable(Jadwal $jadwal)
    {
        // Check permission
        $canEdit = $this->canManageDepartment($jadwal->karyawan->department_id);

        if (!$canEdit) {
            return response()->json([
                'editable' => false,
                'reason' => 'Anda tidak memiliki akses untuk mengedit jadwal department ini'
            ]);
        }

        $isEditable = $jadwal->absen ? $jadwal->absen->isEditable() : true;
        $reason = $isEditable ? null : 'Karyawan sudah melakukan absensi';

        return response()->json([
            'editable' => $isEditable,
            'reason' => $reason
        ]);
    }
}

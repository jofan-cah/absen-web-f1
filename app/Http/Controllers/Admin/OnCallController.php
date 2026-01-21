<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absen;
use App\Models\Department;
use App\Models\Jadwal;
use App\Models\Karyawan;
use App\Models\Lembur;
// use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OnCallController extends Controller
{
    /**
     * Display a listing of OnCall
     * GET /oncall
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->role === 'admin';

        // Base query
        $query = Lembur::with([
            'karyawan.department',
            'jadwalOnCall.shift',
            'absen',
            'createdBy',
        ])->where('jenis_lembur', 'oncall');

        // ADMIN: Bisa liat SEMUA OnCall
        if ($isAdmin) {
            // No additional filter - admin sees all

        } else {
            // KOORDINATOR: Hanya liat OnCall yang DIA BUAT SENDIRI
            $koordinatorDept = $user->karyawan->department_id ?? null;

            if (! $koordinatorDept) {
                return redirect()->back()->with('error', 'Data koordinator tidak valid');
            }

            $query->where('created_by_user_id', $user->user_id) // Hanya yang dia buat
                ->whereHas('karyawan', function ($q) use ($koordinatorDept) {
                    $q->where('department_id', $koordinatorDept); // Dari dept yang sama
                });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by tanggal
        if ($request->filled('date')) {
            $query->whereDate('tanggal_lembur', $request->date);
        }

        // Filter by karyawan
        if ($request->filled('karyawan_id')) {
            $query->where('karyawan_id', $request->karyawan_id);
        }

        // Filter by department (ADMIN ONLY)
        if ($isAdmin && $request->filled('department_id')) {
            $query->whereHas('karyawan', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        $oncalls = $query->orderBy('created_at', 'desc')->paginate(15);

        // Summary stats
        $summaryQuery = Lembur::where('jenis_lembur', 'oncall');

        if (! $isAdmin) {
            // Koordinator: hanya yang dia buat
            $summaryQuery->where('created_by_user_id', $user->user_id);
        }

        $summary = [
            'total' => (clone $summaryQuery)->count(),
            'waiting_checkin' => (clone $summaryQuery)->where('status', 'waiting_checkin')->count(),
            'in_progress' => (clone $summaryQuery)->where('status', 'in_progress')->count(),
            'submitted' => (clone $summaryQuery)->where('status', 'submitted')->count(),
            'approved' => (clone $summaryQuery)->where('status', 'approved')->count(),
        ];

        // List karyawan untuk filter
        if ($isAdmin) {
            // Admin: semua karyawan
            $karyawans = Karyawan::orderBy('full_name')->get();
        } else {
            // Koordinator: hanya dari department-nya
            $koordinatorDept = $user->karyawan->department_id ?? null;
            $karyawans = Karyawan::where('department_id', $koordinatorDept)
                ->orderBy('full_name')
                ->get();
        }

        // List departments untuk filter (ADMIN ONLY)
        $departments = [];
        if ($isAdmin) {
            $departments = Department::where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        return view('admin.oncall.indexOncall', compact('oncalls', 'summary', 'karyawans', 'departments', 'isAdmin'));
    }

    /**
     * Show the form for creating a new OnCall
     * GET /oncall/create
     */
    // public function create()
    // {
    //     $user = Auth::user();
    //     $isAdmin = $user->role === 'admin';

    //     // Get karyawan
    //     if ($isAdmin) {
    //         // Admin: semua karyawan
    //         $karyawans = Karyawan::orderBy('full_name')->get();
    //     } else {
    //         // Koordinator: hanya dari department-nya
    //         $koordinatorDept = $user->karyawan->department_id ?? null;

    //         if (! $koordinatorDept) {
    //             return redirect()->back()->with('error', 'Data koordinator tidak valid');
    //         }

    //         $departmentIds = $user->departments()->pluck('department_id');


    //         $karyawans = Karyawan::whereIn('department_id', $departmentIds)
    //             ->orderBy('full_name')
    //             ->get();

    //     }

    //     return view('admin.oncall.createOncall', compact('karyawans'));
    // }


    public function create()
{
    $user = Auth::user();

    if ($user->role === 'admin') {
        $karyawans = Karyawan::orderBy('full_name')->get();
    } else {

        if (! $user->karyawan) {
            return back()->with('error', 'User belum terhubung ke karyawan');
        }

        // Ambil semua department yang dikoordinatori user
        $departmentIds = $user->managedDepartments()
            ->pluck('department_id');

        if ($departmentIds->isEmpty()) {
            return back()->with('error', 'Koordinator belum punya department');
        }

        $karyawans = Karyawan::whereIn('department_id', $departmentIds)
            ->orderBy('full_name')
            ->get();
    }

    return view('admin.oncall.createOncall', compact('karyawans'));
}

    /**
     * Store a newly created OnCall
     * POST /oncall
     */
public function store(Request $request)
{
    $user = Auth::user();
    $isAdmin = $user->role === 'admin';

    // ===============================
    // VALIDASI INPUT
    // ===============================
    $validator = Validator::make($request->all(), [
        'karyawan_id'     => 'required|exists:karyawans,karyawan_id',
        'tanggal_oncall'  => 'required|date|after_or_equal:today',
        'jam_mulai'       => 'required|date_format:H:i',
        'deskripsi'       => 'required|string|max:500',
    ], [
        'karyawan_id.required'    => 'Karyawan harus dipilih',
        'tanggal_oncall.required' => 'Tanggal OnCall harus diisi',
        'tanggal_oncall.after_or_equal' => 'Tanggal OnCall tidak boleh lampau',
        'jam_mulai.required'      => 'Jam mulai harus diisi',
        'jam_mulai.date_format'   => 'Format jam mulai harus HH:MM',
        'deskripsi.required'      => 'Deskripsi OnCall harus diisi',
    ]);

    if ($validator->fails()) {
        return back()
            ->withErrors($validator)
            ->withInput();
    }

    // ===============================
    // VALIDASI AKSES KOORDINATOR
    // ===============================
    $karyawan = Karyawan::findOrFail($request->karyawan_id);

    if (! $isAdmin) {

        // Ambil semua department yang dikelola user (koordinator)
        $departmentIds = $user->managedDepartments()
            ->pluck('department_id')
            ->toArray();

        if (empty($departmentIds)) {
            return back()
                ->with('error', 'Anda tidak memiliki department yang dikelola')
                ->withInput();
        }

        // Pastikan karyawan berasal dari department yang dikelola
        if (! in_array($karyawan->department_id, $departmentIds)) {
            return back()
                ->with('error', 'Karyawan bukan dari department yang Anda kelola')
                ->withInput();
        }
    }

    // ===============================
    // CEK DUPLIKASI ONCALL
    // ===============================
    $existsOnCall = Jadwal::where('karyawan_id', $request->karyawan_id)
        ->whereDate('date', $request->tanggal_oncall)
        ->where('type', 'oncall')
        ->exists();

    if ($existsOnCall) {
        return back()
            ->with('error', 'Karyawan sudah memiliki jadwal OnCall di tanggal tersebut')
            ->withInput();
    }

    // ===============================
    // SIMPAN DATA (TRANSACTION)
    // ===============================
    DB::beginTransaction();

    try {
        // 1️⃣ JADWAL ONCALL
        $jadwal = Jadwal::create([
            'jadwal_id' => Jadwal::generateJadwalId(),
            'karyawan_id' => $request->karyawan_id,
            'shift_id' => 'SHIFT-ONCALL',
            'date' => $request->tanggal_oncall,
            'status' => 'normal',
            'type' => 'oncall',
            'is_active' => true,
            'notes' => 'OnCall: ' . $request->deskripsi,
            'created_by_user_id' => $user->user_id,
        ]);

        // 2️⃣ ABSEN ONCALL (AUTO CREATE VIA OBSERVER / TRIGGER)
        $absen = Absen::where('jadwal_id', $jadwal->jadwal_id)->first();
        if ($absen) {
            $absen->update([
                'type' => 'oncall',
                'status' => 'scheduled',
            ]);
        }

        // 3️⃣ LEMBUR ONCALL
        Lembur::create([
            'lembur_id' => Lembur::generateLemburId(),
            'karyawan_id' => $request->karyawan_id,
            'absen_id' => null,
            'oncall_jadwal_id' => $jadwal->jadwal_id,
            'tanggal_lembur' => $request->tanggal_oncall,
            'jam_mulai' => $request->jam_mulai . ':00',
            'jam_selesai' => null,
            'total_jam' => 0,
            'deskripsi_pekerjaan' => $request->deskripsi,
            'bukti_foto' => null,
            'jenis_lembur' => 'oncall',
            'status' => 'waiting_checkin',
            'koordinator_status' => 'pending',
            'submitted_via' => 'web',
            'created_by_user_id' => $user->user_id,
        ]);

        DB::commit();

        return redirect()
            ->route('admin.oncall.index')
            ->with('success', "OnCall berhasil di-assign ke {$karyawan->full_name}");

    } catch (\Exception $e) {
        DB::rollBack();

        return back()
            ->with('error', 'Gagal membuat OnCall: ' . $e->getMessage())
            ->withInput();
    }
}

    /**
     * Display the specified OnCall
     */
    public function show($id)
    {
        $user = Auth::user();
        $isAdmin = $user->role === 'admin';

        $query = Lembur::with([
            'karyawan.department',
            'jadwalOnCall.shift',
            'absen',
            'createdBy',
            'tunjanganKaryawan',
        ])
            ->where('lembur_id', $id)
            ->where('jenis_lembur', 'oncall');

        if (! $isAdmin) {
            $query->where('created_by_user_id', $user->user_id);
        }

        $lembur = $query->first();

        if (! $lembur) {
            return redirect()->route('admin.oncall.index')
                ->with('error', 'Data OnCall tidak ditemukan');
        }

        $timeline = [
            [
                'status' => 'assigned',
                'label' => 'Di-Assign',
                'datetime' => $lembur->created_at,
                'user' => $lembur->createdBy->name ?? '-',
                'completed' => true,
            ],
            [
                'status' => 'checked_in',
                'label' => 'Check In',
                'datetime' => $lembur->started_at,
                'user' => $lembur->karyawan->full_name ?? '-',
                'completed' => (bool) $lembur->started_at,
            ],
            [
                'status' => 'checked_out',
                'label' => 'Check Out',
                'datetime' => $lembur->completed_at,
                'user' => $lembur->karyawan->full_name ?? '-',
                'completed' => (bool) $lembur->completed_at,
            ],
            [
                'status' => 'submitted',
                'label' => 'Submitted',
                'datetime' => $lembur->submitted_at,
                'user' => $lembur->karyawan->full_name ?? '-',
                'completed' => in_array($lembur->status, ['submitted', 'approved']),
            ],
            [
                'status' => 'approved',
                'label' => 'Approved',
                'datetime' => $lembur->approved_at,
                'user' => $lembur->approvedBy->name ?? '-',
                'completed' => $lembur->status === 'approved',
            ],
        ];

        return view('admin.oncall.showOncall', compact('lembur', 'timeline'));
    }

    /**
     * Show the form for editing the specified OnCall
     */
    public function edit($id)
    {
        $user = Auth::user();
        $isAdmin = $user->role === 'admin';

        $query = Lembur::with(['karyawan', 'jadwalOnCall'])
            ->where('lembur_id', $id)
            ->where('jenis_lembur', 'oncall');

        if (! $isAdmin) {
            $query->where('created_by_user_id', $user->user_id);
        }

        $lembur = $query->first();

        if (! $lembur) {
            return redirect()->route('admin.oncall.index')
                ->with('error', 'Data OnCall tidak ditemukan');
        }

        if ($lembur->status !== 'waiting_checkin') {
            return redirect()->route('admin.oncall.show', $lembur->lembur_id)
                ->with('error', 'OnCall tidak dapat diedit karena karyawan sudah check-in');
        }

        if ($isAdmin) {
            $karyawans = Karyawan::orderBy('full_name')->get();
        } else {
            $koordinatorDept = $user->karyawan->department_id ?? null;
            $karyawans = Karyawan::where('department_id', $koordinatorDept)
                ->orderBy('full_name')
                ->get();
        }

        return view('admin.oncall.editOncall', compact('lembur', 'karyawans'));
    }

    /**
     * Update the specified OnCall
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $isAdmin = $user->role === 'admin';

        $query = Lembur::with('jadwalOnCall')
            ->where('lembur_id', $id)
            ->where('jenis_lembur', 'oncall');

        if (! $isAdmin) {
            $query->where('created_by_user_id', $user->user_id);
        }

        $lembur = $query->first();

        if (! $lembur) {
            return redirect()->route('admin.oncall.index')
                ->with('error', 'Data OnCall tidak ditemukan');
        }

        if ($lembur->status !== 'waiting_checkin') {
            return redirect()->route('admin.oncall.show', $lembur->lembur_id)
                ->with('error', 'OnCall tidak dapat diedit');
        }

        $validator = Validator::make($request->all(), [
            'tanggal_oncall' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required|date_format:H:i',
            'deskripsi' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $lembur->jadwalOnCall->update([
                'date' => $request->tanggal_oncall,
                'notes' => 'OnCall: '.$request->deskripsi,
            ]);

            if ($lembur->jadwalOnCall->absen) {
                $lembur->jadwalOnCall->absen->update([
                    'date' => $request->tanggal_oncall,
                ]);
            }

            $lembur->update([
                'tanggal_lembur' => $request->tanggal_oncall,
                'jam_mulai' => $request->jam_mulai.':00',
                'deskripsi_pekerjaan' => $request->deskripsi,
            ]);

            DB::commit();

            return redirect()->route('admin.oncall.show', $lembur->lembur_id)
                ->with('success', 'OnCall berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal update OnCall: '.$e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified OnCall
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $isAdmin = $user->role === 'admin';

        $query = Lembur::with('jadwalOnCall.absen')
            ->where('lembur_id', $id)
            ->where('jenis_lembur', 'oncall');

        if (! $isAdmin) {
            $query->where('created_by_user_id', $user->user_id);
        }

        $lembur = $query->first();

        if (! $lembur) {
            return redirect()->route('admin.oncall.index')
                ->with('error', 'Data OnCall tidak ditemukan');
        }

        if ($lembur->status !== 'waiting_checkin') {
            return redirect()->route('admin.oncall.show', $lembur->lembur_id)
                ->with('error', 'OnCall tidak dapat dihapus karena sudah berjalan');
        }

        DB::beginTransaction();
        try {
            $jadwal = $lembur->jadwalOnCall;
            $absen = $jadwal->absen ?? null;

            $lembur->delete();

            if ($absen) {
                $absen->delete();
            }

            $jadwal->delete();

            DB::commit();

            return redirect()->route('admin.oncall.index')
                ->with('success', 'OnCall berhasil dibatalkan');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Gagal menghapus OnCall: '.$e->getMessage());
        }
    }
}

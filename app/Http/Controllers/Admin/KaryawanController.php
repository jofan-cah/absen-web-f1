<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absen;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\Department;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class KaryawanController extends Controller
{


    public function index(Request $request)
    {
        // Base query dengan relasi
        $query = Karyawan::with(['user', 'department']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('department', function ($deptQuery) use ($search) {
                        $deptQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->where('department_id', $request->get('department'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('employment_status', $request->get('status'));
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        // Validate sort fields
        $allowedSortFields = ['full_name', 'nip', 'position', 'hire_date', 'created_at'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Paginate results
        $karyawans = $query->paginate(30)->withQueryString();

        // Statistics untuk dashboard cards
        $totalKaryawan = Karyawan::count();
        $activeKaryawan = Karyawan::where('employment_status', 'aktif')->count();
        $totalDepartments = Department::where('is_active', true)->count();
        $newThisMonth = Karyawan::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Data untuk dropdown filter department
        $departments = Department::where('is_active', true)
            ->orderBy('name')
            ->get(['department_id', 'name']);

        return view('admin.karyawan.indexKaryawan', compact(
            'karyawans',
            'totalKaryawan',
            'activeKaryawan',
            'totalDepartments',
            'newThisMonth',
            'departments'
        ));
    }
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'karyawan_ids' => 'required|array',
            'karyawan_ids.*' => 'exists:karyawans,karyawan_id'
        ]);

        try {
            DB::beginTransaction();

            // Delete related records first (if needed)
            $karyawanIds = $request->karyawan_ids;

            // Delete absens
            Absen::whereIn('karyawan_id', $karyawanIds)->delete();

            // Delete jadwals
            Jadwal::whereIn('karyawan_id', $karyawanIds)->delete();

            // Delete karyawans
            $deletedCount = Karyawan::whereIn('karyawan_id', $karyawanIds)->delete();

            DB::commit();

            return redirect()->route('admin.karyawan.index')
                ->with('success', "Berhasil menghapus {$deletedCount} karyawan");
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.karyawan.index')
                ->with('error', 'Gagal menghapus karyawan: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        // Buat query sama seperti di index
        $query = Karyawan::with(['user', 'department']);

        // Apply filters dari request
        if ($request->filled('search')) {
            // Same search logic as index
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->get('department'));
        }

        if ($request->filled('status')) {
            $query->where('employment_status', $request->get('status'));
        }

        // Jika ada karyawan_ids (dari bulk export)
        if ($request->filled('karyawan_ids')) {
            $query->whereIn('karyawan_id', $request->karyawan_ids);
        }

        $karyawans = $query->get();

        // Export to Excel (gunakan Laravel Excel atau manual CSV)
        return response()->streamDownload(function () use ($karyawans) {
            $handle = fopen('php://output', 'w');

            // Header CSV
            fputcsv($handle, [
                'NIP',
                'Nama Lengkap',
                'Email',
                'Department',
                'Posisi',
                'Status',
                'Tanggal Bergabung',
                'Telepon',
                'Alamat'
            ]);

            // Data
            foreach ($karyawans as $karyawan) {
                fputcsv($handle, [
                    $karyawan->nip,
                    $karyawan->full_name,
                    $karyawan->user->email ?? '',
                    $karyawan->department->name ?? '',
                    $karyawan->position,
                    $karyawan->employment_status,
                    $karyawan->hire_date ? $karyawan->hire_date->format('d/m/Y') : '',
                    $karyawan->phone,
                    $karyawan->address
                ]);
            }

            fclose($handle);
        }, 'data-karyawan-' . date('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        return view('admin.karyawan.createKaryawan', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'department_id' => 'required|exists:departments,department_id',
            'nip' => 'required|string|max:50|unique:karyawans',
            'full_name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'hire_date' => 'required|date',
            'birth_date' => 'nullable|date|before:hire_date',
            'gender' => 'required|in:L,P',
            'staff_status' => 'required|in:staff,koordinator,wakil_koordinator',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);

        try {
            DB::beginTransaction();

            // Create User first
            $user = User::create([

                'user_id' => User::generateUserId(),
                'nip' => $request->nip,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'karyawan',
                'is_active' => true,
            ]);

            // Handle photo upload
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $filename = 'karyawan_' . $user->user_id . '_' . time() . '.' . $photo->getClientOriginalExtension();
                $photoPath = Storage::disk('s3')->putFileAs('karyawan_photos', $photo, $filename);
            }

            // Create Karyawan
            Karyawan::create([
                'karyawan_id' => Karyawan::generateKaryawanId(),
                'user_id' => $user->user_id,
                'department_id' => $request->department_id,
                'nip' => $request->nip,
                'full_name' => $request->full_name,
                'position' => $request->position,
                'phone' => $request->phone,
                'address' => $request->address,
                'hire_date' => $request->hire_date,
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
                'photo' => $photoPath,
                'employment_status' => 'active',
                'staff_status' => $request->staff_status,
            ]);

            DB::commit();

            return redirect()->route('admin.karyawan.index')
                ->with('success', 'Karyawan berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollback();

            // Delete uploaded photo if exists
            if (isset($photoPath) && $photoPath && Storage::disk('s3')->exists($photoPath)) {
                Storage::disk('s3')->delete($photoPath);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan karyawan: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        try {
            $karyawan = Karyawan::findOrFail($id);

            // Toggle status
            if ($karyawan->employment_status === 'aktif') {
                $karyawan->employment_status = 'tidak_aktif';
                $message = 'Karyawan berhasil dinonaktifkan';
            } else {
                $karyawan->employment_status = 'aktif';
                $message = 'Karyawan berhasil diaktifkan';
            }

            $karyawan->save();

            return redirect()->route('admin.karyawan.show', $id)
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('admin.karyawan.show', $id)
                ->with('error', 'Gagal mengubah status karyawan');
        }
    }

    public function resetPassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        try {
            $karyawan = Karyawan::with('user')->findOrFail($id);

            if (!$karyawan->user) {
                return redirect()->route('admin.karyawan.show', $id)
                    ->with('error', 'Karyawan tidak memiliki akun user');
            }

            $karyawan->user->update([
                'password' => Hash::make($request->password)
            ]);

            return redirect()->route('admin.karyawan.show', $id)
                ->with('success', 'Password berhasil direset');
        } catch (\Exception $e) {
            return redirect()->route('admin.karyawan.show', $id)
                ->with('error', 'Gagal reset password: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $karyawan = Karyawan::with(['user', 'department', 'absens'])
            ->findOrFail($id);

        // Hitung statistik absensi bulan ini
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $stats = [
            'total_hadir' => $karyawan->absens()
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->whereIn('status', ['present', 'late'])
                ->count(),

            'total_alpha' => $karyawan->absens()
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->where('status', 'absent')
                ->count(),

            'total_terlambat' => $karyawan->absens()
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->where('status', 'late')
                ->count(),

            'total_jam_kerja' => $karyawan->absens()
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->whereNotNull('work_hours')
                ->sum('work_hours')
        ];

        // Ambil absensi bulan ini untuk tabel
        $recentAbsens = $karyawan->absens()
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();

        return view('admin.karyawan.showKaryawan', compact('karyawan', 'stats', 'recentAbsens'));
    }
    public function edit(Karyawan $karyawan)
    {
        $departments = Department::where('is_active', true)->get();
        $karyawan->load(['user', 'department']);
        return view('admin.karyawan.editKaryawan', compact('karyawan', 'departments'));
    }


    public function update(Request $request, Karyawan $karyawan)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $karyawan->user->user_id . ',user_id',
            'department_id' => 'required|exists:departments,department_id',
            'nip' => 'required|string|max:50|unique:karyawans,nip,' . $karyawan->karyawan_id . ',karyawan_id',
            'full_name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'hire_date' => 'required|date',
            'birth_date' => 'nullable|date|before:hire_date',
            'gender' => 'required|in:L,P',
            'employment_status' => 'required|in:active,inactive,terminated',
            'staff_status' => 'required',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // Update User
            $karyawan->user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // Handle photo upload
            $photoPath = $karyawan->photo; // Keep existing photo
            if ($request->hasFile('photo')) {
                // Delete old photo
                if ($karyawan->photo && Storage::disk('s3')->exists($karyawan->photo)) {
                    Storage::disk('s3')->delete($karyawan->photo);
                }

                $photo = $request->file('photo');
                $filename = 'karyawan_' . $karyawan->user_id . '_' . time() . '.' . $photo->getClientOriginalExtension();
                $photoPath = $photo->storeAs('karyawan_photos', $filename, 'public');
            }

            // Update Karyawan
            $karyawan->update([
                'department_id' => $request->department_id,
                'nip' => $request->nip,
                'full_name' => $request->full_name,
                'position' => $request->position,
                'phone' => $request->phone,
                'address' => $request->address,
                'hire_date' => $request->hire_date,
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
                'employment_status' => $request->employment_status,
                'staff_status' => $request->staff_status,
                'photo' => $photoPath,
            ]);

            DB::commit();

            return redirect()->route('admin.karyawan.index')
                ->with('success', 'Data karyawan berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate karyawan: ' . $e->getMessage());
        }
    }

    public function destroy(Karyawan $karyawan)
    {
        try {
            DB::beginTransaction();

            // Delete photo if exists
            if ($karyawan->photo && Storage::disk('s3')->exists($karyawan->photo)) {
                Storage::disk('s3')->delete($karyawan->photo);
            }

            // Delete related records
            $karyawan->absens()->delete();
            $karyawan->jadwals()->delete();

            // Delete karyawan (this will also delete user if cascaded)
            $name = $karyawan->full_name;
            $karyawan->delete();

            // If user is not deleted by cascade, delete manually
            if ($karyawan->user) {
                $karyawan->user->delete();
            }

            DB::commit();

            return redirect()->route('admin.karyawan.index')
                ->with('success', "Karyawan {$name} berhasil dihapus");
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.karyawan.index')
                ->with('error', 'Gagal menghapus karyawan: ' . $e->getMessage());
        }
    }
}

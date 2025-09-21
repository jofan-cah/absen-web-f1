<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    // Update method index untuk search dan filter
    public function index(Request $request)
    {
        // Base query dengan relasi
        $query = Department::with(['manager', 'karyawans'])
            ->withCount('karyawans');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('manager', function ($managerQuery) use ($search) {
                        $managerQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->get('status') == '1');
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        // Validate sort fields
        $allowedSortFields = ['name', 'code', 'created_at', 'karyawans_count'];
        if (in_array($sortField, $allowedSortFields)) {
            if ($sortField === 'karyawans_count') {
                $query->orderBy('karyawans_count', $sortDirection);
            } else {
                $query->orderBy($sortField, $sortDirection);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Paginate results
        $departments = $query->paginate(15)->withQueryString();

        return view('admin.department.indexDepartement', compact('departments'));
    }

    // Perbaiki method show untuk employment_status yang benar
    public function show(Department $department)
    {
        $department->load(['manager', 'karyawans.user']);
        $department->loadCount([
            'karyawans as total_karyawan',
            'karyawans as active_karyawan' => function ($query) {
                $query->where('employment_status', 'aktif'); // Perbaiki dari 'active' ke 'aktif'
            }
        ]);

        return view('admin.department.showDepartement', compact('department'));
    }

    // Perbaiki method destroy untuk employment_status yang benar
    public function destroy(Department $department)
    {
        // Check if department has active karyawan
        $activeKaryawanCount = $department->karyawans()
            ->where('employment_status', 'aktif') // Perbaiki dari 'active' ke 'aktif'
            ->count();

        if ($activeKaryawanCount > 0) {
            return back()->withErrors([
                'delete' => 'Department tidak bisa dihapus karena masih memiliki ' . $activeKaryawanCount . ' karyawan aktif'
            ]);
        }

        $department->delete();

        return redirect()->route('admin.department.index')
            ->with('success', 'Department berhasil dihapus');
    }

    // Perbaiki method getKaryawans untuk employment_status yang benar
    public function getKaryawans(Department $department)
    {
        $karyawans = $department->karyawans()
            ->where('employment_status', 'aktif') // Perbaiki dari 'active' ke 'aktif'
            ->with('user:user_id,name')
            ->select('karyawan_id', 'user_id', 'full_name', 'nip', 'position')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $karyawans
        ]);
    }
    public function create()
    {
        $managers = User::where('role', 'admin')
            ->orWhereHas('karyawan', function ($query) {
                $query->whereIn('staff_status', ['koordinator', 'wakil_koordinator']);
            })
            ->get();

        return view('admin.department.createDepartement', compact('managers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments',
            'description' => 'nullable|string',
            'manager_user_id' => 'nullable|exists:users,user_id',
        ]);

        Department::create([
            'department_id' => Department::generateDepartmentId(),
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'manager_user_id' => $request->manager_user_id,
            'is_active' => true,
        ]);

        return redirect()->route('admin.department.index')
            ->with('success', 'Department berhasil ditambahkan');
    }



    public function edit(Department $department)
    {
        $managers = User::where('role', 'admin')
            ->orWhereHas('karyawan', function ($query) {
                $query->whereIn('staff_status', ['koordinator', 'wakil_koordinator']);
            })
            ->get();

        return view('admin.department.editDepartement', compact('department', 'managers'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments,code,' . $department->department_id . ',department_id',
            'description' => 'nullable|string',
            'manager_user_id' => 'nullable|exists:users,user_id',
            'is_active' => 'boolean',
        ]);

        $department->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'manager_user_id' => $request->manager_user_id,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.department.index')
            ->with('success', 'Department berhasil diupdate');
    }


    // AJAX method untuk get karyawan by department


    // Toggle active status
    public function toggleStatus(Department $department)
    {
        $department->update([
            'is_active' => !$department->is_active
        ]);

        $status = $department->is_active ? 'aktif' : 'non-aktif';

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Department berhasil diubah menjadi {$status}",
                'is_active' => $department->is_active
            ]);
        }

        return back()->with('success', "Department berhasil diubah menjadi {$status}");
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'department_ids' => 'required|array',
            'department_ids.*' => 'exists:departments,department_id'
        ]);

        try {
            DB::beginTransaction();

            $departmentIds = $request->department_ids;
            $errors = [];
            $deletedCount = 0;

            foreach ($departmentIds as $departmentId) {
                $department = Department::findOrFail($departmentId);

                // Check if department has active karyawan
                $activeKaryawanCount = $department->karyawans()
                    ->where('employment_status', 'aktif')
                    ->count();

                if ($activeKaryawanCount > 0) {
                    $errors[] = "Department '{$department->name}' tidak bisa dihapus karena masih memiliki {$activeKaryawanCount} karyawan aktif";
                    continue;
                }

                $department->delete();
                $deletedCount++;
            }

            DB::commit();

            if ($deletedCount > 0 && empty($errors)) {
                return redirect()->route('admin.department.index')
                    ->with('success', "Berhasil menghapus {$deletedCount} department");
            } elseif ($deletedCount > 0 && !empty($errors)) {
                return redirect()->route('admin.department.index')
                    ->with('warning', "Berhasil menghapus {$deletedCount} department. " . implode('. ', $errors));
            } else {
                return redirect()->route('admin.department.index')
                    ->with('error', implode('. ', $errors));
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.department.index')
                ->with('error', 'Gagal menghapus department: ' . $e->getMessage());
        }
    }

    public function bulkToggleStatus(Request $request)
    {
        $request->validate([
            'department_ids' => 'required|array',
            'department_ids.*' => 'exists:departments,department_id'
        ]);

        try {
            DB::beginTransaction();

            $departmentIds = $request->department_ids;
            $updatedCount = 0;

            foreach ($departmentIds as $departmentId) {
                $department = Department::findOrFail($departmentId);
                $department->update([
                    'is_active' => !$department->is_active
                ]);
                $updatedCount++;
            }

            DB::commit();

            return redirect()->route('admin.department.index')
                ->with('success', "Berhasil mengubah status {$updatedCount} department");
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.department.index')
                ->with('error', 'Gagal mengubah status department: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        // Buat query sama seperti di index
        $query = Department::with(['manager', 'karyawans']);

        // Apply filters dari request
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('manager', function ($managerQuery) use ($search) {
                        $managerQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->get('status') == '1');
        }

        // Jika ada department_ids (dari bulk export)
        if ($request->filled('department_ids')) {
            $query->whereIn('department_id', $request->department_ids);
        }

        $departments = $query->withCount('karyawans')->get();

        // Export to CSV
        return response()->streamDownload(function () use ($departments) {
            $handle = fopen('php://output', 'w');

            // Header CSV
            fputcsv($handle, [
                'Department ID',
                'Nama Department',
                'Kode',
                'Deskripsi',
                'Manager',
                'Email Manager',
                'Jumlah Karyawan',
                'Status',
                'Tanggal Dibuat'
            ]);

            // Data
            foreach ($departments as $department) {
                fputcsv($handle, [
                    $department->department_id,
                    $department->name,
                    $department->code,
                    $department->description ?? '',
                    $department->manager->name ?? '',
                    $department->manager->email ?? '',
                    $department->karyawans_count,
                    $department->is_active ? 'Aktif' : 'Tidak Aktif',
                    $department->created_at->format('d/m/Y H:i')
                ]);
            }

            fclose($handle);
        }, 'data-department-' . date('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}

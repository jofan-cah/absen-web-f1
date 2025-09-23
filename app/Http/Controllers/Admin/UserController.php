<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::with('karyawan.department');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.user.indexUser', compact('users'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        return redirect()->route('admin.user.index');
        // return view('admin.user.createUser');
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
         return redirect()->route('admin.user.index');
        // $request->validate([
        //     'nip' => 'nullable|string|max:50|unique:users',
        //     'name' => 'required|string|max:255',
        //     'email' => 'required|string|email|max:255|unique:users',
        //     'password' => 'required|string|min:8|confirmed',
        //     'role' => 'required|in:admin,user',
        //     'is_active' => 'boolean',
        // ]);

        // User::create([
        //     'user_id' => User::generateUserId(),
        //     'nip' => $request->nip,
        //     'name' => $request->name,
        //     'email' => $request->email,
        //     'password' => Hash::make($request->password),
        //     'role' => $request->role,
        //     'is_active' => $request->boolean('is_active', true),
        // ]);

        // return redirect()->route('admin.user.index')
        //     ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user->load('karyawan.department');

        return view('admin.user.showUser', compact('user'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        return view('admin.user.editUser', compact('user'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'nip' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('users')->ignore($user->user_id, 'user_id'),
            ],
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->user_id, 'user_id'),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,user',
            'is_active' => 'boolean',
        ]);

        $data = [
            'nip' => $request->nip,
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'is_active' => $request->boolean('is_active', true),
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.user.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        // Prevent deleting user with linked karyawan data
        if ($user->karyawan) {
            return redirect()->route('admin.user.index')
                ->with('error', 'User tidak dapat dihapus karena memiliki data karyawan terkait.');
        }

        // Prevent self-deletion
        if ($user->user_id === auth()->user()->user_id) {
            return redirect()->route('admin.user.index')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.user.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /**
     * Toggle user status (active/inactive)
     */
    public function toggleStatus(User $user)
    {
        // Prevent deactivating own account
        if ($user->user_id === auth()->user()->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menonaktifkan akun sendiri.'
            ], 400);
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json([
            'success' => true,
            'message' => "User berhasil {$status}.",
            'status' => $user->is_active
        ]);
    }

    /**
     * Reset user password to default
     */
    public function resetPassword(User $user)
    {
        $defaultPassword = 'password123';

        $user->update([
            'password' => Hash::make($defaultPassword)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil direset ke: ' . $defaultPassword
        ]);
    }

    /**
     * Bulk delete users
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,user_id',
        ]);

        $deletedCount = 0;
        $errors = [];
        $currentUserId = auth()->user()->user_id;

        foreach ($request->user_ids as $userId) {
            $user = User::find($userId);

            if (!$user) continue;

            // Check if trying to delete own account
            if ($user->user_id === $currentUserId) {
                $errors[] = "Tidak dapat menghapus akun sendiri.";
                continue;
            }

            // Check if user has linked karyawan
            if ($user->karyawan) {
                $errors[] = "User {$user->name} memiliki data karyawan terkait.";
                continue;
            }

            $user->delete();
            $deletedCount++;
        }

        $message = "{$deletedCount} user berhasil dihapus.";
        if (!empty($errors)) {
            $message .= " Gagal: " . implode(', ', $errors);
        }

        return response()->json([
            'success' => $deletedCount > 0,
            'message' => $message,
            'deleted_count' => $deletedCount
        ]);
    }

    /**
     * Bulk toggle status
     */
    public function bulkToggleStatus(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,user_id',
            'status' => 'required|boolean',
        ]);

        $updatedCount = 0;
        $errors = [];
        $currentUserId = auth()->user()->user_id;

        foreach ($request->user_ids as $userId) {
            $user = User::find($userId);

            if (!$user) continue;

            // Prevent deactivating own account
            if ($user->user_id === $currentUserId && !$request->boolean('status')) {
                $errors[] = "Tidak dapat menonaktifkan akun sendiri.";
                continue;
            }

            $user->update(['is_active' => $request->boolean('status')]);
            $updatedCount++;
        }

        $action = $request->boolean('status') ? 'diaktifkan' : 'dinonaktifkan';
        $message = "{$updatedCount} user berhasil {$action}.";

        if (!empty($errors)) {
            $message .= " Gagal: " . implode(', ', $errors);
        }

        return response()->json([
            'success' => $updatedCount > 0,
            'message' => $message,
            'updated_count' => $updatedCount
        ]);
    }

    /**
     * Export users data to CSV
     */
    public function export(Request $request)
    {
        $query = User::with('karyawan.department');

        // Apply filters
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        $filename = 'users_export_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');

            // CSV Header
            fputcsv($file, [
                'User ID',
                'NIP',
                'Nama',
                'Email',
                'Role',
                'Status',
                'Nama Karyawan',
                'Department',
                'Posisi',
                'Tanggal Dibuat',
                'Terakhir Login'
            ]);

            // CSV Data
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->user_id,
                    $user->nip ?? '-',
                    $user->name,
                    $user->email,
                    ucfirst($user->role),
                    $user->is_active ? 'Aktif' : 'Nonaktif',
                    $user->karyawan->full_name ?? '-',
                    $user->karyawan->department->name ?? '-',
                    $user->karyawan->position ?? '-',
                    $user->created_at->format('d/m/Y H:i'),
                    $user->updated_at->format('d/m/Y H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

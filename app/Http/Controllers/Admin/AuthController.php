<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{


     public function showProfile()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        $departments = Department::where('is_active', true)->get();

        return view('admin.profile.edit', compact('user', 'karyawan', 'departments'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            // 'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id() . ',user_id',
            'password' => 'nullable|min:8|confirmed',
            'full_name' => 'required|string|max:255',
            'position' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:L,P',
            'department_id' => 'nullable|exists:departments,department_id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $user = Auth::user();
        $karyawan = $user->karyawan;

        // Start transaction
        \DB::beginTransaction();

        try {
            // Update User data
            $userData = [
                'name' => $request->full_name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            // Update Karyawan data (jika ada)
            if ($karyawan) {
                $karyawanData = [
                    'full_name' => $request->full_name,
                    'position' => $request->position,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'birth_date' => $request->birth_date,
                    'gender' => $request->gender,
                    'department_id' => $request->department_id,
                ];

                // Handle photo upload
                if ($request->hasFile('photo')) {
                    // Delete old photo
                    if ($karyawan->photo) {
                        Storage::disk('s3')->delete($karyawan->photo);
                    }

                    // Store new photo
                    $path = $request->file('photo')->store('karyawan', 'public');
                    $karyawanData['photo'] = $path;
                }

                $karyawan->update($karyawanData);

                // OTOMATIS SYNC: Update User.name dengan Karyawan.full_name
                $user->update(['name' => $request->full_name]);
            }

            \DB::commit();

            return redirect()->route('admin.profile')
                ->with('success', 'Profil berhasil diperbarui');

        } catch (\Exception $e) {
            \DB::rollback();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    public function showLogin()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nip' => 'required|string',
            'password' => 'required',
        ]);

        // Try to find user by NIP
        $user = \App\Models\User::where('nip', $request->nip)->first();

        if (!$user || !\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'nip' => 'NIP atau password salah.',
            ]);
        }

        if ($user->role !== 'admin' && $user->role !== 'koordinator' && $user->role !== 'wakil_coordinator') {
            return back()->withErrors([
                'nip' => 'Akses ditolak. Hanya admin yang bisa login.',
            ]);
        }

        if (!$user->is_active) {
            return back()->withErrors([
                'nip' => 'Akun tidak aktif.',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();
        // dd($user);

        return redirect()->intended('/admin/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}

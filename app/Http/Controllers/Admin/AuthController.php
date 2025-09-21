<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
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

        if ($user->role !== 'admin') {
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

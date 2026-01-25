<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Karyawan;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseApiController
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nip' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        // PERBAIKAN: Cari berdasarkan NIP di tabel karyawans
        $karyawan = Karyawan::with(['user', 'department'])
            ->where('nip', $request->nip)
            ->first();

        if (!$karyawan || !$karyawan->user) {
            // Log login gagal - NIP tidak ditemukan
            ActivityLog::logLoginFailed($request->nip, 'NIP tidak ditemukan');
            return $this->notFoundResponse('NIP tidak ditemukan');
        }

        $user = $karyawan->user;

        // Check karyawan status
        if ($karyawan->employment_status !== 'active') {
            ActivityLog::logLoginFailed($request->nip, 'Status karyawan tidak aktif');
            return $this->forbiddenResponse('Status karyawan tidak aktif');
        }

        // Check password
        if (!Hash::check($request->password, $user->password)) {
            ActivityLog::logLoginFailed($request->nip, 'Password salah');
            return $this->unauthorizedResponse('Password salah');
        }

        // Check user active
        if (!$user->is_active) {
            ActivityLog::logLoginFailed($request->nip, 'Akun tidak aktif');
            return $this->forbiddenResponse('Akun tidak aktif');
        }

        // Create token
        $token = $user->createToken('mobile_app')->plainTextToken;

        // Log login sukses
        ActivityLog::logLogin($user, 'mobile');

        return $this->successResponse([
            'user' => $user,
            'karyawan' => $karyawan,
            'token' => $token,
            'token_type' => 'Bearer'
        ], 'Login berhasil');
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $karyawan = $user->karyawan->load('department');

        return $this->successResponse([
            'user' => $user,
            'karyawan' => $karyawan
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        // Log logout sebelum delete token
        ActivityLog::logLogout($user, 'mobile');

        $user->currentAccessToken()->delete();
        return $this->successResponse(null, 'Logout berhasil');
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->errorResponse('Password lama tidak sesuai', 400);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        // Log perubahan password
        ActivityLog::log('update', 'User mengubah password', [
            'module' => 'User',
            'module_id' => $user->user_id,
        ]);

        return $this->successResponse(null, 'Password berhasil diubah');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Models\DeviceToken;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AuthController extends BaseApiController
{
    /**
     * Helper untuk log activity dengan safe try-catch
     */
    private function safeLog(callable $callback): void
    {
        try {
            Log::debug('ActivityLog: attempting to log...');
            $result = $callback();
            Log::debug('ActivityLog: success, ID: ' . ($result->id ?? 'null'));
        } catch (\Exception $e) {
            Log::warning('ActivityLog failed: ' . $e->getMessage());
            Log::warning('ActivityLog trace: ' . $e->getTraceAsString());
        }
    }

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
            $this->safeLog(fn() => ActivityLog::logLoginFailed($request->nip, 'NIP tidak ditemukan'));
            return $this->notFoundResponse('NIP tidak ditemukan');
        }

        $user = $karyawan->user;

        // Check karyawan status
        if ($karyawan->employment_status !== 'active') {
            $this->safeLog(fn() => ActivityLog::logLoginFailed($request->nip, 'Status karyawan tidak aktif'));
            return $this->forbiddenResponse('Status karyawan tidak aktif');
        }

        // Check password
        if (!Hash::check($request->password, $user->password)) {
            $this->safeLog(fn() => ActivityLog::logLoginFailed($request->nip, 'Password salah'));
            return $this->unauthorizedResponse('Password salah');
        }

        // Check user active
        if (!$user->is_active) {
            $this->safeLog(fn() => ActivityLog::logLoginFailed($request->nip, 'Akun tidak aktif'));
            return $this->forbiddenResponse('Akun tidak aktif');
        }

        // Create token
        $token = $user->createToken('mobile_app')->plainTextToken;

        // Log login sukses
        $this->safeLog(fn() => ActivityLog::logLogin($user, 'mobile'));

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
        $user     = $request->user();
        $karyawan = $user->karyawan;

        // ── Hapus SEMUA device token milik karyawan ini saat logout ──────────
        // Dilakukan tanpa syarat agar auto-logout / session expire pun aman.
        // Saat login ulang, mobile app wajib register ulang device token.
        if ($karyawan) {
            $deleted = DeviceToken::where('karyawan_id', $karyawan->karyawan_id)->delete();

            Log::info('DeviceTokens deleted on logout', [
                'karyawan_id' => $karyawan->karyawan_id,
                'deleted'     => $deleted,
            ]);
        }

        // Log logout sebelum delete sanctum token
        $this->safeLog(fn() => ActivityLog::logLogout($user, 'mobile'));

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
        $this->safeLog(fn() => ActivityLog::log('update', 'User mengubah password', [
            'module' => 'User',
            'module_id' => $user->user_id,
        ]));

        return $this->successResponse(null, 'Password berhasil diubah');
    }
}

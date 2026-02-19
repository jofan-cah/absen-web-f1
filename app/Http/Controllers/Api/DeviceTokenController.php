<?php

namespace App\Http\Controllers\Api;

use App\Models\DeviceToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeviceTokenController extends BaseApiController
{
    /**
     * Save/Update device token dari Flutter
     * POST /api/device/device-token
     *
     * Fix: 1 device_token hanya boleh dimiliki 1 karyawan.
     *      Jika token sudah terdaftar atas nama karyawan lain,
     *      record lama DIHAPUS (bukan sekadar dinonaktifkan)
     *      sebelum token ini diklaim akun yang sedang login.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'device_token' => 'required|string',
                'device_type'  => 'required|in:android,ios',
                'device_name'  => 'nullable|string|max:255',
            ]);

            $user       = auth()->user();
            $karyawan   = $user->karyawan;
            $karyawanId = $karyawan?->karyawan_id;

            if (!$karyawanId) {
                return $this->errorResponse('User tidak memiliki data karyawan', 400);
            }

            $token = $request->device_token;

            // ── Hapus semua token lama milik karyawan ini (1 karyawan = 1 token) ─
            DeviceToken::where('karyawan_id', $karyawanId)
                ->where('device_token', '!=', $token)
                ->delete();

            // ── Hapus juga token ini kalau dimiliki karyawan lain ────────────
            DeviceToken::where('device_token', $token)
                ->where('karyawan_id', '!=', $karyawanId)
                ->delete();

            // ── Upsert satu-satunya token untuk karyawan ini ─────────────────
            $deviceToken = DeviceToken::updateOrCreate(
                ['karyawan_id' => $karyawanId],
                [
                    'user_id'      => $user->user_id,
                    'device_token' => $token,
                    'device_type'  => $request->device_type,
                    'device_name'  => $request->device_name,
                    'is_active'    => true,
                    'last_used_at' => now(),
                ]
            );

            $wasCreated = $deviceToken->wasRecentlyCreated;

            Log::info('DeviceToken: ' . ($wasCreated ? 'registered' : 'updated'), [
                'karyawan_id' => $karyawanId,
                'device_type' => $request->device_type,
            ]);

            return $wasCreated
                ? $this->createdResponse($deviceToken, 'Device token berhasil didaftarkan')
                : $this->successResponse($deviceToken, 'Device token berhasil diupdate');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            Log::error('DeviceToken store failed', [
                'error' => $e->getMessage(),
            ]);
            return $this->serverErrorResponse('Gagal menyimpan device token');
        }
    }

    /**
     * Get all device tokens for current user
     * GET /api/device/device-token
     */
    public function index(Request $request)
    {
        try {
            $karyawanId = $request->user()->karyawan?->karyawan_id;

            if (!$karyawanId) {
                return $this->errorResponse('User tidak memiliki data karyawan', 400);
            }

            $tokens = DeviceToken::where('karyawan_id', $karyawanId)
                ->orderByDesc('last_used_at')
                ->get();

            return $this->successResponse($tokens, 'Data device token berhasil diambil');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data device token');
        }
    }

    /**
     * Delete device token (biasanya dipanggil saat logout)
     * DELETE /api/device/device-token
     */
    public function destroy(Request $request)
    {
        try {
            $request->validate(['device_token' => 'required|string']);

            $karyawanId = $request->user()->karyawan?->karyawan_id;

            if (!$karyawanId) {
                return $this->errorResponse('User tidak memiliki data karyawan', 400);
            }

            $deleted = DeviceToken::where('device_token', $request->device_token)
                ->where('karyawan_id', $karyawanId)
                ->delete();

            if ($deleted) {
                Log::info('DeviceToken deleted', [
                    'karyawan_id'  => $karyawanId,
                    'token_prefix' => substr($request->device_token, 0, 20) . '...',
                ]);
                return $this->noContentResponse('Device token berhasil dihapus');
            }

            return $this->notFoundResponse('Device token tidak ditemukan');

        } catch (\Exception $e) {
            Log::error('DeviceToken delete failed', ['error' => $e->getMessage()]);
            return $this->serverErrorResponse('Gagal menghapus device token');
        }
    }

    /**
     * Deactivate device token
     * POST /api/device/device-token/deactivate
     */
    public function deactivate(Request $request)
    {
        try {
            $request->validate(['device_token' => 'required|string']);

            $karyawanId = $request->user()->karyawan?->karyawan_id;

            if (!$karyawanId) {
                return $this->errorResponse('User tidak memiliki data karyawan', 400);
            }

            $dt = DeviceToken::where('device_token', $request->device_token)
                ->where('karyawan_id', $karyawanId)
                ->first();

            if (!$dt) {
                return $this->notFoundResponse('Device token tidak ditemukan');
            }

            $dt->update(['is_active' => false]);

            Log::info('DeviceToken deactivated', ['karyawan_id' => $karyawanId]);

            return $this->successResponse($dt, 'Device token berhasil dinonaktifkan');

        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal menonaktifkan device token');
        }
    }

    /**
     * Activate device token
     * POST /api/device/device-token/activate
     */
    public function activate(Request $request)
    {
        try {
            $request->validate(['device_token' => 'required|string']);

            $karyawanId = $request->user()->karyawan?->karyawan_id;

            if (!$karyawanId) {
                return $this->errorResponse('User tidak memiliki data karyawan', 400);
            }

            $dt = DeviceToken::where('device_token', $request->device_token)
                ->where('karyawan_id', $karyawanId)
                ->first();

            if (!$dt) {
                return $this->notFoundResponse('Device token tidak ditemukan');
            }

            $dt->update(['is_active' => true, 'last_used_at' => now()]);

            Log::info('DeviceToken activated', ['karyawan_id' => $karyawanId]);

            return $this->successResponse($dt, 'Device token berhasil diaktifkan');

        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengaktifkan device token');
        }
    }
}

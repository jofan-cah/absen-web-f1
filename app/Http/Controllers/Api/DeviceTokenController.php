<?php

namespace App\Http\Controllers\Api;

use App\Models\DeviceToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeviceTokenController extends BaseApiController
{
    /**
     * Save/Update device token dari Flutter
     * POST /api/device-token
     */
public function store(Request $request)
{
    try {
        $request->validate([
            'device_token' => 'required|string',
            'device_type' => 'required|in:android,ios',
            'device_name' => 'nullable|string|max:255'
        ]);

        $user = auth()->user();
        $karyawanId = $user->karyawan ? $user->karyawan->karyawan_id : null;

        if (!$karyawanId) {
            return $this->errorResponse('User tidak memiliki data karyawan', 400);
        }

        // ============================================
        // CEK APAKAH DEVICE TOKEN SUDAH ADA
        // ============================================
        $existingToken = DeviceToken::where('device_token', $request->device_token)->first();

        if ($existingToken) {
            // Token sudah ada di database

            if ($existingToken->karyawan_id === $karyawanId) {
                // ============================================
                // CASE 1: TOKEN MILIK KARYAWAN YANG SAMA
                // ============================================
                // Update data token (device_type, device_name, dll)
                $existingToken->update([
                    'user_id' => $user->user_id,
                    'device_type' => $request->device_type,
                    'device_name' => $request->device_name,
                    'is_active' => true,
                    'last_used_at' => now()
                ]);

                Log::info('Device token updated (same karyawan)', [
                    'karyawan_id' => $karyawanId,
                    'device_type' => $request->device_type
                ]);

                return $this->successResponse($existingToken, 'Device token berhasil diupdate');

            } else {
                // ============================================
                // CASE 2: TOKEN MILIK KARYAWAN LAIN
                // ============================================
                // Non-aktifkan token karyawan lama
                $existingToken->update(['is_active' => false]);

                Log::info('Deactivated token from other karyawan', [
                    'old_karyawan_id' => $existingToken->karyawan_id,
                    'new_karyawan_id' => $karyawanId,
                    'device_token' => substr($request->device_token, 0, 20) . '...'
                ]);

                // Buat token baru untuk karyawan sekarang
                $newToken = DeviceToken::create([
                    'user_id' => $user->user_id,
                    'karyawan_id' => $karyawanId,
                    'device_token' => $request->device_token,
                    'device_type' => $request->device_type,
                    'device_name' => $request->device_name,
                    'is_active' => true,
                    'last_used_at' => now()
                ]);

                Log::info('Created new token for current karyawan', [
                    'karyawan_id' => $karyawanId,
                    'device_type' => $request->device_type
                ]);

                return $this->createdResponse($newToken, 'Device token berhasil didaftarkan');
            }

        } else {
            // ============================================
            // CASE 3: TOKEN BELUM ADA DI DATABASE
            // ============================================
            // Buat token baru
            $newToken = DeviceToken::create([
                'user_id' => $user->user_id,
                'karyawan_id' => $karyawanId,
                'device_token' => $request->device_token,
                'device_type' => $request->device_type,
                'device_name' => $request->device_name,
                'is_active' => true,
                'last_used_at' => now()
            ]);

            Log::info('New device token registered', [
                'karyawan_id' => $karyawanId,
                'device_type' => $request->device_type
            ]);

            return $this->createdResponse($newToken, 'Device token berhasil didaftarkan');
        }

    } catch (\Illuminate\Validation\ValidationException $e) {
        return $this->validationErrorResponse($e->errors());
    } catch (\Exception $e) {
        Log::error('Device token store failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return $this->serverErrorResponse('Gagal menyimpan device token');
    }
}

    /**
     * Get all device tokens for current user
     * GET /api/device-tokens
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            $karyawanId = $user->karyawan ? $user->karyawan->karyawan_id : null;

            if (!$karyawanId) {
                return $this->errorResponse('User tidak memiliki data karyawan', 400);
            }

            $tokens = DeviceToken::where('karyawan_id', $karyawanId)
                                 ->orderBy('last_used_at', 'desc')
                                 ->get();

            return $this->successResponse($tokens, 'Data device token berhasil diambil');

        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengambil data device token');
        }
    }

    /**
     * Delete device token (logout)
     * DELETE /api/device-token
     */
    public function destroy(Request $request)
    {
        try {
            $request->validate([
                'device_token' => 'required|string'
            ]);

            $user = auth()->user();
            $karyawanId = $user->karyawan ? $user->karyawan->karyawan_id : null;

            if (!$karyawanId) {
                return $this->errorResponse('User tidak memiliki data karyawan', 400);
            }

            $deleted = DeviceToken::where('device_token', $request->device_token)
                                  ->where('karyawan_id', $karyawanId)
                                  ->delete();

            if ($deleted) {
                Log::info('Device token deleted', [
                    'karyawan_id' => $karyawanId,
                    'device_token' => substr($request->device_token, 0, 20) . '...'
                ]);

                return $this->noContentResponse('Device token berhasil dihapus');
            } else {
                return $this->notFoundResponse('Device token tidak ditemukan');
            }

        } catch (\Exception $e) {
            Log::error('Device token delete failed', [
                'error' => $e->getMessage()
            ]);

            return $this->serverErrorResponse('Gagal menghapus device token');
        }
    }

    /**
     * Deactivate device token
     * POST /api/device-token/deactivate
     */
    public function deactivate(Request $request)
    {
        try {
            $request->validate([
                'device_token' => 'required|string'
            ]);

            $user = auth()->user();
            $karyawanId = $user->karyawan ? $user->karyawan->karyawan_id : null;

            if (!$karyawanId) {
                return $this->errorResponse('User tidak memiliki data karyawan', 400);
            }

            $deviceToken = DeviceToken::where('device_token', $request->device_token)
                                      ->where('karyawan_id', $karyawanId)
                                      ->first();

            if ($deviceToken) {
                $deviceToken->update(['is_active' => false]);

                Log::info('Device token deactivated', ['karyawan_id' => $karyawanId]);

                return $this->successResponse($deviceToken, 'Device token berhasil dinonaktifkan');
            } else {
                return $this->notFoundResponse('Device token tidak ditemukan');
            }

        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal menonaktifkan device token');
        }
    }

    /**
     * Activate device token
     * POST /api/device-token/activate
     */
    public function activate(Request $request)
    {
        try {
            $request->validate([
                'device_token' => 'required|string'
            ]);

            $user = auth()->user();
            $karyawanId = $user->karyawan ? $user->karyawan->karyawan_id : null;

            if (!$karyawanId) {
                return $this->errorResponse('User tidak memiliki data karyawan', 400);
            }

            $deviceToken = DeviceToken::where('device_token', $request->device_token)
                                      ->where('karyawan_id', $karyawanId)
                                      ->first();

            if ($deviceToken) {
                $deviceToken->update([
                    'is_active' => true,
                    'last_used_at' => now()
                ]);

                Log::info('Device token activated', ['karyawan_id' => $karyawanId]);

                return $this->successResponse($deviceToken, 'Device token berhasil diaktifkan');
            } else {
                return $this->notFoundResponse('Device token tidak ditemukan');
            }

        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal mengaktifkan device token');
        }
    }
}

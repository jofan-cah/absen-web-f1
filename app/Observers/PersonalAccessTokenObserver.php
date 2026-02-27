<?php

namespace App\Observers;

use App\Models\DeviceToken;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;

class PersonalAccessTokenObserver
{
    /**
     * Saat Sanctum token dihapus (logout eksplisit, prune expired, atau revoke),
     * hapus juga semua device token FCM milik user tersebut.
     */
    public function deleted(PersonalAccessToken $token): void
    {
        $user     = $token->tokenable;
        $karyawan = $user?->karyawan;

        if (!$karyawan) {
            return;
        }

        $deleted = DeviceToken::where('karyawan_id', $karyawan->karyawan_id)->delete();

        if ($deleted) {
            Log::info('DeviceTokens cleaned up after Sanctum token deletion', [
                'karyawan_id' => $karyawan->karyawan_id,
                'deleted'     => $deleted,
            ]);
        }
    }
}

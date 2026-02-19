<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Bersihkan data duplikat yang sudah ada:
        //    Untuk setiap device_token yang duplikat, pertahankan hanya
        //    record terbaru (id tertinggi), hapus sisanya.
        DB::statement('
            DELETE dt1 FROM device_tokens dt1
            INNER JOIN device_tokens dt2
            WHERE dt1.id < dt2.id
              AND dt1.device_token = dt2.device_token
        ');

        // 2. Ubah kolom device_token dari text → varchar(500)
        //    agar bisa diberi unique index.
        //    FCM token panjang maksimal ~163 karakter, 500 sangat aman.
        Schema::table('device_tokens', function (Blueprint $table) {
            $table->string('device_token', 500)->change();
        });

        // 3. Tambah unique index pada device_token
        Schema::table('device_tokens', function (Blueprint $table) {
            $table->unique('device_token', 'device_tokens_token_unique');
        });
    }

    public function down(): void
    {
        Schema::table('device_tokens', function (Blueprint $table) {
            $table->dropUnique('device_tokens_token_unique');
            $table->text('device_token')->change();
        });
    }
};

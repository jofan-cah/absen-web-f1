<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lemburs', function (Blueprint $table) {
            // Timestamp kapan karyawan mulai lembur (klik "Mulai Lembur")
            $table->timestamp('started_at')
                ->nullable()
                ->after('submitted_via')
                ->comment('Timestamp saat karyawan mulai lembur');

            // Timestamp kapan karyawan selesai lembur (klik "Selesai Lembur")
            $table->timestamp('completed_at')
                ->nullable()
                ->after('started_at')
                ->comment('Timestamp saat karyawan selesai lembur');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lemburs', function (Blueprint $table) {
            $table->dropColumn(['started_at', 'completed_at']);
        });
    }
};

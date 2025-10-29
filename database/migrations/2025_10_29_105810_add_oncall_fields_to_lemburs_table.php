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
            // Tambah kolom jenis_lembur
            $table->enum('jenis_lembur', ['regular', 'oncall'])
                  ->default('regular')
                  ->after('status')
                  ->comment('Jenis lembur: regular atau oncall');

            // Tambah kolom oncall_jadwal_id
            $table->string('oncall_jadwal_id', 50)
                  ->nullable()
                  ->after('absen_id')
                  ->comment('ID jadwal oncall (untuk link ke jadwal yang dibuat khusus oncall)');

            // Tambah index untuk performa
            $table->index('oncall_jadwal_id');

            // Foreign key ke jadwals
            $table->foreign('oncall_jadwal_id')
                  ->references('jadwal_id')
                  ->on('jadwals')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lemburs', function (Blueprint $table) {
            // Drop foreign key dulu
            $table->dropForeign(['oncall_jadwal_id']);

            // Drop index
            $table->dropIndex(['oncall_jadwal_id']);

            // Drop columns
            $table->dropColumn(['jenis_lembur', 'oncall_jadwal_id']);
        });
    }
};

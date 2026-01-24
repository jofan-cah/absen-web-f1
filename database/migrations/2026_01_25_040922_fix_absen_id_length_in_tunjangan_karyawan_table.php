<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Fix absen_id column length dari 10 ke 20 karakter
     * untuk match dengan absens.absen_id
     */
    public function up(): void
    {
        Schema::table('tunjangan_karyawan', function (Blueprint $table) {
            // Drop foreign key dulu
            $table->dropForeign(['absen_id']);
        });

        Schema::table('tunjangan_karyawan', function (Blueprint $table) {
            // Ubah panjang kolom dari 10 ke 20
            $table->string('absen_id', 20)->nullable()->change();
        });

        Schema::table('tunjangan_karyawan', function (Blueprint $table) {
            // Re-add foreign key
            $table->foreign('absen_id')->references('absen_id')->on('absens')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tunjangan_karyawan', function (Blueprint $table) {
            $table->dropForeign(['absen_id']);
        });

        Schema::table('tunjangan_karyawan', function (Blueprint $table) {
            $table->string('absen_id', 10)->nullable()->change();
        });

        Schema::table('tunjangan_karyawan', function (Blueprint $table) {
            $table->foreign('absen_id')->references('absen_id')->on('absens')->onDelete('set null');
        });
    }
};

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
        Schema::table('tunjangan_karyawan', function (Blueprint $table) {
            // Tambah kolom lembur_id setelah absen_id
            $table->string('lembur_id')->nullable()->after('absen_id');

            // Tambah foreign key constraint
            $table->foreign('lembur_id')
                  ->references('lembur_id')
                  ->on('lemburs')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tunjangan_karyawan', function (Blueprint $table) {
            // Drop foreign key constraint dulu
            $table->dropForeign(['lembur_id']);

            // Baru drop column
            $table->dropColumn('lembur_id');
        });
    }
};

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
            // Tambah field coordinator_id untuk tracking siapa koordinator yang approve
            $table->string('coordinator_id')->nullable()->after('approved_by_user_id');

            // Foreign key ke table karyawans
            $table->foreign('coordinator_id')
                  ->references('karyawan_id')
                  ->on('karyawans')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lemburs', function (Blueprint $table) {
            $table->dropForeign(['coordinator_id']);
            $table->dropColumn('coordinator_id');
        });
    }
};

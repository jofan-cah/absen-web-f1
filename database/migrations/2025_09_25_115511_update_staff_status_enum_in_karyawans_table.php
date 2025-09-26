<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan data lama aman dulu → convert "wakil_koordinator" ke "training"
        DB::table('karyawans')
            ->where('staff_status', 'wakil_koordinator')
            ->update(['staff_status' => 'pkwtt']);

        Schema::table('karyawans', function (Blueprint $table) {
            $table->enum('staff_status', ['staff', 'koordinator', 'pkwtt'])
                  ->default('pkwtt')
                  ->change();
        });
    }

    public function down(): void
    {
        // Kalau rollback, balikin lagi "training" ke "wakil_koordinator"
        DB::table('karyawans')
            ->where('staff_status', 'training')
            ->update(['staff_status' => 'wakil_koordinator']);

        Schema::table('karyawans', function (Blueprint $table) {
            $table->enum('staff_status', ['staff', 'koordinator', 'wakil_koordinator'])
                  ->default('staff')
                  ->change();
        });
    }
};

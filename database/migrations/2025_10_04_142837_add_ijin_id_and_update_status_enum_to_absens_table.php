<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Tambah kolom ijin_id
        Schema::table('absens', function (Blueprint $table) {
            $table->string('ijin_id', 20)->nullable()->after('jadwal_id');
        });

        // Step 2: Update ENUM status - tambah nilai baru yang sesuai ijin_type code
        DB::statement("ALTER TABLE absens MODIFY COLUMN status ENUM(
            'scheduled',
            'present',
            'late',
            'early_checkout',
            'absent',
            'sick',
            'annual',
            'personal',
            'shift_swap',
            'compensation_leave'
        ) DEFAULT 'scheduled'");

        // Step 3: Tambah foreign key constraint
        Schema::table('absens', function (Blueprint $table) {
            $table->foreign('ijin_id')
                  ->references('ijin_id')
                  ->on('ijins')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Drop foreign key
        Schema::table('absens', function (Blueprint $table) {
            $table->dropForeign(['ijin_id']);
        });

        // Step 2: Kembalikan ENUM ke nilai lama
        DB::statement("ALTER TABLE absens MODIFY COLUMN status ENUM(
            'scheduled',
            'present',
            'late',
            'early_checkout',
            'absent',
            'sick_leave',
            'annual_leave',
            'permission'
        ) DEFAULT 'scheduled'");

        // Step 3: Drop kolom ijin_id
        Schema::table('absens', function (Blueprint $table) {
            $table->dropColumn('ijin_id');
        });
    }
};

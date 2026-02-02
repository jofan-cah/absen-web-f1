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
        Schema::table('karyawans', function (Blueprint $table) {
            // Flag apakah karyawan ini punya shift normal (jadwal otomatis Senin-Sabtu)
            $table->boolean('is_shift_normal')->default(false)->after('uang_kuota');

            // Default shift yang dipakai untuk generate jadwal otomatis
            $table->string('default_shift_id', 20)->nullable()->after('is_shift_normal');

            // Foreign key ke shifts table
            $table->foreign('default_shift_id')
                ->references('shift_id')
                ->on('shifts')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            $table->dropForeign(['default_shift_id']);
            $table->dropColumn(['is_shift_normal', 'default_shift_id']);
        });
    }
};

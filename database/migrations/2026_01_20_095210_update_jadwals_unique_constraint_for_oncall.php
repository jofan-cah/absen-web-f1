<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Update unique constraint untuk allow karyawan punya jadwal regular + oncall
     * di hari yang sama (beriringan)
     */
    public function up(): void
    {
        // Update jadwals table
        Schema::table('jadwals', function (Blueprint $table) {
            // Drop foreign key first (karena pakai column yang sama)
            $table->dropForeign(['karyawan_id']);

            // Drop old unique constraint
            $table->dropUnique(['karyawan_id', 'date']);
        });

        Schema::table('jadwals', function (Blueprint $table) {
            // Add new unique with type
            $table->unique(['karyawan_id', 'date', 'type'], 'jadwals_karyawan_date_type_unique');

            // Re-add foreign key
            $table->foreign('karyawan_id')->references('karyawan_id')->on('karyawans')->onDelete('cascade');
        });

        // Update absens table
        Schema::table('absens', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['karyawan_id']);

            // Drop old unique constraint
            $table->dropUnique(['karyawan_id', 'date']);
        });

        Schema::table('absens', function (Blueprint $table) {
            // Add new unique with type
            $table->unique(['karyawan_id', 'date', 'type'], 'absens_karyawan_date_type_unique');

            // Re-add foreign key
            $table->foreign('karyawan_id')->references('karyawan_id')->on('karyawans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert jadwals table
        Schema::table('jadwals', function (Blueprint $table) {
            $table->dropForeign(['karyawan_id']);
            $table->dropUnique('jadwals_karyawan_date_type_unique');
        });

        Schema::table('jadwals', function (Blueprint $table) {
            $table->unique(['karyawan_id', 'date']);
            $table->foreign('karyawan_id')->references('karyawan_id')->on('karyawans')->onDelete('cascade');
        });

        // Revert absens table
        Schema::table('absens', function (Blueprint $table) {
            $table->dropForeign(['karyawan_id']);
            $table->dropUnique('absens_karyawan_date_type_unique');
        });

        Schema::table('absens', function (Blueprint $table) {
            $table->unique(['karyawan_id', 'date']);
            $table->foreign('karyawan_id')->references('karyawan_id')->on('karyawans')->onDelete('cascade');
        });
    }
};

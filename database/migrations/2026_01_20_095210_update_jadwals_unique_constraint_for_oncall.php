<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Update jadwals table - drop old, add new unique with type
        DB::statement('ALTER TABLE `jadwals` DROP INDEX `jadwals_karyawan_id_date_unique`');
        DB::statement('ALTER TABLE `jadwals` ADD UNIQUE `jadwals_karyawan_date_type_unique` (`karyawan_id`, `date`, `type`)');

        // Update absens table - drop old, add new unique with type
        DB::statement('ALTER TABLE `absens` DROP INDEX `absens_karyawan_id_date_unique`');
        DB::statement('ALTER TABLE `absens` ADD UNIQUE `absens_karyawan_date_type_unique` (`karyawan_id`, `date`, `type`)');

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Revert jadwals table
        DB::statement('ALTER TABLE `jadwals` DROP INDEX `jadwals_karyawan_date_type_unique`');
        DB::statement('ALTER TABLE `jadwals` ADD UNIQUE `jadwals_karyawan_id_date_unique` (`karyawan_id`, `date`)');

        // Revert absens table
        DB::statement('ALTER TABLE `absens` DROP INDEX `absens_karyawan_date_type_unique`');
        DB::statement('ALTER TABLE `absens` ADD UNIQUE `absens_karyawan_id_date_unique` (`karyawan_id`, `date`)');

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};

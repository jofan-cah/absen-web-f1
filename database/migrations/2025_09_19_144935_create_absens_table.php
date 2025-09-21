<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absens', function (Blueprint $table) {
            $table->string('absen_id', 20)->primary();
            $table->string('karyawan_id', 20);
            $table->string('jadwal_id', 20);
            $table->date('date');

            // Clock In Data
            $table->time('clock_in')->nullable();
            $table->string('clock_in_photo')->nullable();
            $table->decimal('clock_in_latitude', 10, 8)->nullable();
            $table->decimal('clock_in_longitude', 11, 8)->nullable();
            $table->text('clock_in_address')->nullable();

            // Clock Out Data
            $table->time('clock_out')->nullable();
            $table->string('clock_out_photo')->nullable();
            $table->decimal('clock_out_latitude', 10, 8)->nullable();
            $table->decimal('clock_out_longitude', 11, 8)->nullable();
            $table->text('clock_out_address')->nullable();

            // Status & Calculation
            $table->enum('status', [
                'scheduled', 'present', 'late', 'early_checkout',
                'absent', 'sick_leave', 'annual_leave', 'permission'
            ])->default('scheduled');
            $table->integer('late_minutes')->default(0);
            $table->integer('early_checkout_minutes')->default(0);
            $table->decimal('work_hours', 4, 2)->default(0.00);

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('karyawan_id')->references('karyawan_id')->on('karyawans')->onDelete('cascade');
            $table->foreign('jadwal_id')->references('jadwal_id')->on('jadwals')->onDelete('cascade');

            $table->unique(['karyawan_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absens');
    }
};

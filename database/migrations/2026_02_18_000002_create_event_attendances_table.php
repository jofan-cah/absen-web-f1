<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_attendances', function (Blueprint $table) {
            $table->string('attendance_id', 20)->primary();
            $table->string('event_id', 20);
            $table->string('karyawan_id', 20);
            $table->dateTime('check_in_at');
            $table->enum('method', ['qr_scan', 'manual'])->default('qr_scan');
            $table->integer('jumlah_orang')->default(1);
            $table->text('keterangan')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('ticket_token', 64)->unique();
            $table->string('verified_by', 20)->nullable();
            $table->timestamps();

            $table->foreign('event_id')->references('event_id')->on('events')->onDelete('cascade');
            $table->foreign('karyawan_id')->references('karyawan_id')->on('karyawans')->onDelete('cascade');
            $table->foreign('verified_by')->references('user_id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_attendances');
    }
};

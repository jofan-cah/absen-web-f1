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
        Schema::create('notifications', function (Blueprint $table) {
            $table->string('notification_id', 30)->primary();
            $table->string('karyawan_id', 10);

            // Type notifikasi
            $table->enum('type', [
                'reminder_clock_in',      // Reminder absen masuk
                'reminder_clock_out',     // Reminder absen pulang
                'absent_alert',           // Alert belum absen sama sekali
                'late_warning',           // Warning terlambat
                'schedule_update',        // Update jadwal
                'tunjangan_approved',     // Tunjangan disetujui
                'tunjangan_rejected',     // Tunjangan ditolak
                'ijin_approved',          // Ijin disetujui
                'ijin_rejected',          // Ijin ditolak
                'general'                 // Notifikasi umum
            ])->default('general');

            $table->string('title');
            $table->text('message');

            // Data tambahan (JSON)
            // Contoh: {"jadwal_id": "JDW001", "absen_id": "ABS001"}
            $table->json('data')->nullable();

            // Read status
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();

            // FCM tracking
            $table->boolean('fcm_sent')->default(false);
            $table->timestamp('fcm_sent_at')->nullable();

            $table->timestamps();

            // Foreign key
            $table->foreign('karyawan_id')
                  ->references('karyawan_id')
                  ->on('karyawans')
                  ->onDelete('cascade');

            // Indexes untuk performa query
            $table->index(['karyawan_id', 'is_read']);
            $table->index(['karyawan_id', 'created_at']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

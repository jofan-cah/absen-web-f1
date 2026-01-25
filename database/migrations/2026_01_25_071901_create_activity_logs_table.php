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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // User yang melakukan aksi
            $table->string('user_id', 10)->nullable();
            $table->string('karyawan_id', 10)->nullable();
            $table->string('user_name')->nullable(); // Cache nama user

            // Jenis aktivitas
            $table->enum('action', [
                'login',
                'logout',
                'login_failed',
                'create',
                'update',
                'delete',
                'view',
                'export',
                'import',
                'approve',
                'reject',
                'submit',
                'error',
                'other'
            ]);

            // Module/Model yang terkait
            $table->string('module')->nullable(); // Contoh: User, Karyawan, Absen, Lembur, Ijin
            $table->string('module_id')->nullable(); // ID dari record yang diubah

            // Deskripsi aktivitas
            $table->string('description');

            // Data perubahan (JSON)
            $table->json('old_data')->nullable(); // Data sebelum diubah
            $table->json('new_data')->nullable(); // Data setelah diubah
            $table->json('changed_fields')->nullable(); // Field yang berubah saja

            // Error info (jika action = error)
            $table->text('error_message')->nullable();
            $table->text('error_trace')->nullable();

            // Request info
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('request_url')->nullable();
            $table->string('request_method', 10)->nullable();

            // Device info (untuk mobile)
            $table->enum('platform', ['web', 'mobile', 'api', 'system'])->default('web');
            $table->string('device_type')->nullable(); // android, ios, desktop

            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('karyawan_id');
            $table->index('action');
            $table->index('module');
            $table->index('created_at');
            $table->index(['module', 'module_id']);

            // Foreign keys
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('set null');
            $table->foreign('karyawan_id')->references('karyawan_id')->on('karyawans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};

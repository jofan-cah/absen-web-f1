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
        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 10)->nullable();
            $table->string('karyawan_id', 10)->nullable();
            $table->text('device_token'); // FCM token dari Flutter
            $table->enum('device_type', ['android', 'ios'])->default('android');
            $table->string('device_name')->nullable(); // Misal: "Samsung Galaxy S21"
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('karyawan_id')->references('karyawan_id')->on('karyawans')->onDelete('cascade');

            // Indexes untuk performa
            $table->index(['karyawan_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_tokens');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->string('event_id', 20)->primary();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['internal', 'partnership'])->default('internal');
            $table->string('location')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            // QR Security
            $table->string('qr_token', 64);
            $table->string('qr_secret', 64);
            $table->integer('qr_refresh_seconds')->default(30);

            // Constraints
            $table->integer('max_participants')->nullable();
            $table->boolean('allow_multi_scan')->default(false);

            // GPS Validation (opsional)
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('radius')->default(100);

            // Filter
            $table->string('department_id', 20)->nullable();

            $table->string('created_by', 20);
            $table->enum('status', ['draft', 'active', 'ongoing', 'completed', 'cancelled'])->default('draft');
            $table->timestamps();

            $table->foreign('department_id')->references('department_id')->on('departments')->onDelete('set null');
            $table->foreign('created_by')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};

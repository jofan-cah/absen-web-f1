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
        Schema::create('tunjangan_karyawan', function (Blueprint $table) {
            $table->string('tunjangan_karyawan_id', 10)->primary();
            $table->string('karyawan_id', 10);
            $table->string('tunjangan_type_id', 10);
            $table->string('absen_id', 10)->nullable(); // untuk tunjangan lembur
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('amount', 12, 2); // nominal per unit
            $table->integer('quantity')->default(1); // untuk backward compatibility
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->enum('status', ['pending', 'requested', 'approved', 'received'])->default('pending');
            $table->text('notes')->nullable();

            // Workflow tracking
            $table->datetime('requested_at')->nullable();
            $table->enum('requested_via', ['mobile', 'web'])->nullable();
            $table->string('approved_by_user_id', 10)->nullable();
            $table->datetime('approved_at')->nullable();
            $table->datetime('received_at')->nullable();
            $table->string('received_confirmation_photo', 255)->nullable();

            // Penalti integration
            $table->string('penalti_id', 10)->nullable();
            $table->integer('hari_kerja_asli')->nullable(); // jumlah hari kerja sebenarnya
            $table->integer('hari_potong_penalti')->nullable()->default(0); // hari yang dipotong karena penalti
            $table->integer('hari_kerja_final')->nullable(); // hari kerja - hari potong penalti

            // History tracking
            $table->json('history')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('karyawan_id')->references('karyawan_id')->on('karyawans')->onDelete('cascade');
            $table->foreign('tunjangan_type_id')->references('tunjangan_type_id')->on('tunjangan_types')->onDelete('cascade');
            $table->foreign('absen_id')->references('absen_id')->on('absens')->onDelete('set null');
            $table->foreign('approved_by_user_id')->references('user_id')->on('users')->onDelete('set null');
            $table->foreign('penalti_id')->references('penalti_id')->on('penaltis')->onDelete('set null');

            // Indexes for better performance
            $table->index(['karyawan_id', 'status']);
            $table->index(['tunjangan_type_id', 'status']);
            $table->index(['period_start', 'period_end']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tunjangan_karyawan');
    }
};

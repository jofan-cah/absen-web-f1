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
        Schema::create('lemburs', function (Blueprint $table) {
            $table->string('lembur_id')->primary();
            $table->string('karyawan_id');
            $table->string('absen_id')->nullable();
            $table->date('tanggal_lembur');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->decimal('total_jam', 5, 2)->default(0);
            $table->enum('kategori_lembur', ['reguler', 'hari_libur', 'hari_besar'])->default('reguler');
            $table->decimal('multiplier', 3, 2)->default(1.5);
            $table->text('deskripsi_pekerjaan')->nullable();
            $table->string('bukti_foto')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'processed'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->enum('submitted_via', ['mobile', 'web'])->nullable();
            $table->string('approved_by_user_id')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->string('rejected_by_user_id')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->string('tunjangan_karyawan_id')->nullable();
            $table->string('created_by_user_id')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('karyawan_id')->references('karyawan_id')->on('karyawans')->onDelete('cascade');
            $table->foreign('absen_id')->references('absen_id')->on('absens')->onDelete('set null');
            $table->foreign('approved_by_user_id')->references('user_id')->on('users')->onDelete('set null');
            $table->foreign('rejected_by_user_id')->references('user_id')->on('users')->onDelete('set null');
            $table->foreign('created_by_user_id')->references('user_id')->on('users')->onDelete('set null');
            $table->foreign('tunjangan_karyawan_id')->references('tunjangan_karyawan_id')->on('tunjangan_karyawan')->onDelete('set null');

            // Indexes untuk performance
            $table->index('karyawan_id');
            $table->index('tanggal_lembur');
            $table->index('status');
            $table->index(['karyawan_id', 'tanggal_lembur']);
            $table->index(['karyawan_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lemburs');
    }
};

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
        Schema::create('penaltis', function (Blueprint $table) {
            $table->string('penalti_id', 10)->primary();
            $table->string('karyawan_id', 10);
            $table->string('absen_id', 10)->nullable();
            $table->enum('jenis_penalti', ['telat', 'tidak_masuk', 'pelanggaran', 'custom']);
            $table->text('deskripsi');
            $table->integer('hari_potong_uang_makan')->default(0);
            $table->date('tanggal_penalti');
            $table->date('periode_berlaku_mulai');
            $table->date('periode_berlaku_akhir');
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->string('created_by_user_id', 10)->nullable();
            $table->string('approved_by_user_id', 10)->nullable();
            $table->datetime('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('karyawan_id')->references('karyawan_id')->on('karyawans')->onDelete('cascade');
            $table->foreign('absen_id')->references('absen_id')->on('absens')->onDelete('set null');
            $table->foreign('created_by_user_id')->references('user_id')->on('users')->onDelete('set null');
            $table->foreign('approved_by_user_id')->references('user_id')->on('users')->onDelete('set null');

            $table->index(['karyawan_id', 'status']);
            $table->index(['periode_berlaku_mulai', 'periode_berlaku_akhir']);
            $table->index('tanggal_penalti');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penaltis');
    }
};

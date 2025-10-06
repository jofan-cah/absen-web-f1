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
        Schema::create('ijins', function (Blueprint $table) {
            $table->string('ijin_id', 20)->primary();
            $table->string('karyawan_id', 20);
            $table->string('ijin_type_id', 20);

            // Data ijin
            $table->date('date_from');
            $table->date('date_to');
            $table->text('reason')->nullable();

            // Khusus untuk tukar shift & cuti pengganti
            $table->date('original_shift_date')->nullable();
            $table->date('replacement_shift_date')->nullable();

            // Approval flow - Coordinator
            $table->string('coordinator_id', 20)->nullable();
            $table->enum('coordinator_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('coordinator_note')->nullable();
            $table->datetime('coordinator_reviewed_at')->nullable();

            // Approval flow - Admin
            $table->string('admin_id', 20)->nullable();
            $table->enum('admin_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_note')->nullable();
            $table->datetime('admin_reviewed_at')->nullable();

            // Status final
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->timestamps();

            // Foreign keys
            $table->foreign('karyawan_id')->references('karyawan_id')->on('karyawans')->onDelete('cascade');
            $table->foreign('ijin_type_id')->references('ijin_type_id')->on('ijin_types')->onDelete('restrict');
            $table->foreign('coordinator_id')->references('user_id')->on('users')->onDelete('set null');
            $table->foreign('admin_id')->references('user_id')->on('users')->onDelete('set null');

            // Indexes
            $table->index(['karyawan_id', 'status']);
            $table->index(['date_from', 'date_to']);
            $table->index(['coordinator_status', 'admin_status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ijins');
    }
};

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
        // =====================================================
        // CREATE TABLE shift_swap_requests
        // =====================================================
        Schema::create('shift_swap_requests', function (Blueprint $table) {
            $table->string('swap_id', 50)->primary();

            // Requester (Karyawan A yang mengajukan)
            $table->string('requester_karyawan_id', 50);
            $table->string('requester_jadwal_id', 50);

            // Partner (Karyawan B yang diminta tukar)
            $table->string('partner_karyawan_id', 50);
            $table->string('partner_jadwal_id', 50);

            // Request details
            $table->text('reason')->nullable();

            // Status flow dengan admin approval
            $table->enum('status', [
                'pending_partner',           // Menunggu respon partner
                'approved_by_partner',       // Partner setuju (optional step)
                'pending_admin_approval',    // Menunggu approval admin
                'rejected_by_partner',       // Partner tolak
                'rejected_by_admin',         // Admin tolak
                'cancelled',                 // Requester cancel
                'completed'                  // Swap selesai
            ])->default('pending_partner');

            // Partner response
            $table->timestamp('partner_response_at')->nullable();
            $table->text('partner_notes')->nullable();

            // Admin approval
            $table->string('approved_by_admin_id', 50)->nullable();
            $table->timestamp('admin_approved_at')->nullable();
            $table->text('admin_notes')->nullable();

            // Completion
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('requester_karyawan_id')
                  ->references('karyawan_id')
                  ->on('karyawans')
                  ->onDelete('cascade');

            $table->foreign('requester_jadwal_id')
                  ->references('jadwal_id')
                  ->on('jadwals')
                  ->onDelete('cascade');

            $table->foreign('partner_karyawan_id')
                  ->references('karyawan_id')
                  ->on('karyawans')
                  ->onDelete('cascade');

            $table->foreign('partner_jadwal_id')
                  ->references('jadwal_id')
                  ->on('jadwals')
                  ->onDelete('cascade');

            $table->foreign('approved_by_admin_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('set null');

            // Indexes
            $table->index('requester_karyawan_id');
            $table->index('partner_karyawan_id');
            $table->index('approved_by_admin_id');
            $table->index('status');
            $table->index('created_at');
        });

        // =====================================================
        // ALTER TABLE jadwals - tambah field swap_id
        // =====================================================
        Schema::table('jadwals', function (Blueprint $table) {
            $table->string('swap_id', 50)->nullable()->after('ijin_id');

            $table->foreign('swap_id')
                  ->references('swap_id')
                  ->on('shift_swap_requests')
                  ->onDelete('set null');

            $table->index('swap_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key & column dari jadwals
        Schema::table('jadwals', function (Blueprint $table) {
            $table->dropForeign(['swap_id']);
            $table->dropIndex(['swap_id']);
            $table->dropColumn('swap_id');
        });

        // Drop table shift_swap_requests
        Schema::dropIfExists('shift_swap_requests');
    }
};

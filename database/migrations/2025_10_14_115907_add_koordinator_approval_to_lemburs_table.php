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
        Schema::table('lemburs', function (Blueprint $table) {
            // Status approval koordinator
            $table->enum('koordinator_status', ['pending', 'approved', 'rejected'])
                ->default('pending')
                ->after('status')
                ->comment('Status approval dari koordinator');

            // Waktu koordinator approve
            $table->timestamp('koordinator_approved_at')
                ->nullable()
                ->after('koordinator_status')
                ->comment('Waktu koordinator menyetujui');

            // Catatan dari koordinator
            $table->text('koordinator_notes')
                ->nullable()
                ->after('koordinator_approved_at')
                ->comment('Catatan dari koordinator saat approve/reject');

            // Waktu koordinator reject (opsional, untuk tracking)
            $table->timestamp('koordinator_rejected_at')
                ->nullable()
                ->after('koordinator_notes')
                ->comment('Waktu koordinator menolak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lemburs', function (Blueprint $table) {
            $table->dropColumn([
                'koordinator_status',
                'koordinator_approved_at',
                'koordinator_notes',
                'koordinator_rejected_at'
            ]);
        });
    }
};

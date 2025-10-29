<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update enum status lembur (tambah 'waiting_checkin' dan 'in_progress')
        DB::statement("
            ALTER TABLE lemburs
            MODIFY COLUMN status ENUM(
                'draft',
                'waiting_checkin',
                'in_progress',
                'submitted',
                'approved',
                'rejected',
                'processed'
            ) DEFAULT 'draft'
            COMMENT 'Status lembur: draft, waiting_checkin (oncall belum absen), in_progress (oncall sedang berjalan), submitted, approved, rejected, processed'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback ke enum status lama
        DB::statement("
            ALTER TABLE lemburs
            MODIFY COLUMN status ENUM(
                'draft',
                'submitted',
                'approved',
                'rejected',
                'processed'
            ) DEFAULT 'draft'
        ");
    }
};

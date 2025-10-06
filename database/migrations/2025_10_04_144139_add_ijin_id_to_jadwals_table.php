<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            $table->string('ijin_id', 20)->nullable()->after('shift_id');

            $table->foreign('ijin_id')
                  ->references('ijin_id')
                  ->on('ijins')
                  ->onDelete('set null');

            // Optional: tambah status untuk memudahkan query
            $table->enum('status', [
                'normal',           // Jadwal biasa
                'has_ijin',         // Ada ijin yang approved
                'cancelled'         // Dibatalkan
            ])->default('normal')->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            $table->dropForeign(['ijin_id']);
            $table->dropColumn(['ijin_id', 'status']);
        });
    }
};

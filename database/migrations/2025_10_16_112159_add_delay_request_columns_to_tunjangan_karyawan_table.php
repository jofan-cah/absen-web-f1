<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tambah kolom untuk sistem penundaan request Uang Makan
     */
    public function up(): void
    {
        Schema::table('tunjangan_karyawan', function (Blueprint $table) {
            // Jumlah hari delay request
            $table->integer('delay_days')
                ->default(0)
                ->after('history')
                ->comment('Jumlah hari penundaan request (berdasarkan tidak clock out)');

            // Tanggal bisa mulai request
            $table->date('available_request_date')
                ->nullable()
                ->after('delay_days')
                ->comment('Tanggal mulai bisa request (period_end + delay_days)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tunjangan_karyawan', function (Blueprint $table) {
            $table->dropColumn([
                'delay_days',
                'available_request_date',
            ]);
        });
    }
};

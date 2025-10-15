<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Ubah 3 kolom jadi NULLABLE karena flow baru:
     * START → jam_selesai, deskripsi, bukti_foto masih kosong
     * FINISH → baru diisi
     */
    public function up(): void
    {
        Schema::table('lemburs', function (Blueprint $table) {
            // Ubah jam_selesai jadi nullable
            $table->time('jam_selesai')
                ->nullable()
                ->change()
                ->comment('Jam selesai lembur - diisi saat finish');

            // Ubah deskripsi_pekerjaan jadi nullable
            $table->text('deskripsi_pekerjaan')
                ->nullable()
                ->change()
                ->comment('Deskripsi pekerjaan - diisi saat finish');

            // Ubah bukti_foto jadi nullable
            $table->string('bukti_foto', 500)
                ->nullable()
                ->change()
                ->comment('Path foto bukti lembur - diisi saat finish');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lemburs', function (Blueprint $table) {
            // Kembalikan ke NOT NULL (tapi ini risky kalau ada data NULL)
            $table->time('jam_selesai')
                ->nullable(false)
                ->change();

            $table->text('deskripsi_pekerjaan')
                ->nullable(false)
                ->change();

            $table->string('bukti_foto', 500)
                ->nullable(false)
                ->change();
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwals', function (Blueprint $table) {
            $table->string('jadwal_id', 20)->primary();
            $table->string('karyawan_id', 20);
            $table->string('shift_id', 20);
            $table->date('date');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->string('created_by_user_id', 20);
            $table->timestamps();

            $table->foreign('karyawan_id')->references('karyawan_id')->on('karyawans')->onDelete('cascade');
            $table->foreign('shift_id')->references('shift_id')->on('shifts')->onDelete('restrict');
            $table->foreign('created_by_user_id')->references('user_id')->on('users')->onDelete('restrict');

            $table->unique(['karyawan_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};

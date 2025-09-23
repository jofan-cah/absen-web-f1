<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus ENUM lama lalu buat ulang dengan tambahan role baru
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'karyawan', 'koordinator') DEFAULT 'karyawan'");
    }

    public function down(): void
    {
        // Balik lagi ke role lama (tanpa koordinator)
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'karyawan') DEFAULT 'karyawan'");
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absens', function (Blueprint $table) {
            $table->string('jadwal_id', 20)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('absens', function (Blueprint $table) {
            $table->string('jadwal_id', 20)->nullable(false)->change();
        });
    }
};

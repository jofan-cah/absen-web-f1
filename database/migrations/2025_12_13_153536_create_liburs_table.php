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
        Schema::create('liburs', function (Blueprint $table) {
            $table->string('libur_id', 20)->primary();
            $table->string('name', 255);
            $table->date('date')->unique();
            $table->string('type', 50)->nullable();
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#FFD700');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('liburs');
    }
};

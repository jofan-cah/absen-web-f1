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
        Schema::create('tunjangan_types', function (Blueprint $table) {
            $table->string('tunjangan_type_id', 10)->primary();
            $table->string('name', 100);
            $table->string('code', 50)->unique();
            $table->enum('category', ['harian', 'mingguan', 'bulanan']);
            $table->decimal('base_amount', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['code', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tunjangan_types');
    }
};

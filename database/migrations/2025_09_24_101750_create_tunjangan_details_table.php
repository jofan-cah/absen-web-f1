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
        Schema::create('tunjangan_details', function (Blueprint $table) {
            $table->string('tunjangan_detail_id', 10)->primary();
            $table->string('tunjangan_type_id', 10);
            $table->string('staff_status');
            $table->decimal('amount', 12, 2);
            $table->date('effective_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('tunjangan_type_id')->references('tunjangan_type_id')->on('tunjangan_types')->onDelete('cascade');
            $table->index(['tunjangan_type_id', 'staff_status', 'is_active']);
            $table->index('effective_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tunjangan_details');
    }
};

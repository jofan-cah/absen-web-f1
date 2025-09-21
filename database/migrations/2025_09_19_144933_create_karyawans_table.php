<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karyawans', function (Blueprint $table) {
            $table->string('karyawan_id', 20)->primary();
            $table->string('user_id', 20);
            $table->string('department_id', 20);
            $table->string('nip', 50)->unique();
            $table->string('full_name');
            $table->string('position');
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->date('hire_date');
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['L', 'P']);
            $table->string('photo')->nullable();
            $table->enum('employment_status', ['active', 'inactive', 'terminated'])->default('active');
            $table->enum('staff_status', ['staff', 'koordinator', 'wakil_koordinator'])->default('staff');
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('department_id')->references('department_id')->on('departments')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karyawans');
    }
};

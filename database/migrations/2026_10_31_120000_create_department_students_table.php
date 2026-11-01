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
        Schema::create('department_students', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('matric_no', 50)->unique();
            $table->string('department', 100)->default('Computer Engineering');
            $table->unsignedSmallInteger('level')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department_students');
    }
};

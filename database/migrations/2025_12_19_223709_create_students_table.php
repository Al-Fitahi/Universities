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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('public_id')->unique();
            $table->string('F_name');
            $table->string('S_name');
            $table->string('Th_name');
            $table->string('Su_name');
            $table->string('phone_number', 20);
            $table->date('graduation_date');
            $table->float('graduation_grade',2, 2);
            $table->string('certificate_image');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};

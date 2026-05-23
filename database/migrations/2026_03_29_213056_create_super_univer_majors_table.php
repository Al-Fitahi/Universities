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
        Schema::create('super_univer_majors', function (Blueprint $table) {
            $table->id();
            $table->string('public_id')->unique();
            $table->foreignId('university_major_id')->constrained('university_majors')->onDelete('cascade');
            $table->foreignId('supervisor_id')->constrained('supervisors')->onDelete('cascade');
            $table->unique(['university_major_id', 'supervisor_id'], 'super_univer_major_unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('super_univer_majors');
    }
};

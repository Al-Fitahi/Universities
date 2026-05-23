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
        Schema::create('university_majors', function (Blueprint $table) {
            $table->id();
            $table->string('public_id')->unique();
            $table->unsignedInteger('number_of_seats')->nullable();
            $table->float('admission_rate', 5, 2)->nullable();
            $table->unsignedInteger('study_years')->nullable();
            $table->float('tuition_fee', 10, 2)->nullable();
            $table->boolean('published')->default(false);
            $table->foreignId('major_id')->constrained('majors')->onDelete('cascade');
            $table->foreignId('university_id')->constrained('universities')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('university_majors');
    }
};

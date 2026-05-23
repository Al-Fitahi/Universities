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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('public_id')->unique();
            $table->foreignId('super_univer_major_id')->constrained('super_univer_majors')->onDelete('cascade');
            $table->string('name')->unique();
            $table->foreignId('project_type_id')->constrained('project_types')->onDelete('cascade');
            $table->text('project_overview');
            $table->string('pdf_path');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};

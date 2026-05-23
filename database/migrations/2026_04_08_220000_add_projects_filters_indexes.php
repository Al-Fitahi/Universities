<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->index(['is_active', 'project_type_id', 'created_at'], 'projects_active_type_created_idx');
            $table->index('name', 'projects_name_idx');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex('projects_active_type_created_idx');
            $table->dropIndex('projects_name_idx');
        });
    }
};

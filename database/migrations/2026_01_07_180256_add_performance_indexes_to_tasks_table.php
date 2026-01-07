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
        Schema::table('tasks', function (Blueprint $table) {
            // Index for user's tasks queries
            $table->index(['created_by', 'status'], 'idx_tasks_created_by_status');
            
            // Index for overdue tasks queries
            $table->index(['due_date', 'status'], 'idx_tasks_due_date_status');
            
            // Index for project tasks queries
            $table->index(['project_id', 'status'], 'idx_tasks_project_status');
            
            // Index for priority filtering
            $table->index('priority', 'idx_tasks_priority');
            
            // Index for status filtering
            $table->index('status', 'idx_tasks_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('idx_tasks_created_by_status');
            $table->dropIndex('idx_tasks_due_date_status');
            $table->dropIndex('idx_tasks_project_status');
            $table->dropIndex('idx_tasks_priority');
            $table->dropIndex('idx_tasks_status');
        });
    }
};

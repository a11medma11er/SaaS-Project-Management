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
        Schema::table('activity_log', function (Blueprint $table) {
            // Index for date range queries
            $table->index('created_at', 'idx_activity_log_created_at');
            
            // Index for causer queries (who did it)
            $table->index(['causer_id', 'causer_type'], 'idx_activity_log_causer');
            
            // Index for subject queries (what was affected)
            $table->index(['subject_id', 'subject_type'], 'idx_activity_log_subject');
            
            // Index for log name filtering
            $table->index('log_name', 'idx_activity_log_name');
            
            // Index for event description
            $table->index('description', 'idx_activity_log_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropIndex('idx_activity_log_created_at');
            $table->dropIndex('idx_activity_log_causer');
            $table->dropIndex('idx_activity_log_subject');
            $table->dropIndex('idx_activity_log_name');
            $table->dropIndex('idx_activity_log_description');
        });
    }
};

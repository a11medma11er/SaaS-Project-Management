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
        Schema::create('ai_audit_logs', function (Blueprint $table) {
            $table->id();
            
            // Related entities
            $table->foreignId('decision_id')->constrained('ai_decisions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Action details
            $table->string('action', 50); // 'created', 'accepted', 'rejected', 'modified', 'viewed'
            $table->json('context'); // IP, user agent, changes made, etc.
            $table->text('reason')->nullable(); // User's reason for action
            
            // Timestamps (no updated_at needed - audit logs are immutable)
            $table->timestamp('created_at')->useCurrent();
            
            // Performance indexes
            $table->index(['decision_id', 'action'], 'idx_decision_action');
            $table->index('user_id', 'idx_user_id');
            $table->index('created_at', 'idx_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_audit_logs');
    }
};

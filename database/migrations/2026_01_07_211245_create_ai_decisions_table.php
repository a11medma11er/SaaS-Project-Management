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
        Schema::create('ai_decisions', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys to related entities
            $table->foreignId('task_id')->nullable()->constrained('tasks')->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('cascade');
            
            // Decision metadata
            $table->string('decision_type', 50); // 'task_analysis', 'project_breakdown', 'priority_suggestion', etc.
            $table->json('ai_response'); // Full AI response with all data
            $table->json('suggested_actions'); // Specific actionable items
            $table->decimal('confidence_score', 3, 2); // 0.00 to 1.00
            $table->text('reasoning'); // AI explanation/reasoning
            
            // User interaction
            $table->enum('user_action', ['pending', 'accepted', 'rejected', 'modified'])->default('pending');
            $table->text('user_feedback')->nullable(); // User's reason for accept/reject
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            
            // Timestamps and soft deletes
            $table->timestamps();
            $table->softDeletes();
            
            // Performance indexes
            $table->index(['task_id', 'decision_type'], 'idx_task_decision_type');
            $table->index(['project_id', 'decision_type'], 'idx_project_decision_type');
            $table->index('user_action', 'idx_user_action');
            $table->index('created_at', 'idx_created_at');
            $table->index('confidence_score', 'idx_confidence');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_decisions');
    }
};

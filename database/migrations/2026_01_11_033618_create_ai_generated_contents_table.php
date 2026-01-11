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
        Schema::create('ai_generated_contents', function (Blueprint $table) {
            $table->id();
            
            // Relationships
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('task_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            // Content Details
            $table->string('feature_type')->index(); // e.g., 'development_plan', 'feasibility_study'
            $table->string('prompt_name')->nullable(); // System prompt used
            $table->json('content'); // The actual AI result
            
            // Metrics & Metadata
            $table->json('metrics')->nullable(); // Token usage, generation time, etc.
            $table->string('provider')->nullable(); // AI provider name
            $table->string('model')->nullable(); // Model version
            
            $table->timestamps();
            
            // Indexes for faster retrieval
            $table->index(['project_id', 'feature_type']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_generated_contents');
    }
};

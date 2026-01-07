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
        Schema::create('ai_prompts', function (Blueprint $table) {
            $table->id();
            
            // Prompt identification
            $table->string('name', 100)->unique(); // 'system_prompt', 'task_analysis_v1', etc.
            $table->enum('type', ['system', 'user', 'assistant']); // Prompt role
            
            // Prompt content
            $table->text('template'); // Actual prompt template with variables
            $table->string('version', 20)->default('1.0.0'); // Semantic versioning
            $table->json('variables')->nullable(); // Available variables: {{task_title}}, {{project_name}}, etc.
            $table->text('description')->nullable(); // What this prompt does
            
            // Status and usage
            $table->boolean('is_active')->default(true);
            $table->integer('usage_count')->default(0); // How many times used
            
            // Metadata
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // Performance indexes
            $table->index(['name', 'version'], 'idx_name_version');
            $table->index('is_active', 'idx_is_active');
            $table->index('type', 'idx_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_prompts');
    }
};

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
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('thumbnail')->nullable();
            $table->text('description');
            $table->enum('priority', ['High', 'Medium', 'Low'])->default('Medium');
            $table->enum('status', ['Inprogress', 'Completed', 'On Hold'])->default('Inprogress');
            $table->enum('privacy', ['Private', 'Team', 'Public'])->default('Team');
            $table->string('category')->nullable();
            $table->json('skills')->nullable(); // Array of skills
            $table->date('deadline');
            $table->date('start_date')->nullable();
            $table->unsignedTinyInteger('progress')->default(0); // 0-100
            $table->boolean('is_favorite')->default(false);
            
            // Foreign Keys
            $table->foreignId('team_lead_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index('status');
            $table->index('priority');
            $table->index('deadline');
            $table->index('created_by');
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

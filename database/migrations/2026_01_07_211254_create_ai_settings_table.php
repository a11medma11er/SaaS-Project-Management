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
        Schema::create('ai_settings', function (Blueprint $table) {
            $table->id();
            
            // Setting key-value
            $table->string('key', 100)->unique(); // 'ai_enabled', 'max_confidence_threshold', etc.
            $table->text('value'); // Setting value (will be cast based on type)
            $table->string('type', 20); // 'boolean', 'integer', 'string', 'json', 'float'
            
            // Metadata
            $table->text('description')->nullable(); // What this setting controls
            $table->string('group', 50)->default('general'); // 'safety', 'performance', 'general'
            
            // Timestamps
            $table->timestamps();
            
            // Performance indexes
            $table->index('group', 'idx_group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_settings');
    }
};

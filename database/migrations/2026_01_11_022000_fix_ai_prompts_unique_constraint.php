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
        Schema::table('ai_prompts', function (Blueprint $table) {
            // Drop the old unique constraint on name
            $table->dropUnique(['name']);
            
            // Add composite unique constraint on name + version
            $table->unique(['name', 'version'], 'ai_prompts_name_version_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_prompts', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('ai_prompts_name_version_unique');
            
            // Re-add the old unique constraint on name
            $table->unique('name');
        });
    }
};

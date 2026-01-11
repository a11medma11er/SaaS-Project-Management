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
        Schema::create('prompt_tag_pivot', function (Blueprint $table) {
            $table->foreignId('ai_prompt_id')->constrained('ai_prompts')->onDelete('cascade');
            $table->foreignId('prompt_tag_id')->constrained('prompt_tags')->onDelete('cascade');
            $table->timestamps();
            
            $table->primary(['ai_prompt_id', 'prompt_tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prompt_tag_pivot');
    }
};

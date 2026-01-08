<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_decisions', function (Blueprint $table) {
            $table->id();
            $table->string('decision_type');
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id');
            $table->decimal('confidence_score', 3, 2);
            $table->text('reasoning')->nullable();
            $table->json('recommended_action')->nullable();
            $table->json('context_data')->nullable();
            $table->string('user_action')->nullable();
            $table->text('user_feedback')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
            $table->index('decision_type');
            $table->index('user_action');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_decisions');
    }
};

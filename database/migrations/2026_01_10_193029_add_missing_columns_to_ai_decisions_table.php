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
        Schema::table('ai_decisions', function (Blueprint $table) {
            // Add missing columns
            $table->text('recommendation')->nullable()->after('reasoning');
            $table->json('alternatives')->nullable()->after('recommendation');
            $table->timestamp('executed_at')->nullable()->after('reviewed_at');
            $table->json('execution_result')->nullable()->after('executed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_decisions', function (Blueprint $table) {
            $table->dropColumn([
                'recommendation',
                'alternatives',
                'executed_at',
                'execution_result'
            ]);
        });
    }
};

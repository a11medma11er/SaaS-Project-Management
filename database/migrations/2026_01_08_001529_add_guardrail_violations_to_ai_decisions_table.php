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
            $table->integer('guardrail_violations')->default(0)->after('confidence_score');
            $table->json('guardrail_check')->nullable()->after('guardrail_violations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_decisions', function (Blueprint $table) {
            $table->dropColumn(['guardrail_violations', 'guardrail_check']);
        });
    }
};

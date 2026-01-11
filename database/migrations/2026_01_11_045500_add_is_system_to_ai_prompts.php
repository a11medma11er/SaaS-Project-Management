<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add is_system flag to protect core prompts
     */
    public function up(): void
    {
        Schema::table('ai_prompts', function (Blueprint $table) {
            $table->boolean('is_system')->default(false)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_prompts', function (Blueprint $table) {
            $table->dropColumn('is_system');
        });
    }
};

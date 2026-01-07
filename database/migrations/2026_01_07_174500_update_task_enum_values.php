<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, update the column to support longer enum values
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('status', 20)->change();
            $table->string('priority', 10)->change();
        });
        
        // Then update status values to match enum
        DB::table('tasks')->where('status', 'New')->update(['status' => 'new']);
        DB::table('tasks')->where('status', 'Pending')->update(['status' => 'pending']);
        DB::table('tasks')->where('status', 'Inprogress')->update(['status' => 'in_progress']);
        DB::table('tasks')->where('status', 'Completed')->update(['status' => 'completed']);
        
        // Update priority values to match enum
        DB::table('tasks')->where('priority', 'Low')->update(['priority' => 'low']);
        DB::table('tasks')->where('priority', 'Medium')->update(['priority' => 'medium']);
        DB::table('tasks')->where('priority', 'High')->update(['priority' => 'high']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert values first
        DB::table('tasks')->where('status', 'new')->update(['status' => 'New']);
        DB::table('tasks')->where('status', 'pending')->update(['status' => 'Pending']);
        DB::table('tasks')->where('status', 'in_progress')->update(['status' => 'Inprogress']);
        DB::table('tasks')->where('status', 'completed')->update(['status' => 'Completed']);
        
        DB::table('tasks')->where('priority', 'low')->update(['priority' => 'Low']);
        DB::table('tasks')->where('priority', 'medium')->update(['priority' => 'Medium']);
        DB::table('tasks')->where('priority', 'high')->update(['priority' => 'High']);
        
        // Then change column back
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('status', 10)->change();
            $table->string('priority', 6)->change();
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * إضافة عمود kanban_status منفصل عن status
     * - يكون فارغ (null) عند إنشاء المهمة = المهمة ليست في الكانبان
     * - القيم الممكنة: todo, inprogress, review, completed
     * - عند إضافة مهمة للكانبان يتم تعيين قيمة
     * - عند حذف مهمة من الكانبان يتم تعيين null
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // إضافة عمود kanban_status - nullable يعني المهمة ليست في الكانبان
            $table->string('kanban_status', 20)->nullable()->after('status');
            
            // Index لتحسين الأداء
            $table->index('kanban_status', 'idx_tasks_kanban_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('idx_tasks_kanban_status');
            $table->dropColumn('kanban_status');
        });
    }
};

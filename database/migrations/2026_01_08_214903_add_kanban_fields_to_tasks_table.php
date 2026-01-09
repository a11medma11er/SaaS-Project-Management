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
        Schema::table('tasks', function (Blueprint $table) {
            // إضافة حقل progress (0-100)
            $table->unsignedTinyInteger('progress')->default(0)->after('priority');
            
            // إضافة حقل position لترتيب المهام داخل كل عمود
            $table->unsignedInteger('position')->default(0)->after('progress');
            
            // إضافة composite index لتحسين الأداء عند الاستعلام حسب status & position
            $table->index(['status', 'position'], 'idx_tasks_status_position');
        });
        
        // تعيين قيم position افتراضية للمهام الموجودة
        // كل مهمة ستأخذ position بناءً على ترتيب created_at
        DB::statement("
            UPDATE tasks t
            JOIN (
                SELECT id, 
                       ROW_NUMBER() OVER (PARTITION BY status ORDER BY created_at ASC) - 1 as new_position
                FROM tasks
            ) ranked ON t.id = ranked.id
            SET t.position = ranked.new_position
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // حذف الـ index أولاً
            $table->dropIndex('idx_tasks_status_position');
            
            // ثم حذف الحقول
            $table->dropColumn(['progress', 'position']);
        });
    }
};

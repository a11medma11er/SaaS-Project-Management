<?php
use App\Models\Task;

// Find a pending task or create one
$task = Task::firstOrCreate(
    ['title' => 'AI Test Task'],
    [
        'description' => 'This task is designed to test AI overdue analysis',
        'status' => 'pending',
        'priority' => 'high',
        'project_id' => 1,
        'created_by' => 1
    ]
);

// Make it 5 days overdue
$task->due_date = now()->subDays(5);
$task->save();

echo "Task #{$task->id} is now set to overdue (5 days ago).";

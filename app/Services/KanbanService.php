<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use App\Enums\TaskStatus;
use Illuminate\Support\Facades\DB;

class KanbanService
{
    /**
     * Get tasks grouped by kanban_status for Kanban board
     * Only tasks with kanban_status set (not null) will appear on the board
     */
    public function getTasksGroupedByStatus(User $user, array $filters = []): array
    {
        $query = Task::query();
        
        // Only get tasks that are ON the Kanban board (kanban_status is not null)
        $query->whereNotNull('kanban_status');
        
        // Role-based filtering
        if (!$user->hasRole('Super Admin') && !$user->hasRole('Admin')) {
            if ($user->hasRole('Manager')) {
                // Manager sees tasks in their projects
                $projectIds = $user->managedProjects()->pluck('id');
                $query->whereIn('project_id', $projectIds);
            } else {
                // User sees only assigned tasks
                $query->whereHas('assignedUsers', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            }
        }
        
        // Apply filters
        if (!empty($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }
        
        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }
        
        if (!empty($filters['assignee_id'])) {
            $query->whereHas('assignedUsers', function ($q) use ($filters) {
                $q->where('user_id', $filters['assignee_id']);
            });
        }
        
        $tasks = $query->with(['assignedUsers', 'project', 'comments', 'attachments'])
            ->kanbanOrder()
            ->get();
        
        // Group by kanban_status column
        return [
            'unassigned' => $tasks->where('kanban_status', 'unassigned'),
            'todo' => $tasks->where('kanban_status', 'todo'),
            'inprogress' => $tasks->where('kanban_status', 'inprogress'),
            'reviews' => $tasks->where('kanban_status', 'review'),
            'completed' => $tasks->where('kanban_status', 'completed'),
        ];
    }
    
    /**
     * Move task to new status and position
     * @param Task $task
     * @param string|TaskStatus $newStatus - can be string value or TaskStatus enum
     * @param int $newPosition
     * @return bool
     */
    public function moveTask(Task $task, $newStatus, int $newPosition = 0): bool
    {
        // Convert string to TaskStatus enum if needed
        if (is_string($newStatus)) {
            $newStatus = TaskStatus::tryFrom($newStatus) ?? TaskStatus::PENDING;
        }
        
        DB::beginTransaction();
        try {
            $oldStatus = $task->status;
            
            // Update task
            $task->status = $newStatus;
            $task->position = $newPosition;
            $task->save();
            
            // Reorder other tasks in the new status
            $this->reorderTasksAfterMove($newStatus->value, $newPosition, $task->id);
            
            // Log activity
            activity()
                ->performedOn($task)
                ->withProperties([
                    'old_status' => $oldStatus?->value ?? 'unknown',
                    'new_status' => $newStatus->value,
                    'new_position' => $newPosition
                ])
                ->log('task_moved_in_kanban');
            
            DB::commit();
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error moving task in Kanban: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Reorder tasks within a status column
     */
    public function reorderTasksInColumn(string $status, array $taskIds): bool
    {
        DB::beginTransaction();
        try {
            foreach ($taskIds as $position => $taskId) {
                Task::where('id', $taskId)
                    ->update(['position' => $position]);
            }
            
            DB::commit();
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error reordering tasks: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Validate if status transition is allowed
     */
    public function validateStatusTransition(Task $task, string $newStatus): bool
    {
        $currentStatus = $task->status->value;
        
        // Can't move from completed/cancelled back to other statuses
        if (in_array($currentStatus, ['completed', 'cancelled'])) {
            return $newStatus === $currentStatus;
        }
        
        // All other transitions are allowed
        return true;
    }
    
    /**
     * Calculate progress based on subtasks completion
     */
    public function calculateProgress(Task $task): int
    {
        $subTasks = $task->subTasks;
        
        if ($subTasks->isEmpty()) {
            return 0;
        }
        
        $completedCount = $subTasks->where('completed', true)->count();
        $totalCount = $subTasks->count();
        
        return (int) round(($completedCount / $totalCount) * 100);
    }
    
    /**
     * Reorder tasks after moving one to a new position
     */
    protected function reorderTasksAfterMove(string $status, int $newPosition, int $movedTaskId): void
    {
        Task::where('status', $status)
            ->where('id', '!=', $movedTaskId)
            ->where('position', '>=', $newPosition)
            ->increment('position');
    }
}

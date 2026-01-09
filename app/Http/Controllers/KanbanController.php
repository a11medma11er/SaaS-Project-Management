<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;
use App\Services\KanbanService;
use App\Enums\TaskStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KanbanController extends Controller
{
    protected $kanbanService;

    public function __construct(KanbanService $kanbanService)
    {
        $this->kanbanService = $kanbanService;
    }

    /**
     * Display the Kanban board with tasks grouped by status.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $filters = $request->only(['project_id', 'priority', 'assignee_id']);
        
        // Get tasks using KanbanService
        $boardTasks = $this->kanbanService->getTasksGroupedByStatus($user, $filters);
        
        // Get projects and users for filters
        $projects = Project::orderBy('title')->get();
        $users = \App\Models\User::orderBy('name')->get();

        return view('apps-tasks-kanban', compact('boardTasks', 'projects', 'users'));
    }

    /**
     * Update task kanban_status via AJAX (Drag & Drop).
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'taskId' => 'required|exists:tasks,id',
            'status' => 'required|string'
        ]);

        $task = Task::findOrFail($request->taskId);
        
        // Check authorization - use role check
        $user = Auth::user();
        if (!$user->hasRole(['Super Admin', 'Admin', 'Manager']) && !$user->can('update-tasks')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        // Map frontend column IDs to kanban_status values
        $kanbanStatusMap = [
            'unassigned-task' => 'unassigned',
            'todo-task' => 'todo',
            'inprogress-task' => 'inprogress',
            'reviews-task' => 'review',
            'completed-task' => 'completed',
        ];

        $newKanbanStatus = $kanbanStatusMap[$request->status] ?? 'todo';
        $newPosition = $request->input('position', 0);
        
        try {
            $task->kanban_status = $newKanbanStatus;
            $task->position = $newPosition;
            $task->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Task moved successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating kanban status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update task'
            ], 400);
        }
    }

    /**
     * Update task position within same status column
     */
    public function updatePosition(Request $request)
    {
        $request->validate([
            'taskId' => 'required|exists:tasks,id',
            'newPosition' => 'required|integer|min:0',
            'statusColumn' => 'required|string'
        ]);

        $task = Task::findOrFail($request->taskId);
        
        if (!Auth::user()->can('update', $task)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($this->kanbanService->moveTask($task, $task->status->value, $request->newPosition)) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 400);
    }

    /**
     * Get available tasks for adding to Kanban board (tasks not on board)
     */
    public function getAvailableTasks(Request $request)
    {
        $user = Auth::user();
        
        // Only get tasks that are NOT on Kanban board (kanban_status is null)
        $query = Task::whereNull('kanban_status');
        
        // Role-based filtering
        if (!$user->hasRole('Super Admin') && !$user->hasRole('Admin')) {
            if ($user->hasRole('Manager')) {
                $projectIds = Project::where('team_lead_id', $user->id)
                    ->orWhereHas('members', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })->pluck('id');
                $query->whereIn('project_id', $projectIds);
            } else {
                $query->whereHas('assignedUsers', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            }
        }
        
        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        
        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        
        // Exclude certain statuses if needed
        if ($request->filled('exclude_status')) {
            $statusMap = [
                'unassigned' => null,
                'todo' => 'pending',
                'inprogress' => 'in_progress',
                'reviews' => 'review',
                'completed' => 'completed',
            ];
            
            $excludeStatus = $statusMap[$request->exclude_status] ?? null;
            if ($excludeStatus) {
                $query->where('status', '!=', $excludeStatus);
            }
        }
        
        $tasks = $query->with('project')
            ->select('id', 'title', 'priority', 'project_id', 'status')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
        
        // Add priority color
        $tasks->each(function($task) {
            $task->priority_color = match($task->priority) {
                'High' => 'danger',
                'Medium' => 'warning',
                'Low' => 'success',
                default => 'secondary'
            };
        });
        
        return response()->json(['tasks' => $tasks]);
    }

    /**
     * Add existing tasks to Kanban by setting kanban_status
     */
    public function addExistingTasks(Request $request)
    {
        $request->validate([
            'task_ids' => 'required|array',
            'task_ids.*' => 'exists:tasks,id',
            'target_status' => 'required|string'
        ]);

        // Map target column to kanban_status value
        $kanbanStatusMap = [
            'unassigned' => 'unassigned',
            'todo' => 'todo',
            'inprogress' => 'inprogress',
            'reviews' => 'review',
            'completed' => 'completed',
        ];

        $newKanbanStatus = $kanbanStatusMap[$request->target_status] ?? 'todo';

        $user = Auth::user();
        
        // Check user has permission to update tasks
        $hasPermission = $user->hasRole(['Super Admin', 'Admin', 'Manager']) || $user->can('update-tasks');
        
        if (!$hasPermission) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $updatedCount = 0;
            $tasks = Task::whereIn('id', $request->task_ids)->get();
            
            foreach ($tasks as $task) {
                // Set kanban_status to add task to Kanban board
                $task->kanban_status = $newKanbanStatus;
                $task->save();
                $updatedCount++;
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Successfully added {$updatedCount} task(s)",
                'count' => $updatedCount
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error adding tasks to Kanban: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to add tasks'
            ], 500);
        }
    }

    /**
     * Remove task from Kanban board (set kanban_status to null)
     */
    public function removeFromKanban(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id'
        ]);

        $task = Task::findOrFail($request->task_id);
        
        // Check authorization
        $user = Auth::user();
        if (!$user->hasRole(['Super Admin', 'Admin', 'Manager']) && !$user->can('update-tasks')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        try {
            // Set kanban_status to null to remove from Kanban board
            $task->kanban_status = null;
            $task->position = 0;
            $task->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Task removed from Kanban board'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error removing task from Kanban: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove task'
            ], 500);
        }
    }
}

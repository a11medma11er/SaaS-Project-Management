<?php

namespace App\Services\AI;

use App\Models\AI\AIDecision;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AIAutomationService
{
    protected $decisionEngine;
    protected $guardrailService;

    public function __construct(
        AIDecisionEngine $decisionEngine,
        AIGuardrailService $guardrailService
    ) {
        $this->decisionEngine = $decisionEngine;
        $this->guardrailService = $guardrailService;
    }

    /**
     * Execute automated AI analysis based on triggers
     */
    public function runAutomatedAnalysis(): array
    {
        $results = [
            'tasks_analyzed' => 0,
            'decisions_created' => 0,
            'auto_executed' => 0,
            'errors' => [],
        ];

        try {
            // 1. Check overdue tasks
            $overdueTasks = $this->checkOverdueTasks();
            $results['tasks_analyzed'] += count($overdueTasks);
            
            // 2. Check task priority
            $priorityTasks = $this->checkPriorityAdjustments();
            $results['tasks_analyzed'] += count($priorityTasks);
            
            // 3. Check resource allocation
            $resourceTasks = $this->checkResourceAllocation();
            $results['tasks_analyzed'] += count($resourceTasks);
            
            // 4. Check project health
            $projects = $this->checkProjectHealth();
            $results['tasks_analyzed'] += count($projects);
            
            // 5. Check CUSTOM rules
            $customStats = $this->checkCustomRules();
            $results['tasks_analyzed'] += $customStats['analyzed'];
            $results['decisions_created'] += $customStats['decisions'];
            
            Log::info('AI Automation completed', $results);
            
        } catch (\Exception $e) {
            $results['errors'][] = $e->getMessage();
            Log::error('AI Automation failed', ['error' => $e->getMessage()]);
        }

        return $results;
    }

    /**
     * Check and analyze overdue tasks
     */
    protected function checkOverdueTasks(): array
    {
        $tasks = Task::where('status', '!=', 'completed')
            ->where('due_date', '<', now())
            ->whereDoesntHave('aiDecisions', function ($query) {
                $query->where('decision_type', 'overdue_task_analysis')
                    ->where('created_at', '>=', now()->subDay());
            })
            ->limit(10)
            ->get();

        foreach ($tasks as $task) {
            try {
                $decision = $this->decisionEngine->analyzeTask($task, 'overdue_task_analysis');
                
                if ($decision && $decision->confidence_score >= 0.9) {
                    // High confidence - can auto-execute with guardrails
                    $this->attemptAutoExecution($decision);
                }
            } catch (\Exception $e) {
                Log::error('Failed to analyze overdue task', [
                    'task_id' => $task->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $tasks->toArray();
    }

    /**
     * Check tasks that need priority adjustments
     */
    protected function checkPriorityAdjustments(): array
    {
        $tasks = Task::where('status', 'in_progress')
            ->where(function ($query) {
                $query->where('due_date', '<=', now()->addDays(3))
                    ->orWhereHas('dependencies', function ($q) {
                        $q->where('status', 'completed');
                    });
            })
            ->limit(10)
            ->get();

        foreach ($tasks as $task) {
            try {
                $decision = $this->decisionEngine->analyzeTask($task, 'priority_adjustment');
                
                if ($decision && $decision->confidence_score >= 0.85) {
                    $this->attemptAutoExecution($decision);
                }
            } catch (\Exception $e) {
                Log::error('Failed to check priority', [
                    'task_id' => $task->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $tasks->toArray();
    }

    /**
     * Check resource allocation
     */
    protected function checkResourceAllocation(): array
    {
        $tasks = Task::doesntHave('assignedUsers')
            ->where('status', 'pending')
            ->where('priority', 'high')
            ->limit(5)
            ->get();

        foreach ($tasks as $task) {
            try {
                $decision = $this->decisionEngine->analyzeTask($task, 'resource_allocation');
            } catch (\Exception $e) {
                Log::error('Failed to check resources', [
                    'task_id' => $task->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $tasks->toArray();
    }

    /**
     * Check project health
     */
    protected function checkProjectHealth(): array
    {
        $projects = Project::where('status', 'active')
            ->whereHas('tasks', function ($query) {
                $query->where('due_date', '<', now()->addWeek());
            })
            ->limit(5)
            ->get();

        foreach ($projects as $project) {
            try {
                $decision = $this->decisionEngine->analyzeProject($project);
            } catch (\Exception $e) {
                Log::error('Failed to check project health', [
                    'project_id' => $project->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $projects->toArray();
    }

    /**
     * Attempt to auto-execute decision with guardrails
     */
    protected function attemptAutoExecution(AIDecision $decision): bool
    {
        // Check guardrails
        $guardrailCheck = $this->guardrailService->checkDecision($decision);
        
        if (!$guardrailCheck['passed']) {
            Log::warning('Auto-execution blocked by guardrails', [
                'decision_id' => $decision->id,
                'violations' => $guardrailCheck['violations'],
            ]);
            return false;
        }

        // Only auto-execute safe decisions
        $safeTypes = ['priority_adjustment', 'resource_suggestion'];
        
        if (!in_array($decision->decision_type, $safeTypes)) {
            return false;
        }

        try {
            // Execute the decision
            $decision->update([
                'user_action' => 'auto_accepted',
                'executed_at' => now(),
                'execution_result' => ['status' => 'success', 'method' => 'automated'],
            ]);

            Log::info('Decision auto-executed', [
                'decision_id' => $decision->id,
                'type' => $decision->decision_type,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Auto-execution failed', [
                'decision_id' => $decision->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Schedule AI analysis for specific time
     */
    /**
     * Schedule AI analysis for specific time (Database Backed)
     */
    public function scheduleAnalysis(string $type, array $params, Carbon $runAt): void
    {
        \App\Models\AI\AISchedule::create([
            'type' => $type,
            'params' => $params,
            'run_at' => $runAt,
            'status' => 'pending'
        ]);

        Log::info('AI analysis scheduled (DB)', [
            'type' => $type,
            'run_at' => $runAt->toDateTimeString(),
        ]);
    }

    /**
     * Check and run scheduled analyses
     */
    public function runScheduledAnalyses(): array
    {
        $results = [];
        
        // Get all pending scheduled analyses that are due
        $schedules = \App\Models\AI\AISchedule::where('status', 'pending')
            ->where('run_at', '<=', now())
            ->get();
            
        foreach ($schedules as $schedule) {
            try {
                // Mark as processing
                $schedule->update(['status' => 'processing']);
                
                // Execute Logic based on type
                $output = [];
                
                if ($schedule->type === 'automation_run') {
                    $output = $this->runAutomatedAnalysis();
                } else {
                    // Placeholder for other types (e.g., reports)
                    $output = ['message' => "Executed {$schedule->type}"];
                }
                
                // Mark as completed
                $schedule->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'output' => $output
                ]);
                
                $results[] = "Executed Schedule #{$schedule->id}: {$schedule->type}";
                
            } catch (\Exception $e) {
                // Mark as failed
                $schedule->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage()
                ]);
                
                Log::error("Scheduled AI job #{$schedule->id} failed", ['error' => $e->getMessage()]);
            }
        }

        return $results;
    }

    /**
     * Create automation rule
     */
    /**
     * Create automation rule
     */
    public function createAutomationRule(array $rule): array
    {
        $validatedRule = $this->validateRule($rule);
        
        // Store rule in database
        $newRule = \App\Models\AI\AutomationRule::create($validatedRule);
        
        Log::info('Automation rule created', ['rule_id' => $newRule->id]);
        
        return $newRule->toArray();
    }

    /**
     * Validate automation rule
     */
    protected function validateRule(array $rule): array
    {
        $required = ['trigger', 'conditions', 'action'];
        
        foreach ($required as $field) {
            if (!isset($rule[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }

        return [
            'name' => $rule['name'] ?? 'Untitled Rule',
            'trigger' => $rule['trigger'], 
            'conditions' => $rule['conditions'],
            'action' => $rule['action'],
            'enabled' => $rule['enabled'] ?? true,
            'is_active' => $rule['enabled'] ?? true,
        ];
    }

    /**
     * Get active automation rules
     */
    public function getActiveRules(): array
    {
        return \App\Models\AI\AutomationRule::where('is_active', true)
            ->get()
            ->toArray();
    }

    /**
     * Evaluate custom rules for a task
     */
    /**
     * Check valid custom rules against active tasks
     */
    protected function checkCustomRules(): array
    {
        $stats = ['analyzed' => 0, 'decisions' => 0];
        
        // Get active tasks (pending/in_progress)
        $tasks = Task::whereIn('status', ['pending', 'in_progress'])
            ->with(['assignedUsers', 'project'])
            ->limit(20) // Batch size for safety
            ->get();
            
        $stats['analyzed'] = $tasks->count();
        
        foreach ($tasks as $task) {
            if ($this->evaluateCustomRules($task)) {
                $stats['decisions']++;
            }
        }
        
        return $stats;
    }

    /**
     * Evaluate custom rules for a task
     */
    /**
     * Evaluate custom rules for a task
     */
    protected function evaluateCustomRules(Task $task): bool
    {
        $rules = $this->getActiveRules();
        $decisionCreated = false;
        
        foreach ($rules as $rule) {
            $conditions = $rule['conditions'];
            $met = false;
            
            // Generic Logic Interpreter
            if (isset($conditions['field'])) {
                $field = $conditions['field'];
                $operator = $conditions['operator'] ?? '=';
                $value = $conditions['value'];
                $actualValue = null;

                // 1. Determine Actual Value
                if ($field === 'assigned_users_count') {
                    $actualValue = $task->assignedUsers()->count();
                } elseif ($field === 'days_until_due') {
                    $actualValue = $task->due_date ? now()->diffInDays($task->due_date, false) : 0;
                } elseif ($field === 'days_overdue') {
                    $actualValue = ($task->due_date && $task->due_date < now()) ? now()->diffInDays($task->due_date) : 0;
                } elseif (str_contains($field, '.')) {
                    // Support Dot Notation for Relations (e.g., 'project.status', 'owner.name')
                    // Support Collection Wildcard (e.g., 'assignedUsers.*.avatar')
                    
                    if (str_contains($field, '.*.')) {
                        // Collection Logic: Check if ANY item in collection matches
                        [$relation, $subField] = explode('.*.', $field, 2);
                        
                        // Resolve relation (e.g., assignedUsers)
                        $collection = $task->$relation ?? null;
                        
                        if ($collection instanceof \Illuminate\Database\Eloquent\Collection) {
                            // Check if ANY item matches the condition directly here
                            // We need to bypass the standard switch below for collections
                            $hasMatch = $collection->contains(function ($item) use ($subField, $operator, $value) {
                                $itemValue = $item->getAttribute($subField);
                                
                                // Handle NULL string input
                                if ($value === 'NULL') $value = null;

                                switch ($operator) {
                                    case '=': return $itemValue == $value;
                                    case '!=': return $itemValue != $value;
                                    default: return false; 
                                }
                            });
                            
                            if ($hasMatch) {
                                $met = true;
                                $actualValue = "Match found in collection";
                                goto skip_standard_comparison;
                            }
                        }
                    } else {
                        // Standard Dot Notation (Single Object)
                        $parts = explode('.', $field);
                        $current = $task;
                        $valid = true;
                        
                        foreach ($parts as $part) {
                            if ($current && (is_array($current) || $current instanceof \ArrayAccess)) {
                                $current = $current[$part] ?? null;
                            } elseif ($current && is_object($current)) {
                                $current = $current->$part ?? null;
                            } else {
                                $valid = false;
                                break;
                            }
                        }
                        $actualValue = $valid ? $current : null;
                    }
                } else {
                    // Start by checking simple attributes
                    $actualValue = $task->getAttribute($field);
                }

                // Handle string "NULL" as actual null
                if ($value === 'NULL') $value = null;

                // 2. Compare (Standard Logic)
                switch ($operator) {
                    case '>':
                        $met = $actualValue > $value;
                        break;
                    case '<':
                        $met = $actualValue < $value;
                        break;
                    case '>=':
                        $met = $actualValue >= $value;
                        break;
                    case '<=':
                        $met = $actualValue <= $value;
                        break;
                    case '=':
                    case '==':
                        $met = $actualValue == $value;
                        break;
                    case '!=':
                        $met = $actualValue != $value;
                        break;
                    case 'IN':
                        $met = in_array($actualValue, (array)$value);
                        break;
                }
                
                skip_standard_comparison:
            }
            
            if ($met) {
                try {
                    // Rule Matched! Create Decision
                    $this->decisionEngine->createDecision(
                        'rule_triggered',
                        $task->id,
                        $task->project_id,
                        "Custom Rule: {$rule['name']}",
                        [
                            "Rule '{$rule['name']}' triggered",
                            "Condition: {$field} {$operator} {$value}",
                            "Actual Value: " . (is_array($actualValue) ? json_encode($actualValue) : $actualValue)
                        ],
                        0.95,
                        [$rule['action']]
                    );
                    
                    // Update rule stats
                    \App\Models\AI\AutomationRule::find($rule['id'])
                        ->update(['last_triggered_at' => now()]);
                        
                    $decisionCreated = true;
                } catch (\Exception $e) {
                    Log::error("Failed to execute custom rule {$rule['id']}", ['error' => $e->getMessage()]);
                }
            }
        }
        
        return $decisionCreated;
    }

    /**
     * Smart workload balancing
     */
    public function balanceWorkload(): array
    {
        $users = \App\Models\User::whereHas('tasks', function ($query) {
            $query->where('status', '!=', 'completed');
        })->get();

        $recommendations = [];
        
        // Get configurable threshold
        $threshold = (int) (\App\Models\AI\AISetting::where('key', 'workload_threshold')->value('value') ?? 5);

        foreach ($users as $user) {
            $activeTasks = $user->tasks()->where('status', '!=', 'completed')->count();
            
            if ($activeTasks > $threshold) {
                $recommendations[] = [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'active_tasks' => $activeTasks,
                    'recommendation' => "Consider redistributing tasks (Threshold: >{$threshold})",
                ];
            }
        }

        return $recommendations;
    }
}

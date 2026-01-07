<?php

namespace App\Services;

use App\Models\Task;
use App\Enums\TaskStatus;
use App\Exceptions\InvalidStatusTransitionException;

class TaskStatusService
{
    /**
     * Allowed status transitions
     */
    private const ALLOWED_TRANSITIONS = [
        'new' => ['pending', 'in_progress', 'cancelled'],
        'pending' => ['in_progress', 'on_hold', 'cancelled'],
        'in_progress' => ['completed', 'on_hold', 'pending', 'cancelled'],
        'on_hold' => ['pending', 'in_progress', 'cancelled'],
        'completed' => [], // No transitions from completed
        'cancelled' => [], // No transitions from cancelled
    ];

    /**
     * Check if a status transition is allowed
     */
    public function canTransition(string|TaskStatus $from, string|TaskStatus $to): bool
    {
        $fromValue = $from instanceof TaskStatus ? $from->value : $from;
        $toValue = $to instanceof TaskStatus ? $to->value : $to;
        
        return in_array($toValue, self::ALLOWED_TRANSITIONS[$fromValue] ?? []);
    }

    /**
     * Validate a status transition
     * 
     * @throws InvalidStatusTransitionException
     */
    public function validateTransition(Task $task, string|TaskStatus $newStatus): void
    {
        $newStatusValue = $newStatus instanceof TaskStatus ? $newStatus->value : $newStatus;
        
        if (!$this->canTransition($task->status, $newStatusValue)) {
            throw new InvalidStatusTransitionException(
                "Cannot transition from {$task->status->label()} to " . 
                TaskStatus::from($newStatusValue)->label()
            );
        }
    }

    /**
     * Get allowed next statuses for a task
     * 
     * @return array<TaskStatus>
     */
    public function getAllowedNextStatuses(Task $task): array
    {
        $currentStatus = $task->status instanceof TaskStatus 
            ? $task->status->value 
            : $task->status;
            
        $allowedValues = self::ALLOWED_TRANSITIONS[$currentStatus] ?? [];
        
        return array_map(
            fn($value) => TaskStatus::from($value),
            $allowedValues
        );
    }

    /**
     * Transition task to new status
     * 
     * @throws InvalidStatusTransitionException
     */
    public function transitionTo(Task $task, string|TaskStatus $newStatus): Task
    {
        $this->validateTransition($task, $newStatus);
        
        $newStatusEnum = $newStatus instanceof TaskStatus 
            ? $newStatus 
            : TaskStatus::from($newStatus);
            
        $task->status = $newStatusEnum;
        $task->save();
        
        return $task;
    }
}

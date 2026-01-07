<?php

namespace App\Rules;

use App\Enums\TaskStatus;
use App\Enums\TaskPriority;

class TaskRules
{
    /**
     * Validation rules for task status
     */
    public static function status(): array
    {
        return [
            'required',
            'in:' . implode(',', TaskStatus::values())
        ];
    }

    /**
     * Validation rules for task priority
     */
    public static function priority(): array
    {
        return [
            'required',
            'in:' . implode(',', TaskPriority::values())
        ];
    }

    /**
     * Validation rules for due date
     * Accepts both string and TaskStatus enum
     */
    public static function dueDate($currentStatus = null): array
    {
        // Convert Enum to string value if needed
        $statusValue = $currentStatus instanceof TaskStatus 
            ? $currentStatus->value 
            : $currentStatus;
            
        $rules = ['required', 'date'];
        
        // New tasks must have future due date
        if ($statusValue === null || $statusValue === 'new') {
            $rules[] = 'after_or_equal:today';
        }
        
        return $rules;
    }

    /**
     * Validation rules for task title
     */
    public static function title(): array
    {
        return ['required', 'string', 'max:255', 'min:3'];
    }

    /**
     * Validation rules for task description
     */
    public static function description(): array
    {
        return ['nullable', 'string', 'max:5000'];
    }

    /**
     * Validation rules for client name
     */
    public static function clientName(): array
    {
        return ['nullable', 'string', 'max:255'];
    }

    /**
     * Validation rules for assigned users
     */
    public static function assignedUsers(): array
    {
        return [
            'nullable',
            'array',
            'min:1',
            'max:10', // Limit to 10 users per task
        ];
    }

    /**
     * Validation rules for tags
     */
    public static function tags(): array
    {
        return [
            'nullable',
            'array',
            'max:10', // Limit to 10 tags
        ];
    }
}

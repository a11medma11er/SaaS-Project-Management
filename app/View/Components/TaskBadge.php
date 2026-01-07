<?php

namespace App\View\Components;

use App\Enums\TaskStatus;
use App\Enums\TaskPriority;
use Illuminate\View\Component;

class TaskBadge extends Component
{
    public string $value;
    public string $type;

    /**
     * Create a new component instance.
     *
     * @param string $value The status or priority value
     * @param string $type Either 'status' or 'priority'
     */
    public function __construct(string $value, string $type = 'status')
    {
        $this->value = $value;
        $this->type = $type;
    }

    /**
     * Get the badge CSS classes
     */
    public function badgeClass(): string
    {
        if ($this->type === 'status') {
            $status = TaskStatus::from($this->value);
            return $status->badgeClass();
        }
        
        $priority = TaskPriority::from($this->value);
        return $priority->badgeClass();
    }

    /**
     * Get the label text
     */
    public function label(): string
    {
        if ($this->type === 'status') {
            return TaskStatus::from($this->value)->label();
        }
        
        return TaskPriority::from($this->value)->label();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.task-badge');
    }
}

<?php

namespace App\Enums;

enum TaskPriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case URGENT = 'urgent';

    /**
     * Get human-readable label
     */
    public function label(): string
    {
        return match($this) {
            self::LOW => 'Low',
            self::MEDIUM => 'Medium',
            self::HIGH => 'High',
            self::URGENT => 'Urgent',
        };
    }

    /**
     * Get Bootstrap color class
     */
    public function color(): string
    {
        return match($this) {
            self::LOW => 'success',
            self::MEDIUM => 'info',
            self::HIGH => 'warning',
            self::URGENT => 'danger',
        };
    }

    /**
     * Get badge CSS classes
     */
    public function badgeClass(): string
    {
        $color = $this->color();
        return "badge bg-{$color} text-uppercase";
    }

    /**
     * Get numeric order for sorting
     */
    public function order(): int
    {
        return match($this) {
            self::LOW => 1,
            self::MEDIUM => 2,
            self::HIGH => 3,
            self::URGENT => 4,
        };
    }

    /**
     * Get icon class
     */
    public function icon(): string
    {
        return match($this) {
            self::LOW => 'ri-arrow-down-line',
            self::MEDIUM => 'ri-subtract-line',
            self::HIGH => 'ri-arrow-up-line',
            self::URGENT => 'ri-error-warning-line',
        };
    }

    /**
     * Check if priority requires immediate attention
     */
    public function isUrgent(): bool
    {
        return in_array($this, [self::HIGH, self::URGENT]);
    }

    /**
     * Get all possible values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all cases with labels
     */
    public static function options(): array
    {
        return array_map(
            fn(self $priority) => [
                'value' => $priority->value,
                'label' => $priority->label(),
                'color' => $priority->color(),
                'order' => $priority->order(),
            ],
            self::cases()
        );
    }
}

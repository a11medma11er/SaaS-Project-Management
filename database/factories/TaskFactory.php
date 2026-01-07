<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use App\Enums\TaskStatus;
use App\Enums\TaskPriority;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'task_number' => '#VLZ' . str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'project_id' => null, // Can be overridden
            'client_name' => $this->faker->company(),
            'due_date' => $this->faker->dateTimeBetween('now', '+30 days'),
            'status' => $this->faker->randomElement(TaskStatus::cases())->value,
            'priority' => $this->faker->randomElement(TaskPriority::cases())->value,
            'created_by' => User::factory(),
        ];
    }

    public function new_status(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatus::NEW->value,
        ]);
    }

    public function in_progress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatus::IN_PROGRESS->value,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatus::COMPLETED->value,
        ]);
    }

    public function high_priority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => TaskPriority::HIGH->value,
        ]);
    }

    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => TaskPriority::URGENT->value,
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => $this->faker->dateTimeBetween('-30 days', '-1 day'),
            'status' => TaskStatus::IN_PROGRESS->value,
        ]);
    }

    public function due_soon(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => $this->faker->dateTimeBetween('now', '+3 days'),
            'status' => TaskStatus::NEW->value,
        ]);
    }
}

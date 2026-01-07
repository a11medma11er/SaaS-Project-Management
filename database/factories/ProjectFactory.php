<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'slug' => $this->faker->slug(),
            'description' => $this->faker->paragraph(),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
            'status' => $this->faker->randomElement(['planning', 'active', 'completed']),
            'privacy' => $this->faker->randomElement(['public', 'private']),
            'category' => $this->faker->randomElement(['web', 'mobile', 'desktop', 'other']),
            'skills' => [$this->faker->word(), $this->faker->word()],
            'deadline' => $this->faker->dateTimeBetween('now', '+6 months'),
            'start_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'progress' => $this->faker->numberBetween(0, 100),
            'is_favorite' => $this->faker->boolean(),
            'team_lead_id' => User::factory(),
            'created_by' => User::factory(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'progress' => 100,
        ]);
    }

    public function favorite(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_favorite' => true,
        ]);
    }
}

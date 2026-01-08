<?php

namespace Database\Factories\AI;

use App\Models\AI\AIDecision;
use Illuminate\Database\Eloquent\Factories\Factory;

class AIDecisionFactory extends Factory
{
    protected $model = AIDecision::class;

    public function definition(): array
    {
        return [
            'decision_type' => $this->faker->randomElement([
                'priority_adjustment',
                'deadline_change',
                'resource_allocation',
                'task_breakdown',
                'risk_assessment',
            ]),
            'entity_type' => 'task',
            'entity_id' => $this->faker->numberBetween(1, 100),
            'confidence_score' => $this->faker->randomFloat(2, 0.5, 1.0),
            'reasoning' => $this->faker->sentence(),
            'recommended_action' => [
                'action' => 'adjust_priority',
                'details' => $this->faker->text(100),
            ],
            'context_data' => [
                'analyzed_at' => now()->toIso8601String(),
                'factors' => ['deadline', 'priority'],
            ],
            'user_action' => null,
            'user_feedback' => null,
            'executed_at' => null,
            'reviewed_at' => null,
        ];
    }

    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_action' => 'accepted',
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_action' => 'rejected',
            'reviewed_at' => now(),
        ]);
    }

    public function highConfidence(): static
    {
        return $this->state(fn (array $attributes) => [
            'confidence_score' => $this->faker->randomFloat(2, 0.85, 1.0),
        ]);
    }

    public function lowConfidence(): static
    {
        return $this->state(fn (array $attributes) => [
            'confidence_score' => $this->faker->randomFloat(2, 0.5, 0.7),
        ]);
    }
}

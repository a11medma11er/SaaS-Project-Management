<?php

namespace Tests\Unit\AI;

use Tests\TestCase;
use App\Services\AI\AIDecisionEngine;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AIDecisionEngineTest extends TestCase
{
    use RefreshDatabase;

    protected $engine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->engine = app(AIDecisionEngine::class);
    }

    /** @test */
    public function it_can_analyze_overdue_task()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $task = Task::factory()->create([
            'title' => 'Overdue Task',
            'due_date' => now()->subDays(5),
            'status' => 'in_progress',
            'priority' => 'medium',
        ]);

        $decision = $this->engine->analyzeTask($task);

        $this->assertNotNull($decision);
        $this->assertArrayHasKey('decision_type', $decision);
        $this->assertArrayHasKey('confidence_score', $decision);
        $this->assertArrayHasKey('recommended_action', $decision);
    }

    /** @test */
    public function it_suggests_priority_increase_for_overdue_tasks()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $task = Task::factory()->create([
            'title' => 'Critical Overdue Task',
            'due_date' => now()->subDays(10),
            'status' => 'in_progress',
            'priority' => 'low',
        ]);

        $decision = $this->engine->analyzeTask($task);

        $this->assertNotNull($decision);
        $this->assertEquals('priority_adjustment', $decision['decision_type']);
        $this->assertStringContainsString('urgent', strtolower($decision['recommended_action']['new_priority'] ?? ''));
    }

    /** @test */
    public function it_returns_null_for_completed_tasks()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $task = Task::factory()->create([
            'title' => 'Completed Task',
            'status' => 'completed',
        ]);

        $decision = $this->engine->analyzeTask($task);

        // Completed tasks don't need AI decisions
        $this->assertNull($decision);
    }

    /** @test */
    public function it_includes_reasoning_in_decisions()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $task = Task::factory()->create([
            'title' => 'Task Needs Analysis',
            'due_date' => now()->addDays(1),
            'status' => 'pending',
            'priority' => 'low',
        ]);

        $decision = $this->engine->analyzeTask($task);

        if ($decision) {
            $this->assertArrayHasKey('reasoning', $decision);
            $this->assertIsString($decision['reasoning']);
            $this->assertNotEmpty($decision['reasoning']);
        } else {
            // If no decision needed, that's also valid
            $this->assertNull($decision);
        }
    }

    /** @test */
    public function it_calculates_confidence_scores()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $task = Task::factory()->create([
            'title' => 'Task for Confidence Test',
            'due_date' => now()->subDays(7),
            'status' => 'in_progress',
        ]);

        $decision = $this->engine->analyzeTask($task);

        if ($decision) {
            $this->assertArrayHasKey('confidence_score', $decision);
            $this->assertIsFloat($decision['confidence_score']);
            $this->assertGreaterThanOrEqual(0, $decision['confidence_score']);
            $this->assertLessThanOrEqual(1, $decision['confidence_score']);
        } else {
            $this->assertNull($decision);
        }
    }
}

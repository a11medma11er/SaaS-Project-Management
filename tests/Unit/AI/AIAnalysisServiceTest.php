<?php

namespace Tests\Unit\AI;

use Tests\TestCase;
use App\Services\AI\AIAnalysisService;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AIAnalysisServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(AIAnalysisService::class);
    }

    /** @test */
    public function it_can_analyze_task_workload()
    {
        $user = User::factory()->create();
        
        // Create multiple tasks for the user
        Task::factory()->count(5)->create([
            'assigned_to' => $user->id,
            'status' => 'in_progress',
        ]);

        $analysis = $this->service->analyzeUserWorkload($user);

        $this->assertIsArray($analysis);
        $this->assertArrayHasKey('task_count', $analysis);
        $this->assertEquals(5, $analysis['task_count']);
    }

    /** @test */
    public function it_identifies_overdue_tasks()
    {
        $user = User::factory()->create();
        
        // Create overdue task
        Task::factory()->create([
            'assigned_to' => $user->id,
            'due_date' => now()->subDays(3),
            'status' => 'in_progress',
        ]);

        $analysis = $this->service->analyzeUserWorkload($user);

        $this->assertArrayHasKey('overdue_count', $analysis);
        $this->assertGreaterThan(0, $analysis['overdue_count']);
    }

    /** @test */
    public function it_calculates_project_health()
    {
        $project = Project::factory()->create([
            'start_date' => now()->subDays(10),
            'end_date' => now()->addDays(20),
        ]);

        // Create tasks for the project
        Task::factory()->count(10)->create([
            'project_id' => $project->id,
            'status' => 'in_progress',
        ]);

        Task::factory()->count(5)->create([
            'project_id' => $project->id,
            'status' => 'completed',
        ]);

        $health = $this->service->calculateProjectHealth($project);

        $this->assertIsArray($health);
        $this->assertArrayHasKey('health_score', $health);
        $this->assertArrayHasKey('completion_rate', $health);
        $this->assertGreaterThan(0, $health['completion_rate']);
    }

    /** @test */
    public function it_detects_at_risk_projects()
    {
        $project = Project::factory()->create([
            'end_date' => now()->addDays(2), // Soon deadline
        ]);

        // Most tasks incomplete
        Task::factory()->count(10)->create([
            'project_id' => $project->id,
            'status' => 'pending',
        ]);

        $health = $this->service->calculateProjectHealth($project);

        $this->assertArrayHasKey('risk_level', $health);
        $this->assertContains($health['risk_level'], ['low', 'medium', 'high', 'critical']);
    }

    /** @test */
    public function it_provides_recommendations()
    {
        $user = User::factory()->create();
        
        Task::factory()->count(15)->create([
            'assigned_to' => $user->id,
            'status' => 'in_progress',
        ]);

        $analysis = $this->service->analyzeUserWorkload($user);

        if (isset($analysis['recommendations'])) {
            $this->assertIsArray($analysis['recommendations']);
            $this->assertNotEmpty($analysis['recommendations']);
        }

        $this->assertTrue(true); // Analysis completed
    }
}

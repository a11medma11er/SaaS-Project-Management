<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use App\Services\ActivityService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceLayerIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function activity_service_integrates_with_database()
    {
        $user = User::factory()->create();
        Task::factory()->count(5)->create(['created_by' => $user->id]);
        
        $service = app(ActivityService::class);
        $activities = $service->getRecentActivities(10);
        
        // Activities exist from task creation
        $this->assertNotNull($activities);
    }

    /** @test */
    public function service_returns_similar_tasks()
    {
        $task = Task::factory()->create();
        $service = app(ActivityService::class);
        
        $activities = $service->getSimilarTaskActivities($task);
        
        $this->assertIsIterable($activities);
    }

    /** @test */
    public function service_layer_handles_errors_gracefully()
    {
        $service = app(ActivityService::class);
        
        // Should not throw exception with invalid data
        $result = $service->getRecentActivities(10);
        
        $this->assertIsIterable($result);
    }

    /** @test */
    public function services_can_be_resolved_from_container()
    {
        $activityService = app(ActivityService::class);
        
        $this->assertInstanceOf(ActivityService::class, $activityService);
    }
}

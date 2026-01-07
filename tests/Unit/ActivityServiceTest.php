<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Task;
use App\Services\ActivityService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActivityServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_get_similar_task_activities()
    {
        $task = Task::factory()->create();
        $service = app(ActivityService::class);
        
        $activities = $service->getSimilarTaskActivities($task);
        
        $this->assertTrue(is_iterable($activities));
    }

    /** @test */
    public function can_get_recent_activities()
    {
        $service = app(ActivityService::class);
        
        $activities = $service->getRecentActivities(10);
        
        $this->assertTrue(is_iterable($activities));
    }

    /** @test */
    public function service_can_be_instantiated()
    {
        $service = app(ActivityService::class);
        
        $this->assertInstanceOf(ActivityService::class, $service);
    }
}

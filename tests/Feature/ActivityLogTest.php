<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Activitylog\Models\Activity;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Permission::create(['name' => 'view-activity-logs']);
        Permission::create(['name' => 'manage-activity-logs']);
        
        $role = Role::create(['name' => 'Admin']);
        $role->givePermissionTo(['view-activity-logs', 'manage-activity-logs']);
    }

    /** @test */
    public function task_creation_is_logged()
    {
        $user = User::factory()->create();
        
        $task = Task::factory()->create([
            'created_by' => $user->id,
            'title' => 'Test Task'
        ]);
        
        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'tasks',
            'description' => 'created',
            'subject_type' => Task::class,
            'subject_id' => $task->id,
        ]);
    }

    /** @test */
    public function task_update_is_logged()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['created_by' => $user->id]);
        
        activity()->causedBy($user)
            ->performedOn($task)
            ->log('updated');
        
        $this->assertDatabaseHas('activity_log', [
            'description' => 'updated',
            'subject_id' => $task->id,
            'causer_id' => $user->id,
        ]);
    }

    /** @test */
    public function user_with_permission_can_view_activity_logs()
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');
        
        $response = $this->actingAs($user)
            ->get('/management/activity-logs');
        
        $response->assertStatus(200);
    }

    /** @test */
    public function user_without_permission_cannot_view_activity_logs()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->get('/management/activity-logs');
        
        $response->assertStatus(403);
    }

    /** @test */
    public function activity_log_captures_context()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'created_by' => $user->id,
            'title' => 'Test Task'
        ]);
        
        // Activity should have context
        $activity = Activity::where('subject_id', $task->id)->first();
        
        $this->assertNotNull($activity);
        $this->assertEquals('tasks', $activity->log_name);
    }

    /** @test */
    public function can_retrieve_recent_activities()
    {
        $user = User::factory()->create();
        
        // Create multiple activities
        Task::factory()->count(5)->create(['created_by' => $user->id]);
        
        $activities = Activity::latest()->limit(5)->get();
        
        $this->assertCount(5, $activities);
    }
}

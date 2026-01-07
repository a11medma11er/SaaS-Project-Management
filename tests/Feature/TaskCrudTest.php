<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use App\Models\Project;
use App\Enums\TaskStatus;
use App\Enums\TaskPriority;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TaskCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Permission::create(['name' => 'view-tasks']);
        Permission::create(['name' => 'create-tasks']);
        Permission::create(['name' => 'edit-tasks']);
        Permission::create(['name' => 'delete-tasks']);
        
        $role = Role::create(['name' => 'Admin']);
        $role->givePermissionTo(['view-tasks', 'create-tasks', 'edit-tasks', 'delete-tasks']);
    }

    /** @test */
    public function guest_is_redirected_to_login()
    {
        $response = $this->get('/management/tasks');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function user_without_permission_cannot_view_tasks()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->get('/management/tasks');
        
        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_delete_task()
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');
        $task = Task::factory()->create(['created_by' => $user->id]);
        
        $response = $this->actingAs($user)
            ->delete("/management/tasks/{$task->id}");
        
        $response->assertRedirect();
        $this->assertSoftDeleted('tasks', [
            'id' => $task->id,
        ]);
    }

    /** @test */
    public function user_without_delete_permission_cannot_delete_task()
    {
        Permission::create(['name' => 'view-only']);
        $role = Role::create(['name' => 'Viewer']);
        $role->givePermissionTo('view-tasks');
        
        $user = User::factory()->create();
        $user->assignRole('Viewer');
        
        $task = Task::factory()->create();
        
        $response = $this->actingAs($user)
            ->delete("/management/tasks/{$task->id}");
        
        $response->assertStatus(403);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function user_can_view_task_details()
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');
        $task = Task::factory()->create();
        
        $response = $this->actingAs($user)
            ->get("/management/tasks/{$task->id}");
        
        $response->assertStatus(200);
        $response->assertSee($task->title);
    }
}

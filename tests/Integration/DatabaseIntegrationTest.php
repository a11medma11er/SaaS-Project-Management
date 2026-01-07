<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use App\Models\Project;
use App\Enums\TaskStatus;
use App\Enums\TaskPriority;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DatabaseIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function task_and_project_relationship_works()
    {
        $project = Project::factory()->create();
        $task = Task::factory()->create(['project_id' => $project->id]);
        
        $this->assertEquals($project->id, $task->project->id);
        $this->assertTrue($project->tasks->contains($task));
    }

    /** @test */
    public function user_created_tasks_relationship_works()
    {
        $user = User::factory()->create();
        $tasks = Task::factory()->count(3)->create(['created_by' => $user->id]);
        
        $this->assertCount(3, $user->createdTasks);
        $this->assertEquals($tasks->first()->id, $user->createdTasks->first()->id);
    }

    /** @test */
    public function soft_delete_works_correctly()
    {
        $task = Task::factory()->create();
        $taskId = $task->id;
        
        $task->delete();
        
        // Should not be in normal query
        $this->assertNull(Task::find($taskId));
        
        // Should be in withTrashed query
        $this->assertNotNull(Task::withTrashed()->find($taskId));
    }

    /** @test */
    public function database_transaction_rollback()
    {
        $this->expectException(\Exception::class);
        
        DB::transaction(function () {
            Task::factory()->create(['title' => 'Test Task']);
            throw new \Exception('Force rollback');
        });
        
        $this->assertDatabaseMissing('tasks', ['title' => 'Test Task']);
    }

    /** @test */
    public function eager_loading_prevents_n_plus_1()
    {
        Project::factory()->count(5)->create()->each(function ($project) {
            Task::factory()->count(3)->create(['project_id' => $project->id]);
        });
        
        DB::enableQueryLog();
        
        // Without eager loading - N+1 problem
        $projectsWithoutEager = Project::all();
        foreach ($projectsWithoutEager as $project) {
            $project->tasks->count();
        }
        $queriesWithout = count(DB::getQueryLog());
        
        DB::flushQueryLog();
        
        // With eager loading - efficient
        $projectsWithEager = Project::with('tasks')->get();
        foreach ($projectsWithEager as $project) {
            $project->tasks->count();
        }
        $queriesWith = count(DB::getQueryLog());
        
        // Eager loading should use fewer queries
        $this->assertLessThan($queriesWithout, $queriesWith);
    }

    /** @test */
    public function cache_integration_works()
    {
        $key = 'test_cache_key';
        $value = 'test_value';
        
        Cache::put($key, $value, 60);
        
        $this->assertEquals($value, Cache::get($key));
        
        Cache::forget($key);
        
        $this->assertNull(Cache::get($key));
    }

    /** @test */
    public function activity_log_records_task_creation()
    {
        $task = Task::factory()->create();
        
        $this->assertDatabaseHas('activity_log', [
            'subject_type' => Task::class,
            'subject_id' => $task->id,
            'description' => 'created',
        ]);
    }

    /** @test */
    public function multiple_models_can_be_saved_in_transaction()
    {
        DB::transaction(function () {
            $user = User::factory()->create();
            $project = Project::factory()->create(['created_by' => $user->id]);
            $task = Task::factory()->create([
                'project_id' => $project->id,
                'created_by' => $user->id
            ]);
            
            $this->assertDatabaseHas('users', ['id' => $user->id]);
            $this->assertDatabaseHas('projects', ['id' => $project->id]);
            $this->assertDatabaseHas('tasks', ['id' => $task->id]);
        });
    }

    /** @test */
    public function pivot_table_relationship_works()
    {
        $project = Project::factory()->create();
        $user = User::factory()->create();
        
        // Attach user to project
        $project->members()->attach($user->id);
        
        $this->assertTrue($project->members->contains($user));
        $this->assertTrue($user->projects->contains($project));
    }

    /** @test */
    public function query_scopes_work_correctly()
    {
        Task::factory()->create(['status' => TaskStatus::NEW]);
        Task::factory()->create(['status' => TaskStatus::COMPLETED]);
        Task::factory()->create(['due_date' => now()->subDays(5), 'status' => TaskStatus::IN_PROGRESS]);
        
        $newTasks = Task::whereStatus(TaskStatus::NEW)->get();
        $overdueTasks = Task::overdue()->get();
        
        $this->assertCount(1, $newTasks);
        $this->assertCount(1, $overdueTasks);
    }
}

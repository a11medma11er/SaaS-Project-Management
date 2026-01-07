<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Task;
use App\Enums\TaskStatus;
use App\Enums\TaskPriority;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskBusinessLogicTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function task_is_overdue_when_past_due_date_and_not_completed()
    {
        $task = Task::factory()->create([
            'due_date' => now()->subDays(5),
            'status' => TaskStatus::IN_PROGRESS,
        ]);
        
        $this->assertTrue($task->isOverdue());
    }

    /** @test */
    public function completed_task_is_not_overdue()
    {
        $task = Task::factory()->create([
            'due_date' => now()->subDays(5),
            'status' => TaskStatus::COMPLETED,
        ]);
        
        $this->assertFalse($task->isOverdue());
    }

    /** @test */
    public function task_with_future_due_date_is_not_overdue()
    {
        $task = Task::factory()->create([
            'due_date' => now()->addDays(5),
            'status' => TaskStatus::NEW,
        ]);
        
        $this->assertFalse($task->isOverdue());
    }

    /** @test */
    public function calculates_days_overdue_correctly()
    {
        $task = Task::factory()->create([
            'due_date' => now()->subDays(7),
            'status' => TaskStatus::IN_PROGRESS,
        ]);
        
        $this->assertEquals(7, $task->getDaysOverdue());
    }

    /** @test */
    public function non_overdue_task_returns_zero_days_overdue()
    {
        $task = Task::factory()->create([
            'due_date' => now()->addDays(3),
            'status' => TaskStatus::NEW,
        ]);
        
        $this->assertEquals(0, $task->getDaysOverdue());
    }

    /** @test */
    public function urgency_level_is_critical_when_overdue_by_more_than_7_days()
    {
        $task = Task::factory()->create([
            'due_date' => now()->subDays(10),
            'status' => TaskStatus::IN_PROGRESS,
        ]);
        
        $this->assertEquals('critical', $task->getUrgencyLevel());
    }

    /** @test */
    public function urgency_level_is_high_when_overdue_by_4_to_7_days()
    {
        $task = Task::factory()->create([
            'due_date' => now()->subDays(5),
            'status' => TaskStatus::IN_PROGRESS,
        ]);
        
        $this->assertEquals('high', $task->getUrgencyLevel());
    }

    /** @test */
    public function urgency_level_is_medium_when_overdue_by_1_to_3_days()
    {
        $task = Task::factory()->create([
            'due_date' => now()->subDays(2),
            'status' => TaskStatus::IN_PROGRESS,
        ]);
        
        $this->assertEquals('medium', $task->getUrgencyLevel());
    }

    /** @test */
    public function urgency_level_is_normal_when_not_overdue()
    {
        $task = Task::factory()->create([
            'due_date' => now()->addDays(5),
            'status' => TaskStatus::NEW,
        ]);
        
        $this->assertEquals('normal', $task->getUrgencyLevel());
    }

    /** @test */
    public function task_is_due_soon_within_3_days()
    {
        $task = Task::factory()->create([
            'due_date' => now()->addDays(2),
            'status' => TaskStatus::IN_PROGRESS,
        ]);
        
        $this->assertTrue($task->isDueSoon());
    }

    /** @test */
    public function task_is_not_due_soon_when_more_than_3_days()
    {
        $task = Task::factory()->create([
            'due_date' => now()->addDays(5),
            'status' => TaskStatus::NEW,
        ]);
        
        $this->assertFalse($task->isDueSoon());
    }

    /** @test */
    public function completed_task_is_not_due_soon()
    {
        $task = Task::factory()->create([
            'due_date' => now()->addDays(1),
            'status' => TaskStatus::COMPLETED,
        ]);
        
        $this->assertFalse($task->isDueSoon());
    }

    /** @test */
    public function urgent_priority_is_urgent()
    {
        $this->assertTrue(TaskPriority::URGENT->isUrgent());
    }

    /** @test */
    public function high_priority_is_not_urgent()
    {
        $this->assertFalse(TaskPriority::HIGH->isUrgent());
    }

    /** @test */
    public function completed_status_is_terminal()
    {
        $this->assertTrue(TaskStatus::COMPLETED->isTerminal());
    }

    /** @test */
    public function cancelled_status_is_terminal()
    {
        $this->assertTrue(TaskStatus::CANCELLED->isTerminal());
    }

    /** @test */
    public function in_progress_status_is_not_terminal()
    {
        $this->assertFalse(TaskStatus::IN_PROGRESS->isTerminal());
    }
}

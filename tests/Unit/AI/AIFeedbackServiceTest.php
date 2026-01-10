<?php

namespace Tests\Unit\AI;

use Tests\TestCase;
use App\Services\AI\AIFeedbackService;
use App\Models\AI\AIDecision;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AIFeedbackServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $feedbackService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->feedbackService = new AIFeedbackService();
    }

    /** @test */
    public function it_can_track_user_feedback()
    {
        $user = User::factory()->create();
        $decision = AIDecision::factory()->create([
            'confidence_score' => 0.85,
        ]);

        $this->actingAs($user);

        $this->feedbackService->recordFeedback(
            $decision,
            'accepted',
            'Task analysis was accurate'
        );

        $decision->refresh();
        $this->assertEquals('accepted', $decision->user_action);
        $this->assertEquals('Task analysis was accurate', $decision->user_feedback);
    }

    /** @test */
    public function it_calculates_accuracy_correctly()
    {
        // Create test decisions with known outcomes
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create 10 decisions: 7 accepted, 3 rejected
        for ($i = 0; $i < 7; $i++) {
            $decision = AIDecision::factory()->create();
            $this->feedbackService->recordFeedback($decision, 'accepted');
        }

        for ($i = 0; $i < 3; $i++) {
            $decision = AIDecision::factory()->create();
            $this->feedbackService->recordFeedback($decision, 'rejected');
        }

        $metrics = $this->feedbackService->getLearningMetrics();

        $this->assertIsArray($metrics);
        $this->assertArrayHasKey('acceptance_rate', $metrics);
        // 7 accepted out of 10 total = 70% acceptance rate
        $this->assertEquals(70, round($metrics['acceptance_rate']));
    }

    /** @test */
    public function it_updates_confidence_calibration()
    {
        $decision = AIDecision::factory()->create([
            'decision_type' => 'priority_adjustment',
            'confidence_score' => 0.9,
        ]);

        $user = User::factory()->create();
        $this->actingAs($user);

        // Accept high-confidence decision
        $this->feedbackService->recordFeedback($decision, 'accepted');

        $decision->refresh();
        $this->assertEquals('accepted', $decision->user_action);
        $this->assertNotNull($decision->reviewed_at);
    }
}

<?php

namespace App\Notifications;

use App\Models\AI\AIDecision;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GuardrailViolationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $decision;
    protected $violations;

    /**
     * Create a new notification instance.
     */
    public function __construct(AIDecision $decision, array $violations)
    {
        $this->decision = $decision;
        $this->violations = $violations;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $severity = $this->violations['highest_severity'] ?? 'unknown';
        
        return (new MailMessage)
                    ->subject("Guardrail Violation Detected - {$severity} severity")
                    ->line('An AI decision has violated one or more guardrail rules.')
                    ->line('Decision ID: #' . $this->decision->id)
                    ->line('Recommendation: ' . $this->decision->recommendation)
                    ->line('Violations: ' . $this->violations['total_violations'])
                    ->line('Highest Severity: ' . strtoupper($severity))
                    ->action('Review Decision', route('ai.decisions.show', $this->decision->id))
                    ->line('Please review and take appropriate action.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'guardrail_violation',
            'decision_id' => $this->decision->id,
            'decision_type' => $this->decision->decision_type,
            'recommendation' => $this->decision->recommendation,
            'total_violations' => $this->violations['total_violations'],
            'highest_severity' => $this->violations['highest_severity'],
            'violations' => $this->violations['violations'],
            'task_id' => $this->decision->task_id,
            'project_id' => $this->decision->project_id,
            'created_at' => now()->toIso8601String(),
        ];
    }
}

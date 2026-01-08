<?php

namespace App\Services\AI;

use App\Models\AI\AIDecision;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AIGuardrailService
{
    /**
     * Default guardrail rules
     */
    const RULES = [
        'no_data_deletion' => true,
        'no_critical_changes' => true,
        'no_mass_changes' => true,
        'no_unverified_actions' => true,
    ];

    /**
     * Default thresholds
     */
    const THRESHOLDS = [
        'mass_change_limit' => 5,           // Max items affected in one action
        'min_confidence_score' => 0.7,      // Minimum confidence for auto-execution
        'critical_actions' => [             // Actions requiring manual review
            'delete',
            'cancel',
            'archive',
            'reassign_project',
        ],
    ];

    /**
     * Check if decision passes all active guardrails
     */
    public function checkDecision(AIDecision $decision): array
    {
        $violations = [];
        $rules = $this->getActiveRules();

        // Rule 1: No data deletion
        if ($rules['no_data_deletion'] && $this->involvesDataDeletion($decision)) {
            $violations[] = [
                'rule' => 'no_data_deletion',
                'severity' => 'critical',
                'message' => 'Decision involves data deletion which is prohibited',
                'recommendation' => 'Mark for manual review instead of auto-execution',
            ];
        }

        // Rule 2: No critical changes
        if ($rules['no_critical_changes'] && $this->involvesCriticalChange($decision)) {
            $violations[] = [
                'rule' => 'no_critical_changes',
                'severity' => 'high',
                'message' => 'Decision involves critical system changes',
                'recommendation' => 'Require explicit human approval',
            ];
        }

        // Rule 3: No mass changes
        if ($rules['no_mass_changes'] && $this->involvesMassChanges($decision)) {
            $violations[] = [
                'rule' => 'no_mass_changes',
                'severity' => 'medium',
                'message' => 'Decision affects multiple items beyond threshold',
                'recommendation' => 'Split into smaller batches or require approval',
            ];
        }

        // Rule 4: No unverified actions (low confidence)
        if ($rules['no_unverified_actions'] && !$this->meetsConfidenceThreshold($decision)) {
            $violations[] = [
                'rule' => 'no_unverified_actions',
                'severity' => 'medium',
                'message' => 'Decision confidence below minimum threshold',
                'recommendation' => 'Increase confidence or mark for manual review',
            ];
        }

        return [
            'passed' => empty($violations),
            'violations' => $violations,
            'total_violations' => count($violations),
            'highest_severity' => $this->getHighestSeverity($violations),
        ];
    }

    /**
     * Check if decision involves data deletion
     */
    protected function involvesDataDeletion(AIDecision $decision): bool
    {
        $recommendation = strtolower($decision->recommendation);
        
        $deletionKeywords = [
            'delete',
            'remove',
            'cancel',
            'archive permanently',
            'drop',
        ];

        foreach ($deletionKeywords as $keyword) {
            if (str_contains($recommendation, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if decision involves critical changes
     */
    protected function involvesCriticalChange(AIDecision $decision): bool
    {
        $recommendation = strtolower($decision->recommendation);
        $criticalActions = $this->getThreshold('critical_actions');

        foreach ($criticalActions as $action) {
            if (str_contains($recommendation, $action)) {
                return true;
            }
        }

        // Check if affecting critical project/task
        if ($decision->project_id) {
            // Check if project is marked as critical (you can add this logic)
            // For now, consider any project with >10 tasks as critical
            $taskCount = \App\Models\Task::where('project_id', $decision->project_id)->count();
            if ($taskCount > 10) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if decision involves mass changes
     */
    protected function involvesMassChanges(AIDecision $decision): bool
    {
        $recommendation = strtolower($decision->recommendation);
        $limit = $this->getThreshold('mass_change_limit');

        // Check for mass action keywords
        $massKeywords = [
            'all tasks',
            'all projects',
            'multiple',
            'bulk',
            'batch',
        ];

        foreach ($massKeywords as $keyword) {
            if (str_contains($recommendation, $keyword)) {
                return true;
            }
        }

        // If decision relates to project with many tasks, consider it mass change
        if ($decision->project_id) {
            $taskCount = \App\Models\Task::where('project_id', $decision->project_id)->count();
            if ($taskCount > $limit) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if decision meets confidence threshold
     */
    protected function meetsConfidenceThreshold(AIDecision $decision): bool
    {
        $minConfidence = $this->getThreshold('min_confidence_score');
        return $decision->confidence_score >= $minConfidence;
    }

    /**
     * Get highest severity from violations
     */
    protected function getHighestSeverity(array $violations): ?string
    {
        if (empty($violations)) {
            return null;
        }

        $severityOrder = ['critical' => 3, 'high' => 2, 'medium' => 1, 'low' => 0];
        $highest = 'low';

        foreach ($violations as $violation) {
            if ($severityOrder[$violation['severity']] > $severityOrder[$highest]) {
                $highest = $violation['severity'];
            }
        }

        return $highest;
    }

    /**
     * Get active guardrail rules from settings
     */
    public function getActiveRules(): array
    {
        return Cache::remember('guardrail_rules', 3600, function () {
            // Try to get from database settings (if exists)
            // For now, return defaults
            return self::RULES;
        });
    }

    /**
     * Get specific threshold value
     */
    public function getThreshold(string $key)
    {
        $thresholds = Cache::remember('guardrail_thresholds', 3600, function () {
            return self::THRESHOLDS;
        });

        return $thresholds[$key] ?? null;
    }

    /**
     * Update guardrail rule
     */
    public function updateRule(string $rule, bool $enabled): bool
    {
        try {
            // In production, save to database
            // For now, just update cache
            $rules = $this->getActiveRules();
            $rules[$rule] = $enabled;
            
            Cache::put('guardrail_rules', $rules, 3600);

            Log::info("Guardrail rule updated: {$rule} = " . ($enabled ? 'enabled' : 'disabled'));
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to update guardrail rule {$rule}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update threshold value
     */
    public function updateThreshold(string $key, $value): bool
    {
        try {
            $thresholds = Cache::get('guardrail_thresholds', self::THRESHOLDS);
            $thresholds[$key] = $value;
            
            Cache::put('guardrail_thresholds', $thresholds, 3600);

            Log::info("Guardrail threshold updated: {$key} = {$value}");
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to update threshold {$key}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get guardrail statistics
     */
    public function getStatistics(): array
    {
        $totalDecisions = AIDecision::count();
        $violatedDecisions = AIDecision::where('guardrail_violations', '>', 0)->count();

        return [
            'total_checks' => $totalDecisions,
            'total_violations' => $violatedDecisions,
            'violation_rate' => $totalDecisions > 0 ? round(($violatedDecisions / $totalDecisions) * 100, 2) : 0,
            'active_rules' => array_filter($this->getActiveRules()),
            'rules_count' => count(array_filter($this->getActiveRules())),
        ];
    }

    /**
     * Clear guardrail cache
     */
    public function clearCache(): void
    {
        Cache::forget('guardrail_rules');
        Cache::forget('guardrail_thresholds');
        Log::info('Guardrail cache cleared');
    }
}

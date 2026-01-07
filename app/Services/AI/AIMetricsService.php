<?php

namespace App\Services\AI;

use App\Models\AI\AIDecision;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AIMetricsService
{
    /**
     * Get acceptance rate (percentage of accepted decisions)
     */
    public function getAcceptanceRate(int $days = 30): float
    {
        $total = AIDecision::where('created_at', '>=', now()->subDays($days))
            ->whereIn('user_action', ['accepted', 'rejected'])
            ->count();
            
        if ($total === 0) {
            return 0.0;
        }

        $accepted = AIDecision::where('created_at', '>=', now()->subDays($days))
            ->where('user_action', 'accepted')
            ->count();

        return round(($accepted / $total) * 100, 2);
    }

    /**
     * Get system health metrics
     */
    public function getSystemHealth(): array
    {
        return [
            'response_time' => Cache::get('ai_avg_response_time', 1500), // milliseconds
            'fallback_rate' => Cache::get('ai_fallback_rate', 0), // percentage
            'error_count_24h' => Cache::get('ai_errors_24h', 0),
            'last_decision' => AIDecision::latest()->first()?->created_at?->diffForHumans() ?? 'Never',
        ];
    }

    /**
     * Get average confidence score
     */
    public function getAverageConfidence(int $days = 30): float
    {
        return round(
            AIDecision::where('created_at', '>=', now()->subDays($days))
                ->avg('confidence_score') ?? 0,
            2
        );
    }

    /**
     * Get decision type distribution
     */
    public function getDecisionTypeDistribution(): array
    {
        return AIDecision::select('decision_type', DB::raw('count(*) as count'))
            ->groupBy('decision_type')
            ->pluck('count', 'decision_type')
            ->toArray();
    }

    /**
     * Get recent metrics summary
     */
    public function getSummary(): array
    {
        return [
            'total_decisions' => AIDecision::count(),
            'pending_decisions' => AIDecision::pending()->count(),
            'acceptance_rate' => $this->getAcceptanceRate(),
            'avg_confidence' => $this->getAverageConfidence(),
            'system_health' => $this->getSystemHealth(),
        ];
    }
}

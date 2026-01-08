<?php

namespace App\Http\Controllers\Admin\AI;

use App\Http\Controllers\Controller;
use App\Services\AI\AIAnalysisService;
use App\Services\AI\AIDataAggregator;
use App\Models\AI\AIDecision;
use Illuminate\Http\Request;

class AIInsightsController extends Controller
{
    protected $analysisService;
    protected $dataAggregator;

    public function __construct(
        AIAnalysisService $analysisService,
        AIDataAggregator $dataAggregator
    ) {
        $this->middleware(['auth', 'can:view-ai-analytics']);
        $this->analysisService = $analysisService;
        $this->dataAggregator = $dataAggregator;
    }

    /**
     * Display AI insights dashboard
     */
    public function index()
    {
        // Get comprehensive system insights
        $insights = $this->analysisService->getSystemInsights();
        
        // Get trend data for charts
        $trends = $this->analysisService->analyzeTrends();
        
        // Get recent decisions for timeline
        $recentDecisions = AIDecision::with(['task', 'project'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // Calculate key metrics
        $metrics = [
            'total_decisions' => AIDecision::count(),
            'pending_count' => AIDecision::where('user_action', 'pending')->count(),
            'acceptance_rate' => $this->calculateAcceptanceRate(),
            'avg_confidence' => AIDecision::avg('confidence_score'),
            'health_score' => $insights['health_score'] ?? 0,
        ];
        
        // Prepare chart data
        $chartData = $this->prepareChartData($trends);
        
        return view('admin.ai-insights.index', compact(
            'insights',
            'trends',
            'recentDecisions',
            'metrics',
            'chartData'
        ));
    }

    /**
     * Calculate acceptance rate
     */
    protected function calculateAcceptanceRate(): float
    {
        $total = AIDecision::whereIn('user_action', ['accepted', 'rejected'])->count();
        $accepted = AIDecision::where('user_action', 'accepted')->count();
        
        return $total > 0 ? round(($accepted / $total) * 100, 2) : 0;
    }

    /**
     * Prepare data for charts
     */
    protected function prepareChartData(array $trends): array
    {
        // Decision trend over time (last 7 days)
        $decisionTrend = AIDecision::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Confidence distribution
        $confidenceDistribution = [
            'high' => AIDecision::where('confidence_score', '>=', 0.8)->count(),
            'medium' => AIDecision::whereBetween('confidence_score', [0.6, 0.8])->count(),
            'low' => AIDecision::where('confidence_score', '<', 0.6)->count(),
        ];
        
        // Decision types breakdown
        $typeBreakdown = AIDecision::selectRaw('decision_type, COUNT(*) as count')
            ->groupBy('decision_type')
            ->get()
            ->pluck('count', 'decision_type')
            ->toArray();
        
        // User action distribution
        $actionDistribution = AIDecision::selectRaw('user_action, COUNT(*) as count')
            ->groupBy('user_action')
            ->get()
            ->pluck('count', 'user_action')
            ->toArray();
        
        return [
            'decision_trend' => [
                'labels' => $decisionTrend->pluck('date')->toArray(),
                'data' => $decisionTrend->pluck('count')->toArray(),
            ],
            'confidence_distribution' => $confidenceDistribution,
            'type_breakdown' => $typeBreakdown,
            'action_distribution' => $actionDistribution,
            'acceptance_trend' => $trends['acceptance_trend'] ?? [],
            'confidence_trend' => $trends['confidence_trend'] ?? [],
        ];
    }

    /**
     * Get performance metrics API endpoint
     */
    public function getPerformanceMetrics()
    {
        $metrics = $this->analysisService->getPerformanceMetrics();
        
        return response()->json([
            'success' => true,
            'metrics' => $metrics,
        ]);
    }

    /**
     * Get system health API endpoint
     */
    public function getSystemHealth()
    {
        $insights = $this->analysisService->getSystemInsights();
        
        return response()->json([
            'success' => true,
            'health_score' => $insights['health_score'],
            'status' => $insights['system_status'],
            'metrics' => $insights['metrics'],
        ]);
    }
}

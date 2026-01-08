<?php

namespace App\Http\Controllers\Admin\AI;

use App\Http\Controllers\Controller;
use App\Services\AI\AIAnalyticsEngine;
use Illuminate\Http\Request;

class AIAnalyticsController extends Controller
{
    protected $analyticsEngine;

    public function __construct(AIAnalyticsEngine $analyticsEngine)
    {
        $this->middleware(['auth', 'can:view-ai-insights']);
        $this->analyticsEngine = $analyticsEngine;
    }

    /**
     * Display analytics
     */
    public function index()
    {
        $analytics = $this->analyticsEngine->generateComprehensiveReport();

        return view('admin.ai-analytics.index', compact('analytics'));
    }

    /**
     * Get metrics data
     */
    public function metrics(Request $request)
    {
        $type = $request->input('type', 'summary');
        
        $data = match($type) {
            'summary' => $this->analyticsEngine->getSummaryMetrics(),
            'performance' => $this->analyticsEngine->generatePerformanceReport(),
            'accuracy' => $this->analyticsEngine->analyzeAccuracyTrend(30),
            default => [],
        };

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin\AI;

use App\Http\Controllers\Controller;
use App\Services\AI\AIAutomationService;
use Illuminate\Http\Request;

class AIWorkflowController extends Controller
{
    protected $automationService;

    public function __construct(AIAutomationService $automationService)
    {
        $this->middleware(['auth', 'can:manage-ai-settings']);
        $this->automationService = $automationService;
    }

    /**
     * Display workflows dashboard
     */
    public function index()
    {
        $activeRules = $this->automationService->getActiveRules();
        $workloadBalance = $this->automationService->balanceWorkload();
        
        // Fetch Scheduled Jobs
        $scheduledJobs = \App\Models\AI\AISchedule::orderBy('run_at', 'desc')->limit(10)->get();
        
        // Get Workload Threshold
        $threshold = (int) (\App\Models\AI\AISetting::where('key', 'workload_threshold')->value('value') ?? 5);

        return view('admin.ai-workflows.index', compact(
            'activeRules',
            'workloadBalance',
            'scheduledJobs',
            'threshold'
        ));
    }

    /**
     * Create automation rule
     */
    public function createRule(Request $request)
    {
        $request->validate([
            'trigger' => 'required|string',
            'conditions' => 'required|array',
            'action' => 'required|string',
        ]);

        try {
            $rule = $this->automationService->createAutomationRule([
                'trigger' => $request->trigger,
                'conditions' => $request->conditions,
                'action' => $request->action,
                'enabled' => $request->boolean('enabled', true),
            ]);

            return response()->json([
                'success' => true,
                'rule' => $rule,
                'message' => 'Automation rule created successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create automation rule',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Schedule analysis
     */
    public function scheduleAnalysis(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'params' => 'nullable|array',
            'run_at' => 'required|date|after:now',
        ]);

        try {
            $this->automationService->scheduleAnalysis(
                $request->type,
                $request->params ?? [],
                \Carbon\Carbon::parse($request->run_at)
            );

            return response()->json([
                'success' => true,
                'message' => 'Analysis scheduled successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to schedule analysis',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Run automation manually
     */
    public function runAutomation()
    {
        try {
            $results = $this->automationService->runAutomatedAnalysis();

            return response()->json([
                'success' => true,
                'results' => $results,
                'message' => 'Automation completed successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Automation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get workload balance recommendations
     */
    public function workloadBalance()
    {
        try {
            $recommendations = $this->automationService->balanceWorkload();

            return response()->json([
                'success' => true,
                'recommendations' => $recommendations,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze workload',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Update workload threshold
     */
    public function updateWorkloadThreshold(Request $request)
    {
        $request->validate([
            'threshold' => 'required|integer|min:1|max:50',
        ]);

        \App\Models\AI\AISetting::updateOrCreate(
            ['key' => 'workload_threshold'],
            [
                'value' => $request->threshold,
                'type' => 'integer',
                'group' => 'system',
                'description' => 'Max tasks per user before flag'
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Threshold updated successfully'
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin\AI;

use App\Http\Controllers\Controller;
use App\Services\AI\AIGuardrailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AIGuardrailController extends Controller
{
    protected $guardrailService;

    public function __construct(AIGuardrailService $guardrailService)
    {
        $this->middleware(['auth', 'can:manage-ai-settings']);
        $this->guardrailService = $guardrailService;
    }

    /**
     * Display guardrail settings
     */
    public function index()
    {
        $rules = $this->guardrailService->getActiveRules();
        $thresholds = [
            'mass_change_limit' => $this->guardrailService->getThreshold('mass_change_limit'),
            'min_confidence_score' => $this->guardrailService->getThreshold('min_confidence_score'),
            'critical_actions' => $this->guardrailService->getThreshold('critical_actions'),
        ];
        $statistics = $this->guardrailService->getStatistics();

        return view('admin.ai-guardrails.index', compact('rules', 'thresholds', 'statistics'));
    }

    /**
     * Update guardrail rule
     */
    public function updateRule(Request $request)
    {
        $request->validate([
            'rule' => 'required|string|in:no_data_deletion,no_critical_changes,no_mass_changes,no_unverified_actions',
            'enabled' => 'required|boolean',
        ]);

        $success = $this->guardrailService->updateRule($request->rule, $request->enabled);

        if ($success) {
            activity('ai')
                ->causedBy(auth()->user())
                ->log('guardrail_rule_updated: ' . $request->rule . ' = ' . ($request->enabled ? 'enabled' : 'disabled'));

            return response()->json([
                'success' => true,
                'message' => 'Guardrail rule updated successfully',
                'rule' => $request->rule,
                'enabled' => $request->enabled,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to update guardrail rule',
        ], 500);
    }

    /**
     * Update threshold value
     */
    public function updateThreshold(Request $request)
    {
        $request->validate([
            'key' => 'required|string|in:mass_change_limit,min_confidence_score',
            'value' => 'required|numeric|min:0',
        ]);

        // Validate specific thresholds
        if ($request->key === 'min_confidence_score' && ($request->value < 0 || $request->value > 1)) {
            return response()->json([
                'success' => false,
                'message' => 'Confidence score must be between 0 and 1',
            ], 422);
        }

        if ($request->key === 'mass_change_limit' && $request->value < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Mass change limit must be at least 1',
            ], 422);
        }

        $success = $this->guardrailService->updateThreshold($request->key, $request->value);

        if ($success) {
            activity('ai')
                ->causedBy(auth()->user())
                ->log('guardrail_threshold_updated: ' . $request->key . ' = ' . $request->value);

            return response()->json([
                'success' => true,
                'message' => 'Threshold updated successfully',
                'key' => $request->key,
                'value' => $request->value,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to update threshold',
        ], 500);
    }

    /**
     * Clear guardrail cache
     */
    public function clearCache()
    {
        $this->guardrailService->clearCache();

        activity('ai')
            ->causedBy(auth()->user())
            ->log('guardrail_cache_cleared');

        return redirect()->route('ai.guardrails.index')
            ->with('success', 'Guardrail cache cleared successfully');
    }
}

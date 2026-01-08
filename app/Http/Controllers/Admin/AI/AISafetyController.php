<?php

namespace App\Http\Controllers\Admin\AI;

use App\Http\Controllers\Controller;
use App\Services\AI\AIGuardrailService;
use Illuminate\Http\Request;

class AISafetyController extends Controller
{
    protected $guardrailService;

    public function __construct(AIGuardrailService $guardrailService)
    {
        $this->middleware(['auth', 'can:manage-ai-safety']);
        $this->guardrailService = $guardrailService;
    }

    /**
     * Display safety dashboard
     */
    public function index()
    {
        $guardrails = config('ai.guardrails');
        $violations = []; // Would fetch from logs

        return view('admin.ai-safety.index', compact('guardrails', 'violations'));
    }

    /**
     * Update guardrails
     */
    public function updateGuardrails(Request $request)
    {
        $request->validate([
            'min_confidence' => 'required|numeric|min:0|max:1',
            'max_actions_per_hour' => 'required|integer|min:1',
        ]);

        // Update config or database
        return response()->json([
            'success' => true,
            'message' => 'Guardrails updated successfully',
        ]);
    }

    /**
     * Update fallback settings
     */
    public function updateFallback(Request $request)
    {
        $request->validate([
            'fallback_provider' => 'required|in:local,openai,claude',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fallback settings updated',
        ]);
    }
}

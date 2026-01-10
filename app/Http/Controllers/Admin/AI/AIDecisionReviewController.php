<?php

namespace App\Http\Controllers\Admin\AI;

use App\Http\Controllers\Controller;
use App\Models\AI\AIDecision;
use App\Services\AI\AIDecisionEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AIDecisionReviewController extends Controller
{
    protected $decisionEngine;

    public function __construct(AIDecisionEngine $decisionEngine)
    {
        $this->decisionEngine = $decisionEngine;
    }

    /**
     * Display pending reviews
     */
    public function index()
    {
        $pending = AIDecision::where('user_action', 'pending')
            ->with(['task', 'project'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.ai-review.index', compact('pending'));
    }

    /**
     * Show decision for review
     */
    public function show(AIDecision $decision)
    {
        $decision->load(['task', 'project']);
        return view('admin.ai-decisions.show', compact('decision'));
    }

    /**
     * Accept a decision and execute it
     */
    public function accept(Request $request, AIDecision $decision)
    {
        try {
            // Update decision status
            $decision->update([
                'user_action' => 'accepted',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            // Execute the decision
            $executed = $this->decisionEngine->executeDecision($decision);

            // Log activity
            activity('ai')
                ->causedBy(auth()->user())
                ->performedOn($decision)
                ->withProperties(['action' => 'accepted', 'executed' => $executed])
                ->log('decision_accepted');

            $message = $executed 
                ? 'Decision accepted and executed successfully!'
                : 'Decision accepted but execution failed. Check logs for details.';

            return redirect()
                ->route('ai.decisions.show', $decision->id)
                ->with($executed ? 'success' : 'warning', $message);

        } catch (\Exception $e) {
            Log::error("Failed to accept decision #{$decision->id}: " . $e->getMessage());
            
            return back()->withErrors(['error' => 'Failed to accept decision: ' . $e->getMessage()]);
        }
    }

    /**
     * Reject a decision
     */
    public function reject(Request $request, AIDecision $decision)
    {
        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        try {
            // Update decision status
            $decision->update([
                'user_action' => 'rejected',
                'user_feedback' => $validated['rejection_reason'] ?? null,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            // Log activity
            activity('ai')
                ->causedBy(auth()->user())
                ->performedOn($decision)
                ->withProperties([
                    'action' => 'rejected',
                    'reason' => $validated['rejection_reason'] ?? null
                ])
                ->log('decision_rejected');

            return redirect()
                ->route('ai.decisions.index')
                ->with('success', 'Decision rejected successfully!');

        } catch (\Exception $e) {
            Log::error("Failed to reject decision #{$decision->id}: " . $e->getMessage());
            
            return back()->withErrors(['error' => 'Failed to reject decision.']);
        }
    }

    /**
     * Modify and execute decision
     */
    public function modify(Request $request, AIDecision $decision)
    {
        $validated = $request->validate([
            'modified_recommendation' => 'required|string|max:500',
        ]);

        try {
            // Update decision
            $decision->update([
                'user_action' => 'modified',
                'user_feedback' => $validated['modified_recommendation'],
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            // Execute modified decision
            $executed = $this->decisionEngine->executeDecision(
                $decision,
                $validated['modified_recommendation']
            );

            // Log activity
            activity('ai')
                ->causedBy(auth()->user())
                ->performedOn($decision)
                ->withProperties([
                    'action' => 'modified',
                    'modified_recommendation' => $validated['modified_recommendation'],
                    'executed' => $executed
                ])
                ->log('decision_modified');

            return redirect()
                ->route('ai.decisions.show', $decision->id)
                ->with('success', 'Decision modified and executed successfully!');

        } catch (\Exception $e) {
            Log::error("Failed to modify decision #{$decision->id}: " . $e->getMessage());
            
            return back()->withErrors(['error' => 'Failed to modify decision.']);
        }
    }

    /**
     * Bulk accept decisions
     */
    public function bulkAccept(Request $request)
    {
        $validated = $request->validate([
            'decision_ids' => 'required|array',
            'decision_ids.*' => 'exists:ai_decisions,id',
        ]);

        try {
            $accepted = 0;
            $failed = 0;

            foreach ($validated['decision_ids'] as $decisionId) {
                $decision = AIDecision::find($decisionId);
                
                if ($decision && $decision->user_action === 'pending') {
                    $decision->update([
                        'user_action' => 'accepted',
                        'reviewed_by' => auth()->id(),
                        'reviewed_at' => now(),
                    ]);
                    
                    if ($this->decisionEngine->executeDecision($decision)) {
                        $accepted++;
                    } else {
                        $failed++;
                    }
                }
            }

            $message = "Accepted {$accepted} decision(s)";
            if ($failed > 0) {
                $message .= " ({$failed} failed execution)";
            }

            return redirect()
                ->route('ai.decisions.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            Log::error("Bulk accept failed: " . $e->getMessage());
            
            return back()->withErrors(['error' => 'Bulk operation failed.']);
        }
    }
}


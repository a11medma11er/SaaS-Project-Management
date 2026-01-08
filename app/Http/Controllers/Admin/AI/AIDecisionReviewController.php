<?php

namespace App\Http\Controllers\Admin\AI;

use App\Http\Controllers\Controller;
use App\Models\AI\AIDecision;
use Illuminate\Http\Request;

class AIDecisionReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:manage-ai-decisions']);
    }

    /**
     * Display pending reviews
     */
    public function index()
    {
        $pending = AIDecision::where('user_action', null)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.ai-review.index', compact('pending'));
    }

    /**
     * Accept a decision
     */
    public function accept(Request $request, AIDecision $decision)
    {
        $decision->update([
            'user_action' => 'accepted',
            'user_feedback' => $request->input('feedback'),
            'reviewed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Decision accepted successfully');
    }

    /**
     * Reject a decision
     */
    public function reject(Request $request, AIDecision $decision)
    {
        $decision->update([
            'user_action' => 'rejected',
            'user_feedback' => $request->input('feedback'),
            'reviewed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Decision rejected');
    }

    /**
     * Mark as reviewed
     */
    public function review(Request $request, AIDecision $decision)
    {
        $request->validate([
            'action' => 'required|in:accepted,rejected',
            'feedback' => 'nullable|string|max:500',
        ]);

        $decision->update([
            'user_action' => $request->action,
            'user_feedback' => $request->feedback,
            'reviewed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Decision reviewed successfully',
        ]);
    }
}

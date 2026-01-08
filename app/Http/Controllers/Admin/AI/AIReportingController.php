<?php

namespace App\Http\Controllers\Admin\AI;

use App\Http\Controllers\Controller;
use App\Services\AI\AIAnalyticsEngine;
use App\Services\AI\AIReportingService;
use Illuminate\Http\Request;

class AIReportingController extends Controller
{
    protected $analyticsEngine;
    protected $reportingService;

    public function __construct(
        AIAnalyticsEngine $analyticsEngine,
        AIReportingService $reportingService
    ) {
        $this->middleware(['auth', 'can:view-ai-analytics']);
        $this->analyticsEngine = $analyticsEngine;
        $this->reportingService = $reportingService;
    }

    /**
     * Display reporting dashboard
     */
    public function index()
    {
        // Get report templates
        $templates = $this->reportingService->getTemplates();
        
        // Get decision types for filter
        $decisionTypes = \App\Models\AI\AIDecision::select('decision_type')
            ->distinct()
            ->pluck('decision_type');
        
        //  Get recent reports (if we store them)
        $recentReports = [];
        
        return view('admin.ai-reports.index', compact(
            'templates',
            'decisionTypes',
            'recentReports'
        ));
    }

    /**
     * Generate report
     */
    public function generate(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'template' => 'required|string',
            'decision_types' => 'nullable|array',
        ]);

        $filters = [
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'decision_types' => $request->decision_types,
        ];

        $report = $this->reportingService->generateReport(
            $request->template,
            $filters
        );

        return response()->json([
            'success' => true,
            'report' => $report,
        ]);
    }

    /**
     * Export report to PDF
     */
    public function exportPDF(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'decision_types' => 'nullable|array',
        ]);

        $filters = [
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'decision_types' => $request->decision_types,
        ];

        $report = $this->reportingService->generateReport('comprehensive', $filters);
        $url = $this->reportingService->exportToPDF($report);

        return response()->json([
            'success' => true,
            'url' => $url,
            'message' => 'Report generated successfully',
        ]);
    }

    /**
     * Export report to Excel
     */
    public function exportExcel(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'decision_types' => 'nullable|array',
        ]);

        $filters = [
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'decision_types' => $request->decision_types,
        ];

        $report = $this->reportingService->generateReport('comprehensive', $filters);
        $url = $this->reportingService->exportToExcel($report);

        return response()->json([
            'success' => true,
            'url' => $url,
            'message' => 'Report generated successfully',
        ]);
    }

    /**
     * Get comparison data (AI vs Human)
     */
    public function comparison(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $comparison = $this->analyticsEngine->compareAIvsHuman(
            $request->start_date,
            $request->end_date
        );

        return response()->json([
            'success' => true,
            'comparison' => $comparison,
        ]);
    }

    /**
     * Get decision type performance
     */
    public function decisionTypePerformance()
    {
        $performance = $this->analyticsEngine->getDecisionTypePerformance();

        return response()->json([
            'success' => true,
            'performance' => $performance,
        ]);
    }
}

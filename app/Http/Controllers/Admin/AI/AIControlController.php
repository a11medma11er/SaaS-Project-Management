<?php

namespace App\Http\Controllers\Admin\AI;

use App\Http\Controllers\Controller;
use App\Models\AI\AIDecision;
use App\Services\AI\AISettingsService;
use App\Services\AI\AIMetricsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AIControlController extends Controller
{
    protected $aiSettings;
    protected $aiMetrics;

    public function __construct(AISettingsService $settings, AIMetricsService $metrics)
    {
        $this->aiSettings = $settings;
        $this->aiMetrics = $metrics;
    }

    /**
     * Display AI control panel
     */
    public function index()
    {
        // Fetch settings grouped by category for Settings tab
        $settings = [
            'general' => \App\Models\AI\AISetting::where('group', 'general')->get(),
            'safety' => \App\Models\AI\AISetting::where('group', 'safety')->get(),
            'performance' => \App\Models\AI\AISetting::where('group', 'performance')->get(),
        ];

        $data = [
            'ai_enabled' => $this->aiSettings->get('ai_enabled', false),
            'ai_provider' => $this->aiSettings->get('ai_provider', config('ai.default_provider', 'local')),
            'total_decisions' => AIDecision::count(),
            'pending_decisions' => AIDecision::pending()->count(),
            'acceptance_rate' => $this->aiMetrics->getAcceptanceRate(),
            'avg_confidence' => $this->aiMetrics->getAverageConfidence(),
            'recent_activity' => AIDecision::with(['task', 'project', 'reviewedBy'])
                ->latest()
                ->take(10)
                ->get(),
            'system_health' => $this->aiMetrics->getSystemHealth(),
            'settings' => $settings, // Add settings data
        ];

        return view('admin.ai-control.index', $data);
    }

    /**
     * Toggle AI system on/off
     */
    public function toggle(Request $request)
    {
        $enabled = $request->boolean('enabled');
        
        $this->aiSettings->set('ai_enabled', $enabled, 'boolean', 'Master AI system toggle');
        
        // Log the toggle action
        activity('ai')
            ->causedBy(auth()->user())
            ->withProperties([
                'enabled' => $enabled,
                'ip' => $request->ip(),
            ])
            ->log('ai_system_toggled');

        Log::info('AI system ' . ($enabled ? 'enabled' : 'disabled') . ' by user ' . auth()->id());

        return response()->json([
            'success' => true,
            'message' => 'AI system ' . ($enabled ? 'enabled' : 'disabled') . ' successfully.',
            'enabled' => $enabled,
        ]);
    }

    /**
     * Get system health check
     */
    public function health()
    {
        $health = $this->aiMetrics->getSystemHealth();
        
        $isHealthy = $health['response_time'] < 3000 
                    && $health['fallback_rate'] < 10 
                    && $health['error_count_24h'] < 50;

        return response()->json([
            'status' => $isHealthy ? 'healthy' : 'degraded',
            'metrics' => $health,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Update AI settings
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'required',
            'settings.*.group' => 'required|string',
        ]);

        foreach ($validated['settings'] as $setting) {
            $this->aiSettings->set(
                $setting['key'],
                $setting['value'],
                null,
                null,
                $setting['group']
            );
        }

        // Log settings update
        activity('ai')
            ->causedBy(auth()->user())
            ->withProperties(['settings' => $validated['settings']])
            ->log('ai_settings_updated');

        return response()->json([
            'success' => true,
            'message' => 'AI settings updated successfully.',
        ]);
    }

    /**
     * Set AI provider
     */
    public function setProvider(Request $request)
    {
        $request->validate([
            'provider' => 'required|in:openai,gemini,openrouter,claude,local',
        ]);

        $provider = $request->input('provider');
        
        $this->aiSettings->set('ai_provider', $provider, 'string', 'Selected AI provider');
        
        // Log the provider change
        activity('ai')
            ->causedBy(auth()->user())
            ->withProperties([
                'provider' => $provider,
                'ip' => $request->ip(),
            ])
            ->log('ai_provider_changed');

        Log::info('AI provider changed to ' . $provider . ' by user ' . auth()->id());

        // Clear config cache to apply changes
        \Artisan::call('config:clear');

        return response()->json([
            'success' => true,
            'message' => 'AI provider changed to ' . $provider . ' successfully.',
            'provider' => $provider,
        ]);
    }
}

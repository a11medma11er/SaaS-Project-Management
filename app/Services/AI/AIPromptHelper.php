<?php

namespace App\Services\AI;

use App\Models\AI\AIPrompt;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AIPromptHelper
{
    /**
     * Get active system prompt by name with caching
     */
    public function getSystemPrompt(string $name): ?AIPrompt
    {
        return Cache::remember("system_prompt_{$name}", 3600, function () use ($name) {
            return AIPrompt::where('name', $name)
                ->where('is_system', true)
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->first();
        });
    }

    /**
     * Compile prompt template with variables
     */
    public function compilePrompt(string $name, array $variables): ?string
    {
        $prompt = $this->getSystemPrompt($name);

        if (!$prompt) {
            Log::warning("System prompt not found: {$name}");
            return null;
        }

        $template = $prompt->template;

        // Replace all {{variable}} with actual values
        foreach ($variables as $key => $value) {
            // Convert value to string, handle arrays and objects
            $stringValue = is_array($value) || is_object($value) 
                ? json_encode($value, JSON_PRETTY_PRINT) 
                : (string) $value;
            
            $template = str_replace("{{{$key}}}", $stringValue, $template);
        }

        // Increment usage count
        $prompt->increment('usage_count');

        return $template;
    }

    /**
     * Get compiled prompt with fallback
     */
    public function getCompiledPrompt(string $name, array $variables, ?string $fallback = null): string
    {
        $compiled = $this->compilePrompt($name, $variables);

        if ($compiled) {
            return $compiled;
        }

        if ($fallback) {
            Log::info("Using fallback for prompt: {$name}");
            return $fallback;
        }

        throw new \Exception("System prompt '{$name}' not found and no fallback provided");
    }

    /**
     * Check if system prompt exists
     */
    public function exists(string $name): bool
    {
        return $this->getSystemPrompt($name) !== null;
    }

    /**
     * Get all missing system prompts
     */
    public function getMissingSystemPrompts(array $requiredPrompts): array
    {
        $missing = [];
        
        foreach ($requiredPrompts as $promptName) {
            if (!$this->exists($promptName)) {
                $missing[] = $promptName;
            }
        }

        return $missing;
    }

    /**
     * Clear prompt cache
     */
    public function clearCache(string $name = null): void
    {
        if ($name) {
            Cache::forget("system_prompt_{$name}");
        } else {
            // Clear all system prompt caches
            $prompts = AIPrompt::where('is_system', true)->pluck('name');
            foreach ($prompts as $promptName) {
                Cache::forget("system_prompt_{$promptName}");
            }
        }
    }

    /**
     * Validate that all required system prompts exist
     */
    public function validateSystemPrompts(): array
    {
        $required = [
            'ai_feature_development_plan',
            'ai_feature_task_analysis',
            'ai_feature_feasibility_study',
            'ai_feature_project_breakdown',
            'ai_decision_priority_suggestion',
            'ai_decision_assignment_suggestion',
            'ai_decision_deadline_estimation',
            'ai_decision_risk_assessment',
            'ai_automation_workload_analysis',
            'ai_automation_task_redistribution',
            'ai_automation_overload_detection',
            'ai_automation_workflow_optimization',
            'ai_insights_performance_analysis',
            'ai_insights_bottleneck_detection',
            'ai_insights_trend_prediction',
        ];

        return $this->getMissingSystemPrompts($required);
    }
}

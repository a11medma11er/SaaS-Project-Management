<?php

namespace App\Services\AI\Providers;

use App\Contracts\AIProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * OpenRouter AI Provider
 * 
 * Uses OpenRouter API to access multiple AI models
 * Documentation: https://openrouter.ai/docs
 */
class OpenRouterProvider implements AIProvider
{
    private string $apiKey;
    private string $model;
    private string $baseUrl = 'https://openrouter.ai/api/v1';
    private string $siteUrl;
    private string $appName;

    public function __construct(
        string $apiKey, 
        string $model = 'openai/gpt-4',
        string $siteUrl = 'http://localhost',
        string $appName = 'Project Management AI'
    ) {
        $this->apiKey = $apiKey;
        $this->model = $model;
        $this->siteUrl = $siteUrl;
        $this->appName = $appName;
    }

    /**
     * Get AI suggestion based on context
     */
    public function getSuggestion(array $context): ?array
    {
        $type = $context['type'] ?? 'general';
        $data = $context['context'] ?? [];

        $systemPrompt = $this->getSystemPrompt($type);
        $userPrompt = $this->constructUserPrompt($type, $data);

        try {
            // Increase PHP execution time for slow AI responses
            set_time_limit(180);
            
            $response = Http::withHeaders([
                    'Authorization' => "Bearer {$this->apiKey}",
                    'HTTP-Referer' => $this->siteUrl,
                    'X-Title' => $this->appName,
                ])
                ->timeout(120)
                ->post("{$this->baseUrl}/chat/completions", [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt],
                    ],
                    'temperature' => 0.7,
                    'response_format' => ['type' => 'json_object'],
                ]);

            if ($response->failed()) {
                Log::error('OpenRouter API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $content = $response->json('choices.0.message.content');
            
            if (!$content) {
                return null;
            }

            return json_decode($content, true);

        } catch (\Exception $e) {
            Log::error('OpenRouter Request Failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Check if AI service is available
     */
    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Record user feedback
     */
    public function recordFeedback(string $suggestionId, bool $accepted, array $metadata = []): void
    {
        Log::info('AI Feedback', [
            'provider' => 'openrouter',
            'model' => $this->model,
            'id' => $suggestionId,
            'accepted' => $accepted,
            'metadata' => $metadata
        ]);
    }

    /**
     * Get AI model information
     */
    public function getModelInfo(): array
    {
        return [
            'provider' => 'OpenRouter',
            'model' => $this->model,
            'capabilities' => ['text', 'code', 'json', 'multi-model'],
        ];
    }

    /**
     * Get system prompt based on type
     */
    private function getSystemPrompt(string $type): string
    {
        $base = "You are an expert Project Management AI assistant. Your response MUST be valid JSON only, with no markdown formatting, no code blocks, just pure JSON.";
        
        return match ($type) {
            'development_plan' => "$base Return a development plan with these exact keys: overview (object with title, summary, estimated_duration in weeks, complexity string, confidence float), phases (array of objects with name, duration, tasks array, deliverables array), timeline (object with start_date, estimated_end_date, total_weeks, milestones array), resources (object), risks (array), recommendations (array). Make it comprehensive.",
            'project_breakdown' => "$base Return a task breakdown with total_estimated_tasks (number) and categories (object where each key is a category name like 'Planning', 'Design', etc. with properties: tasks array, estimated_duration string, priority string).",
            'task_analysis' => "$base Analyze the task and return JSON with: task (string), estimated_effort (string), complexity (object with level, score, factors array), dependencies (array), recommendations (array).",
            'feasibility_study' => "$base Generate a comprehensive feasibility study with keys: analysis (object with executive_summary, technical_feasibility, financial_feasibility, operational_feasibility, schedule_feasibility, legal_feasibility, recommendations array, conclusion).",
            'technical_study' => "$base Generate a technical study with keys: analysis (object with technology_stack array, architecture_overview, scalability_analysis, security_considerations, performance_requirements, technical_risks array, recommendations array).",
            'risk_study' => "$base Generate a risk assessment with keys: analysis (object with identified_risks array with (risk, severity, probability, impact, mitigation), risk_matrix, contingency_plans array, monitoring_strategy).",
            default => "$base Provide helpful suggestions in valid JSON format.",
        };
    }

    /**
     * Construct user prompt from data
     */
    private function constructUserPrompt(string $type, array $data): string
    {
        return "Analyze the following project management context and provide your response as valid JSON:\n\n" . 
               json_encode($data, JSON_PRETTY_PRINT);
    }
}

<?php

namespace App\Services\AI\Providers;

use App\Contracts\AIProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIProvider implements AIProvider
{
    private string $apiKey;
    private string $model;
    private string $baseUrl = 'https://api.openai.com/v1';

    public function __construct(string $apiKey, string $model = 'gpt-4')
    {
        $this->apiKey = $apiKey;
        $this->model = $model;
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
            $response = Http::withToken($this->apiKey)
                ->timeout(60)
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
                Log::error('OpenAI API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $content = $response->json('choices.0.message.content');
            return json_decode($content, true);

        } catch (\Exception $e) {
            Log::error('OpenAI Request Failed', ['error' => $e->getMessage()]);
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
        // OpenAI doesn't have a direct feedback loop via API in this manner,
        // but we could log it or fine-tune later.
        // For now, we just log locally as the interface requires implementation.
        Log::info('AI Feedback', [
            'provider' => 'openai',
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
            'provider' => 'OpenAI',
            'model' => $this->model,
            'capabilities' => ['text', 'code', 'json'],
        ];
    }

    private function getSystemPrompt(string $type): string
    {
        $base = "You are an expert Project Management AI assistant. Response MUST be valid JSON.";
        
        return match ($type) {
            'development_plan' => "$base Return a development plan with keys: overview, phases, timeline, resources, risks, recommendations. Phases must be an array of objects.",
            'project_breakdown' => "$base Return a task breakdown with categories (Planning, Design, etc.) and estimated tasks.",
            'task_analysis' => "$base Analyze the task complexity, effort, and provide recommendations.",
            default => "$base Provide helpful suggestions.",
        };
    }

    private function constructUserPrompt(string $type, array $data): string
    {
        return json_encode($data);
    }
}

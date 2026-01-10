<?php

namespace App\Services\AI\Providers;

use App\Contracts\AIProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Google Gemini AI Provider
 * 
 * Uses Google's Gemini API for AI suggestions
 * Documentation: https://ai.google.dev/docs
 */
class GeminiProvider implements AIProvider
{
    private string $apiKey;
    private string $model;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1';

    public function __construct(string $apiKey, string $model = 'gemini-pro')
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

        $prompt = $this->constructPrompt($type, $data);

        try {
            $response = Http::timeout(60)
                ->post("{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'topK' => 40,
                        'topP' => 0.95,
                        'maxOutputTokens' => 2048,
                    ],
                    'safetySettings' => [
                        [
                            'category' => 'HARM_CATEGORY_HARASSMENT',
                            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_HATE_SPEECH',
                            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                        ],
                    ],
                ]);

            if ($response->failed()) {
                Log::error('Gemini API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $result = $response->json();
            
            // Extract text from Gemini response
            $content = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
            
            if (!$content) {
                return null;
            }

            // Parse JSON from response
            // Gemini sometimes wraps JSON in markdown code blocks
            $content = preg_replace('/```json\s*|\s*```/', '', $content);
            $content = trim($content);
            
            return json_decode($content, true);

        } catch (\Exception $e) {
            Log::error('Gemini Request Failed', ['error' => $e->getMessage()]);
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
            'provider' => 'gemini',
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
            'provider' => 'Google Gemini',
            'model' => $this->model,
            'capabilities' => ['text', 'json', 'multilingual'],
        ];
    }

    /**
     * Construct prompt based on type and data
     */
    private function constructPrompt(string $type, array $data): string
    {
        $systemInstructions = "You are an expert Project Management AI assistant. You must respond with ONLY valid JSON, no markdown, no explanations, just pure JSON.";
        
        $specificInstructions = match ($type) {
            'development_plan' => "Generate a development plan with these exact keys: overview (with title, summary, estimated_duration, complexity, confidence), phases (array of objects with name, duration, tasks, deliverables), timeline, resources, risks, recommendations. Make it comprehensive and professional.",
            'project_breakdown' => "Generate a task breakdown with categories (Planning, Design, Development, Testing, Deployment) and estimated tasks for each category.",
            'task_analysis' => "Analyze the task complexity, effort estimate, dependencies, and provide actionable recommendations.",
            'feasibility_study' => "Generate a comprehensive feasibility study with analysis containing: executive_summary, technical_feasibility, financial_feasibility, operational_feasibility, schedule_feasibility, legal_feasibility, recommendations array, conclusion.",
            'technical_study' => "Generate a technical study with analysis containing: technology_stack array, architecture_overview, scalability_analysis, security_considerations, performance_requirements, technical_risks array, recommendations array.",
            'risk_study' => "Generate a risk assessment with analysis containing: identified_risks array (each with risk, severity, probability, impact, mitigation), risk_matrix, contingency_plans array, monitoring_strategy.",
            default => "Provide helpful project management suggestions in JSON format.",
        };

        $dataJson = json_encode($data, JSON_PRETTY_PRINT);

        return "{$systemInstructions}\n\n{$specificInstructions}\n\nContext:\n{$dataJson}\n\nRespond with valid JSON only:";
    }
}

<?php

namespace App\Services\AI;

use App\Contracts\AIProvider;
use Illuminate\Support\Facades\Log;

/**
 * AI Gateway Service
 * 
 * Acts as a boundary between business logic and AI services.
 * Ensures the system works perfectly WITHOUT AI.
 */
class AIGateway
{
    private ?AIProvider $provider = null;
    private AIPromptHelper $promptHelper;
    
    public function __construct(AIPromptHelper $promptHelper)
    {
        $this->promptHelper = $promptHelper;
    }
    
    /**
     * Set AI provider (optional)
     */
    public function setProvider(?AIProvider $provider): void
    {
        $this->provider = $provider;
    }
    
    /**
     * Get AI suggestion with graceful degradation
     * 
     * @param string $promptName System prompt name (e.g., 'ai_feature_development_plan')
     * @param array $variables Variables to compile into the prompt template
     * @return array|null Returns null if AI unavailable or fails
     */
    public function suggest(string $promptName, array $variables = []): ?array
    {
        try {
            // Get compiled prompt from database
            $compiledPrompt = $this->promptHelper->compilePrompt($promptName, $variables);
            
            if (!$compiledPrompt) {
                Log::warning("System prompt '{$promptName}' not found", [
                    'prompt' => $promptName,
                    'variables_count' => count($variables),
                ]);
                return null;
            }

            // If AI is disabled or not configured, return null gracefully
            if (!$this->provider || !$this->provider->isAvailable()) {
                Log::info('AI suggestion skipped - provider not available', [
                    'prompt' => $promptName,
                ]);
                return null;
            }
            
            // Send to AI provider
            $suggestion = $this->provider->getSuggestion([
                'prompt' => $compiledPrompt,
                'type' => $promptName,
                'context' => $variables,
            ]);
            
            if ($suggestion) {
                Log::info('AI suggestion generated successfully', [
                    'prompt' => $promptName,
                    'suggestion_id' => $suggestion['id'] ?? 'unknown',
                ]);
            }
            
            return $suggestion;
            
        } catch (\Exception $e) {
            // AI failure should NEVER break the system
            Log::warning('AI suggestion failed - gracefully degrading', [
                'prompt' => $promptName,
                'error' => $e->getMessage(),
            ]);
            
            return null;
        }
    }
    
    /**
     * Record user feedback on AI suggestion
     */
    public function recordFeedback(string $suggestionId, bool $accepted, array $metadata = []): void
    {
        if (!$this->provider) {
            return;
        }
        
        try {
            $this->provider->recordFeedback($suggestionId, $accepted, $metadata);
            
            Log::info('AI feedback recorded', [
                'suggestion_id' => $suggestionId,
                'accepted' => $accepted,
            ]);
            
        } catch (\Exception $e) {
            Log::warning('AI feedback recording failed', [
                'suggestion_id' => $suggestionId,
                'error' => $e->getMessage(),
            ]);
        }
    }
    
    /**
     * Check if AI is available
     */
    public function isAvailable(): bool
    {
        return $this->provider && $this->provider->isAvailable();
    }
    
    /**
     * Get AI model information
     */
    public function getModelInfo(): ?array
    {
        if (!$this->provider) {
            return null;
        }
        
        try {
            return $this->provider->getModelInfo();
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Get AI status for debugging
     */
    public function getStatus(): array
    {
        return [
            'enabled' => config('ai.enabled', false),
            'provider_configured' => $this->provider !== null,
            'available' => $this->isAvailable(),
            'model_info' => $this->getModelInfo(),
        ];
    }
}

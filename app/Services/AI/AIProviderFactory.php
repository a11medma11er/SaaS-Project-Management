<?php

namespace App\Services\AI;

use App\Contracts\AIProvider;
use App\Services\AI\Providers\OpenAIProvider;
use App\Services\AI\Providers\GeminiProvider;
use App\Services\AI\Providers\OpenRouterProvider;
use Illuminate\Support\Facades\Log;

/**
 * AI Provider Factory
 * 
 * Creates the appropriate AI provider based on configuration
 */
class AIProviderFactory
{
    /**
     * Create AI provider based on configuration
     */
    public static function create(): ?AIProvider
    {
        // Check if AI is enabled
        if (!config('ai.enabled', false)) {
            Log::info('AI system is disabled');
            return null;
        }

        // Get provider from settings (database) first, then fall back to config
        $settingsService = app(\App\Services\AI\AISettingsService::class);
        $provider = $settingsService->get('ai_provider', config('ai.default_provider', 'local'));

        try {
            return match($provider) {
                'openai' => self::createOpenAI(),
                'gemini' => self::createGemini(),
                'openrouter' => self::createOpenRouter(),
                'claude' => self::createClaude(),
                default => null,
            };
        } catch (\Exception $e) {
            Log::error('Failed to create AI provider', [
                'provider' => $provider,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Create OpenAI provider
     */
    private static function createOpenAI(): ?OpenAIProvider
    {
        $apiKey = config('ai.openai.api_key');
        $model = config('ai.openai.model', 'gpt-4');

        if (empty($apiKey)) {
            Log::warning('OpenAI API key not configured');
            return null;
        }

        return new OpenAIProvider($apiKey, $model);
    }

    /**
     * Create Google Gemini provider
     */
    private static function createGemini(): ?GeminiProvider
    {
        $apiKey = config('ai.gemini.api_key');
        $model = config('ai.gemini.model', 'gemini-pro');

        if (empty($apiKey)) {
            Log::warning('Gemini API key not configured');
            return null;
        }

        return new GeminiProvider($apiKey, $model);
    }

    /**
     * Create OpenRouter provider
     */
    private static function createOpenRouter(): ?OpenRouterProvider
    {
        $apiKey = config('ai.openrouter.api_key');
        $model = config('ai.openrouter.model', 'openai/gpt-4');
        $siteUrl = config('ai.openrouter.site_url', config('app.url'));
        $appName = config('ai.openrouter.app_name', config('app.name'));

        if (empty($apiKey)) {
            Log::warning('OpenRouter API key not configured');
            return null;
        }

        return new OpenRouterProvider($apiKey, $model, $siteUrl, $appName);
    }

    /**
     * Create Claude provider (placeholder for future implementation)
     */
    private static function createClaude(): ?AIProvider
    {
        Log::info('Claude provider not yet implemented');
        return null;
    }
}

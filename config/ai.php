<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI System Configuration
    |--------------------------------------------------------------------------
    |
    | Configure AI providers and settings for the application.
    |
    */

    'enabled' => env('AI_SYSTEM_ENABLED', false),

    'default_provider' => env('AI_DEFAULT_PROVIDER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | OpenAI Configuration
    |--------------------------------------------------------------------------
    */
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4'),
        'timeout' => env('OPENAI_TIMEOUT', 60),
        'max_tokens' => env('OPENAI_MAX_TOKENS', 2048),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Gemini Configuration
    |--------------------------------------------------------------------------
    */
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-pro'),
        'timeout' => env('GEMINI_TIMEOUT', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenRouter Configuration
    |--------------------------------------------------------------------------
    */
    'openrouter' => [
        'api_key' => env('OPENROUTER_API_KEY'),
        'model' => env('OPENROUTER_MODEL', 'openai/gpt-4'),
        'site_url' => env('OPENROUTER_SITE_URL', env('APP_URL')),
        'app_name' => env('OPENROUTER_APP_NAME', env('APP_NAME')),
        'timeout' => env('OPENROUTER_TIMEOUT', 120),
    ],

    /*
    |--------------------------------------------------------------------------
    | Claude/Anthropic Configuration
    |--------------------------------------------------------------------------
    */
    'claude' => [
        'api_key' => env('CLAUDE_API_KEY'),
        'model' => env('CLAUDE_MODEL', 'claude-3-sonnet-20240229'),
        'timeout' => env('CLAUDE_TIMEOUT', 60),
        'max_tokens' => env('CLAUDE_MAX_TOKENS', 2048),
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Guardrails
    |--------------------------------------------------------------------------
    */
    'min_confidence' => env('AI_MIN_CONFIDENCE', 0.7),
    'max_actions_per_hour' => env('AI_MAX_ACTIONS_PER_HOUR', 100),
    'require_approval_below' => env('AI_REQUIRE_APPROVAL_BELOW', 0.8),

    /*
    |--------------------------------------------------------------------------
    | Performance & Caching
    |--------------------------------------------------------------------------
    */
    'cache_ttl' => env('AI_CACHE_TTL', 3600),
    'cache_driver' => env('AI_CACHE_DRIVER', 'file'),
    'enable_query_cache' => env('AI_ENABLE_QUERY_CACHE', true),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    'rate_limit' => env('AI_RATE_LIMIT', 60),
    'rate_limit_window' => env('AI_RATE_LIMIT_WINDOW', 60),

    /*
    |--------------------------------------------------------------------------
    | Automation
    |--------------------------------------------------------------------------
    */
    'auto_execute_enabled' => env('AI_AUTO_EXECUTE_ENABLED', false),
    'auto_execute_min_confidence' => env('AI_AUTO_EXECUTE_MIN_CONFIDENCE', 0.95),

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    */
    'log_channel' => env('AI_LOG_CHANNEL', 'daily'),
    'log_level' => env('AI_LOG_LEVEL', 'info'),
    'log_decisions' => env('AI_LOG_DECISIONS', true),

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    */
    'enable_learning' => env('AI_ENABLE_LEARNING', true),
    'enable_analytics' => env('AI_ENABLE_ANALYTICS', true),
    'enable_automation' => env('AI_ENABLE_AUTOMATION', true),
    'enable_external_providers' => env('AI_ENABLE_EXTERNAL_PROVIDERS', false),
];

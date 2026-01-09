<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\App\Services\AI\AIGateway::class, function ($app) {
            $gateway = new \App\Services\AI\AIGateway();
            
            if (config('ai.enabled')) {
                // Determine provider based on config
                $provider = config('ai.provider', 'openai');
                
                if ($provider === 'openai' && config('ai.openai.api_key')) {
                    $gateway->setProvider(new \App\Services\AI\Providers\OpenAIProvider(
                        config('ai.openai.api_key'),
                        config('ai.openai.model', 'gpt-4')
                    ));
                }
            }
            
            return $gateway;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Illuminate\Pagination\Paginator::useBootstrapFive();
        Schema::defaultStringLength(191);
    }
}

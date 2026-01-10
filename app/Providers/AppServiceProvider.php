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
        // Register AIGateway as singleton with automatic provider selection
        $this->app->singleton(\App\Services\AI\AIGateway::class, function ($app) {
            $gateway = new \App\Services\AI\AIGateway();
            
            // Use factory to create provider based on configuration
            $provider = \App\Services\AI\AIProviderFactory::create();
            
            if ($provider) {
                $gateway->setProvider($provider);
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

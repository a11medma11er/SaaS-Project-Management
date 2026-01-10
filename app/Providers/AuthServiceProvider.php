<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // AI Permissions - Use Spatie's permission system
        // Admin users automatically have all permissions
        Gate::define('access-ai-control', function ($user) {
            return $user->hasRole('admin') || $user->hasPermissionTo('access-ai-control');
        });

        Gate::define('manage-ai-settings', function ($user) {
            return $user->hasRole('admin') || $user->hasPermissionTo('manage-ai-settings');
        });

        Gate::define('manage-ai-prompts', function ($user) {
            return $user->hasRole('admin') || $user->hasPermissionTo('manage-ai-prompts');
        });

        Gate::define('test-ai-prompts', function ($user) {
            return $user->hasRole('admin') || $user->hasPermissionTo('test-ai-prompts');
        });

        Gate::define('view-ai-decisions', function ($user) {
            return $user->hasRole('admin') || $user->hasPermissionTo('view-ai-decisions');
        });

        Gate::define('approve-ai-actions', function ($user) {
            return $user->hasRole('admin') || $user->hasPermissionTo('approve-ai-actions');
        });

        Gate::define('view-ai-analytics', function ($user) {
            return $user->hasRole('admin') || $user->hasPermissionTo('view-ai-analytics');
        });

        Gate::define('manage-ai-safety', function ($user) {
            return $user->hasRole('admin') || $user->hasPermissionTo('manage-ai-safety');
        });
    }
}

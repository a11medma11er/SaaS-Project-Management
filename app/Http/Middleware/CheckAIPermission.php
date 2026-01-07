<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAIPermission
{
    /**
     * Handle an incoming request.
     * Checks if authenticated user has the required AI permission
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission  Required permission name
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            abort(401, 'Unauthorized. Please log in.');
        }

        // Check if user has the required permission
        if (!auth()->user()->can($permission)) {
            // Log unauthorized attempt
            activity()
                ->causedBy(auth()->user())
                ->withProperties([
                    'permission' => $permission,
                    'ip' => $request->ip(),
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                ])
                ->log('ai_permission_denied');

            // Return 403 Forbidden
            abort(403, "Unauthorized AI action. Required permission: {$permission}");
        }

        // Log successful AI access
        activity('ai')
            ->causedBy(auth()->user())
            ->withProperties([
                'permission' => $permission,
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
            ])
            ->log('ai_access_granted');

        return $next($request);
    }
}

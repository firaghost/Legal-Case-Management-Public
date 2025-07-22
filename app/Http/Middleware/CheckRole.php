<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // If no roles are specified, deny access
        if (empty($roles)) {
            abort(403, 'No roles specified for this route.');
        }

        // Allow access if user is authenticated and has any of the required roles
        if (!Auth::check() || !$this->userHasAnyRole($roles)) {
            return $this->handleUnauthorized($request);
        }

        return $next($request);
    }

    /**
     * Check if the authenticated user has any of the specified roles.
     */
    protected function userHasAnyRole(array $roles): bool
    {
        $user = Auth::user();
        
        // Admin has access to everything
        if ($user->isAdmin()) {
            return true;
        }

        // Check if user has any of the required roles
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Handle unauthorized access attempt.
     */
    protected function handleUnauthorized(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'You do not have permission to access this resource.',
            ], 403);
        }

        abort(403, 'You do not have permission to access this page.');
    }
}







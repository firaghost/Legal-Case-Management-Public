<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$permissions
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        // If no permissions are specified, deny access
        if (empty($permissions)) {
            abort(403, 'No permissions specified for this route.');
        }

        // Allow access if user is authenticated and has any of the required permissions
        if (!Auth::check() || !$this->userHasAnyPermission($permissions)) {
            return $this->handleUnauthorized($request);
        }

        return $next($request);
    }

    /**
     * Check if the authenticated user has any of the specified permissions.
     */
    protected function userHasAnyPermission(array $permissions): bool
    {
        $user = Auth::user();
        
        // Admin has all permissions
        if ($user->isAdmin()) {
            return true;
        }

        // Check if user has any of the required permissions
        foreach ($permissions as $permission) {
            if ($user->hasPermissionTo($permission)) {
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
                'message' => 'You do not have permission to perform this action.',
            ], 403);
        }

        abort(403, 'You do not have permission to access this page.');
    }
}







<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequirePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip middleware for guests
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Skip if user doesn't need to change password
        if (!$user->password_change_required) {
            return $next($request);
        }

        // Allow access to password change routes and logout
        $allowedRoutes = [
            'password.change',
            'password.update',
            'logout',
            'profile.destroy'
        ];

        if (in_array($request->route()->getName(), $allowedRoutes)) {
            return $next($request);
        }

        // Redirect to password change page
        return redirect()->route('password.change')
            ->with('warning', 'You must change your password before continuing.');
    }
}







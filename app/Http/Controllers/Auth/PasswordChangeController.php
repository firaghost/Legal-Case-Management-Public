<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PasswordChangeController extends Controller
{
    /**
     * Show the password change form.
     */
    public function show(): View
    {
        return view('auth.change-password');
    }

    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();
        
        // Update the password and clear the password change requirement
        $user->update([
            'password' => Hash::make($validated['password']),
            'password_change_required' => false,
        ]);

        // Determine the appropriate dashboard based on user role
        $dashboardRoute = $this->getDashboardRouteForUser($user);

        return redirect()->intended($dashboardRoute)
            ->with('status', 'Password updated successfully! Welcome to the system.');
    }

    /**
     * Get the appropriate dashboard route based on user role.
     */
    private function getDashboardRouteForUser($user): string
    {
        // Check user's role and return appropriate dashboard route
        if ($user->role === 'admin' || $user->hasRole('admin')) {
            return route('admin.dashboard');
        } elseif ($user->role === 'supervisor' || $user->hasRole('supervisor')) {
            return route('supervisor.dashboard');
        } elseif ($user->role === 'lawyer' || $user->hasRole('lawyer')) {
            return route('lawyer.dashboard');
        }

        // Default fallback - redirect to lawyer dashboard if role is unclear
        return route('lawyer.dashboard');
    }
}







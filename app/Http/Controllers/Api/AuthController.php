<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Login user and create token
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check if user is active
        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated.'],
            ]);
        }

        // Create token with abilities based on user role
        $abilities = $this->getAbilitiesForRole($user->role);
        $token = $user->createToken('api-token', $abilities)->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user->only(['id', 'name', 'email', 'role']),
            'token' => $token,
            'abilities' => $abilities,
        ]);
    }

    /**
     * Logout user (revoke token)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful'
        ]);
    }

    /**
     * Get current user information
     */
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()->only(['id', 'name', 'email', 'role', 'branch_id', 'work_unit_id']),
        ]);
    }

    /**
     * Refresh token
     */
    public function refresh(Request $request)
    {
        $user = $request->user();
        
        // Revoke current token
        $request->user()->currentAccessToken()->delete();
        
        // Create new token
        $abilities = $this->getAbilitiesForRole($user->role);
        $token = $user->createToken('api-token', $abilities)->plainTextToken;

        return response()->json([
            'message' => 'Token refreshed',
            'token' => $token,
            'abilities' => $abilities,
        ]);
    }

    /**
     * Get abilities based on user role
     */
    private function getAbilitiesForRole(string $role): array
    {
        $abilities = [
            'Admin' => [
                'cases:view-all',
                'cases:create',
                'cases:update',
                'cases:delete',
                'users:view-all',
                'users:create',
                'users:update',
                'users:delete',
                'branches:view-all',
                'branches:create',
                'branches:update',
                'branches:delete',
                'documents:view-all',
                'documents:create',
                'documents:update',
                'documents:delete',
            ],
            'Supervisor' => [
                'cases:view-branch',
                'cases:update',
                'cases:approve',
                'users:view-branch',
                'users:update',
                'documents:view-branch',
                'documents:create',
                'documents:update',
            ],
            'Lawyer' => [
                'cases:view-own',
                'cases:create',
                'cases:update-own',
                'documents:view-own',
                'documents:create',
                'documents:update-own',
            ],
        ];

        return $abilities[$role] ?? [];
    }
}







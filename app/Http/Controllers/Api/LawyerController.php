<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

class LawyerController extends Controller
{
    /**
     * Return list of active lawyers (id & name) for dropdowns.
     */
    public function index()
    {
        try {
            \Log::info('LawyerController@index called');
            
            // First, let's get all users to debug
            $allUsers = User::get(['id', 'name', 'role']);
            \Log::info('All users found', ['users' => $allUsers->toArray()]);
            
            // Simple approach - get all users first
            $lawyers = User::where('role', 'lawyer')
                ->orWhere('role', 'Lawyer')
                ->orderBy('name')
                ->get(['id', 'name', 'email']);
            
            \Log::info('Lawyers found by role column', ['count' => $lawyers->count(), 'lawyers' => $lawyers->toArray()]);
            
            // If no lawyers found by role column, try roles relationship
            if ($lawyers->isEmpty()) {
                $lawyers = User::whereHas('roles', function ($q) {
                        $q->where('name', 'lawyer')
                          ->orWhere('name', 'Lawyer');
                    })
                    ->orderBy('name')
                    ->get(['id', 'name', 'email']);
                \Log::info('Lawyers found by roles relationship', ['count' => $lawyers->count(), 'lawyers' => $lawyers->toArray()]);
            }
            
            // If still no lawyers, get all users for testing
            if ($lawyers->isEmpty()) {
                \Log::warning('No lawyers found, returning all users for testing');
                $lawyers = User::orderBy('name')->get(['id', 'name', 'email']);
            }
            
            \Log::info('Final lawyers result', ['count' => $lawyers->count()]);
            
            return response()->json($lawyers);
        } catch (\Throwable $e) {
            \Log::error('LawyerController@index error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'error' => 'Failed to load lawyers',
                'message' => $e->getMessage(),
                'debug' => app()->environment('local') ? $e->getTraceAsString() : null
            ], 500);
        }
    }
}







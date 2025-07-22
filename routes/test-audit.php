<?php

use Illuminate\Support\Facades\Route;
use App\Models\AuditLog;
use App\Models\User;

Route::get('/test/audit-logs', function() {
    // Check if user is authenticated
    if (!auth()->check()) {
        return response()->json(['error' => 'Not authenticated'], 401);
    }

    // Check if user has admin role
    if (!auth()->user()->hasRole(['admin', 'super-admin'])) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    // Get all audit logs with user relationship
    $logs = AuditLog::with('user')
                   ->latest()
                   ->take(10) // Limit to 10 for testing
                   ->get();

    // Get total count
    $total = AuditLog::count();

    return response()->json([
        'total_logs' => $total,
        'logs' => $logs->map(function($log) {
            return [
                'id' => $log->id,
                'action' => $log->action,
                'user' => $log->user ? [
                    'id' => $log->user->id,
                    'name' => $log->user->name,
                    'email' => $log->user->email
                ] : null,
                'auditable_type' => $log->auditable_type,
                'auditable_id' => $log->auditable_id,
                'ip_address' => $log->ip_address,
                'user_agent' => $log->user_agent,
                'created_at' => $log->created_at,
                'changes' => $log->changes
            ];
        })
    ]);
})->middleware(['auth', 'verified']);







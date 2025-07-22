<?php

use Illuminate\Support\Facades\Route;
use App\Models\AuditLog;

Route::get('/test/audit-logs', function() {
    // Get all audit logs with user relationship
    $logs = AuditLog::with('user')->latest()->get();
    
    return response()->json([
        'count' => $logs->count(),
        'logs' => $logs->toArray()
    ]);
});







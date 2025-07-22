<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestAuditLogController extends Controller
{
    public function createTestLog()
    {
        try {
            $log = AuditLog::create([
                'user_id' => Auth::id(),
                'auditable_type' => 'test',
                'auditable_id' => 1,
                'action' => 'TEST_LOG_ENTRY',
                'changes' => json_encode(['test' => 'This is a test log entry']),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test log entry created successfully',
                'log' => $log
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create test log entry: ' . $e->getMessage()
            ], 500);
        }
    }
}







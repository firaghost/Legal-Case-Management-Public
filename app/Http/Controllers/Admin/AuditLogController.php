<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class AuditLogController extends Controller
{
    /**
     * Available model types for filtering
     */
    protected $modelTypes = [
        'App\\Models\\User' => 'Users',
        'App\\Models\\Role' => 'Roles',
        'App\\Models\\Permission' => 'Permissions',
        'App\\Models\\LitigationCase' => 'Cases',
        'App\\Models\\Appointment' => 'Appointments',
        'App\\Models\\Appeal' => 'Appeals',
    ];
    
    /**
     * Action type mappings for display
     */
    protected $actionTypes = [
        'LOGIN' => 'Login',
        'LOGOUT' => 'Logout',
        'LOGIN_FAILED' => 'Failed Login',
        'PASSWORD_RESET' => 'Password Reset',
        'EMAIL_VERIFIED' => 'Email Verified',
        'CREATE_' => 'Create',
        'UPDATE_' => 'Update',
        'DELETE_' => 'Delete',
        'RESTORE_' => 'Restore',
        'FORCE_DELETE_' => 'Force Delete',
    ];




    /**
     * Display audit logs with optional filters.
     */
    public function index()
    {
        // Debug: Log the request parameters
        Log::debug('AuditLogController@index', [
            'request' => request()->all(),
            'user_id' => auth()->id(),
            'url' => request()->fullUrl(),
            'user_roles' => auth()->user() ? auth()->user()->getRoleNames() : []
        ]);

        try {
            $query = $this->buildFilteredQuery();
            
            // Debug: Log the raw SQL query
            $sql = Str::replaceArray('?', $query->getBindings(), $query->toSql());
            Log::debug('Audit log query:', [
                'sql' => $sql,
                'bindings' => $query->getBindings()
            ]);
            
            // Get paginated results with user relationships
            $logs = $query->with('user')
                         ->latest()
                         ->paginate(20)
                         ->withQueryString();

            // Debug: Log the number of logs found
            Log::debug('Audit logs found:', [
                'count' => $logs->total(),
                'current_page' => $logs->currentPage(),
                'per_page' => $logs->perPage(),
                'has_more_pages' => $logs->hasMorePages()
            ]);
            
            if ($logs->isEmpty()) {
                // Debug: Check if there are any logs at all
                $totalLogs = \App\Models\AuditLog::count();
                $sampleLog = \App\Models\AuditLog::first();
                
                Log::debug('No logs found in pagination', [
                    'total_logs_in_db' => $totalLogs,
                    'sample_log' => $sampleLog ? [
                        'id' => $sampleLog->id,
                        'action' => $sampleLog->action,
                        'auditable_type' => $sampleLog->auditable_type,
                        'auditable_id' => $sampleLog->auditable_id,
                        'created_at' => $sampleLog->created_at
                    ] : null,
                    'applied_filters' => request()->all(),
                    'query_explain' => $query->explain()
                ]);
            }

            // Get users for the filter dropdown
            $users = User::orderBy('name')
                        ->pluck('name', 'id');

            // Add debug information to the view
            $debugInfo = [
                'total_logs' => \App\Models\AuditLog::count(),
                'query' => $sql,
                'bindings' => $query->getBindings(),
                'applied_filters' => request()->all()
            ];

            return view('admin.logs', [
                'logs' => $logs,
                'users' => $users,
                'modelTypes' => $this->modelTypes,
                'actionTypes' => $this->actionTypes,
                '_debug' => $debugInfo // Add debug info to view
            ]);
        } catch (\Exception $e) {
            Log::error('Error in AuditLogController@index: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return an error response
            return back()->with('error', 'An error occurred while loading the audit logs. Please check the logs for more details.');
        }
    }
    
    /**
     * Build query with filters applied
     */
    protected function buildFilteredQuery()
    {
        $query = AuditLog::query();
        $request = request();
        
        // Date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        // User filter
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        // Action type filter
        if ($request->filled('action')) {
            if (in_array($request->action, ['LOGIN', 'LOGOUT', 'LOGIN_FAILED'])) {
                $query->where('action', $request->action);
            } else {
                $query->where('action', 'like', $request->action . '%');
            }
        }
        
        // Model type filter
        if ($request->filled('auditable_type')) {
            $query->where('auditable_type', $request->auditable_type);
        }
        
        // Search in changes
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('changes', 'like', $searchTerm)
                  ->orWhere('action', 'like', $searchTerm);
            });
        }
        
        return $query;
    }

    /**
     * Export audit logs as PDF or CSV.
     *
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function export()
    {
        $format = request('format', 'pdf');
        $logs = $this->buildFilteredQuery()
                    ->with('user')
                    ->latest()
                    ->get();

        $filename = 'audit-logs-' . now()->format('Y-m-d-H-i-s');

        if ($format === 'excel') {
            return $this->exportToCsv($logs, $filename);
        }

        return $this->exportToPdf($logs, $filename);
    }
    
    /**
     * Export logs to CSV format
     */
    protected function exportToCsv($logs, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
        ];
        
        return response()->streamDownload(function () use ($logs) {
            $out = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 compatibility
            echo "\xEF\xBB\xBF";
            
            // Headers
            fputcsv($out, [
                'Date/Time', 
                'User', 
                'Action', 
                'Model Type', 
                'Model ID', 
                'IP Address',
                'User Agent',
                'Changes'
            ]);
            
            // Data rows
            foreach ($logs as $log) {
                $changes = is_array($log->changes) 
                    ? json_encode($log->changes, JSON_PRETTY_PRINT)
                    : $log->changes;
                    
                fputcsv($out, [
                    $log->created_at?->format('Y-m-d H:i:s'),
                    optional($log->user)->name ?? 'System',
                    $this->getActionLabel($log->action),
                    $log->auditable_type ? class_basename($log->auditable_type) : 'N/A',
                    $log->auditable_id ?? 'N/A',
                    $log->ip_address,
                    $log->user_agent,
                    $changes
                ]);
            }
            
            fclose($out);
        }, $filename . '.csv', $headers);
    }
    
    /**
     * Export logs to PDF format
     */
    protected function exportToPdf($logs, $filename)
    {
        $pdf = Pdf::loadView('admin.exports.audit-logs-pdf', [
            'logs' => $logs,
            'actionTypes' => $this->actionTypes,
            'filters' => request()->all(),
        ]);
        
        return $pdf->download($filename . '.pdf');
    }
    
    /**
     * Get human-readable action label
     */
    protected function getActionLabel($action)
    {
        foreach ($this->actionTypes as $key => $label) {
            if (Str::startsWith($action, $key)) {
                $modelName = Str::after($action, $key);
                return $label . ($modelName ? ' ' . $modelName : '');
            }
        }
        return $action;
    }
}







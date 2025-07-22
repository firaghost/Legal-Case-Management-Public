<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\CaseFile;
use App\Models\User;
use App\Models\CaseType;
use Illuminate\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $caseTypes = CaseType::orderBy('name')->get();
        $selectedType = $request->get('type');
        $pendingApprovalCount = CaseFile::whereNull('approved_at')->count();
        $byTypePending = $caseTypes->mapWithKeys(fn($t) => [
            $t->id => CaseFile::where('case_type_id', $t->id)
                ->whereNull('approved_at')->count()
        ])->toArray();
        $stats = [
            'pending_closure' => CaseFile::whereNull('closed_at')->count(),
            'pending_approval' => $pendingApprovalCount,
            'execution_opened' => CaseFile::whereNotNull('execution_opened_at')->count(),
            'appeals' => 0, // Placeholder
            'actions' => 0, // Placeholder
            'by_type' => $caseTypes->mapWithKeys(fn($t) => [
                $t->id => CaseFile::where('case_type_id', $t->id)->count()
            ])->toArray(),
            'by_type_pending' => $byTypePending,
        ];

        // Cases needing authorization (early closure requested)
        // recent updates from lawyers
        $recentUpdates = \App\Models\ProgressUpdate::with(['caseFile:id,file_number','user:id,name'])
            ->latest()->take(6)->get(['id','case_file_id','notes','created_at','updated_by']);

        $needAuthQuery = CaseFile::whereNull('approved_at');
        if ($selectedType) {
            $needAuthQuery->where('case_type_id', $selectedType);
        }
        $needAuth = $needAuthQuery->latest()->take(10)->get(['id', 'file_number', 'title', 'closure_requested_at', 'case_type_id']);

        return view('supervisor.dashboard', compact('stats', 'needAuth', 'recentUpdates', 'caseTypes', 'selectedType'));
    }
}







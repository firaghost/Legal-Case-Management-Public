<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\CaseFile;
use App\Models\User;
use App\Models\ActionLog;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use App\Notifications\CaseClosureApproved;
use App\Notifications\CaseReassigned;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class CaseController extends Controller
{
    /**
     * Supervisor approves a case (initial or closure).
     */
    public function approve(CaseFile $case)
    {
        abort_unless(auth()->check() && (auth()->user()->hasAnyRole(['supervisor','Supervisor']) || auth()->user()->hasPermissionTo('approve cases') || (property_exists(auth()->user(), 'role') && auth()->user()->role === 'supervisor')), 403);

        if ($case->approved_at) {
            return back()->with('error', 'Case already approved.');
        }

        if ($case->closure_requested_at) {
            // Approve closure
            $case->approved_at = now();
            if (!$case->closed_at) {
                $case->closed_at = now();
            }
            $case->status = 'Closed';
            $case->save();
            // Notify assigned lawyer, or fallback to creator
            $recipient = $case->lawyer ?: $case->creator;
            if ($recipient) {
                $recipient->notify(new \App\Notifications\CaseClosureApproved($case));
            }
            return back()->with('success', 'Case closure approved.');
        } else {
            // Initial approval
            $case->approved_at = now();
            $case->status = 'Open'; // or whatever status is appropriate
            $case->save();
            // Notify both the lawyer and the creator (if different)
            $notified = [];
            if ($case->lawyer) {
                $case->lawyer->notify(new \App\Notifications\CaseApproved($case, auth()->user()));
                $notified[] = $case->lawyer->id;
            }
            if ($case->creator && (!in_array($case->creator->id, $notified))) {
                $case->creator->notify(new \App\Notifications\CaseApproved($case, auth()->user()));
            }
            return back()->with('success', 'Case approved.');
        }
    }

    public function index(Request $request): View|StreamedResponse
    {
        $query = CaseFile::with(['lawyer:id,name','branch:id,name'])->withCount('progressUpdates')->latest();
        if ($request->filled('search')) {
            $query->where('file_number', 'like', '%' . $request->input('search') . '%');
        }
        if ($request->filled('type')) {
            $query->where('case_type_id', $request->input('type'));
        }
        $cases = $query->paginate(20);
        // Export logic
        if ($request->has('export')) {
            $exportQuery = CaseFile::with(['lawyer:id,name','branch:id,name'])->withCount('progressUpdates')->latest();
            if ($request->get('export') === 'row' && $request->has('id')) {
                $exportQuery->where('id', $request->get('id'));
            } elseif ($request->get('export') === 'bulk' && $request->has('ids')) {
                $ids = explode(',', $request->get('ids'));
                $exportQuery->whereIn('id', $ids);
            }
            $exportCases = $exportQuery->get();
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="cases.csv"',
            ];
            $columns = ['File #', 'Type', 'Status', 'Lawyer', 'Branch', 'Progress Updates', 'Opened At', 'Closed At', 'Description'];
            return response()->streamDownload(function () use ($exportCases, $columns) {
                $out = fopen('php://output', 'w');
                fputcsv($out, $columns);
                foreach ($exportCases as $case) {
                    fputcsv($out, [
                        $case->file_number,
                        $case->type,
                        $case->status,
                        $case->lawyer->name ?? '-',
                        $case->branch->name ?? '-',
                        $case->progress_updates_count ?? 0,
                        $case->opened_at,
                        $case->closed_at,
                        $case->description,
                    ]);
                }
                fclose($out);
            }, 'cases.csv', $headers);
        }
        return view('supervisor.cases.index', compact('cases'));
    }

    public function approvals(): View
    {
        $cases = CaseFile::with([
            'lawyer:id,name',
            'branch:id,name',
            'caseType:id,name,code',
            'court:id,name',
            'workUnit:id,name',
            'evidences:id,case_file_id,original_name,stored_name,mime_type,size,hash,uploaded_by,created_at',
            'plaintiffs:id,case_file_id,name,contact_number,address,email',
            'defendants:id,case_file_id,name,contact_number,address,email',
            'appointments:id,case_file_id,appointment_date,appointment_time,title,purpose,location,notes,created_by',
            'progressUpdates:id,case_file_id,status,notes,updated_by,created_at',
            // Load case-type-specific relationships
            'litigation',
            'laborLitigation', 
            'otherCivilLitigation',
            'criminalLitigation',
            'securedLoanRecovery.auctions',
            'legalAdvisory',
            'cleanLoanRecovery'
        ])
        ->whereNull('approved_at')
        ->latest()
        ->get();

        return view('supervisor.cases.approvals', compact('cases'));
    }

    public function closed(Request $request): View|StreamedResponse
    {
        $cases = CaseFile::with(['lawyer:id,name'])
            ->whereNotNull('closed_at')
            ->latest()->paginate(20);
        $stats = [
            'claimed' => CaseFile::whereNotNull('closed_at')->sum('claimed_amount'),
            'recovered' => CaseFile::whereNotNull('closed_at')->sum('recovered_amount'),
        ];
        // Export logic
        if ($request->has('export')) {
            $query = CaseFile::with(['lawyer:id,name'])->whereNotNull('closed_at')->latest();
            if ($request->get('export') === 'row' && $request->has('id')) {
                $query->where('id', $request->get('id'));
            } elseif ($request->get('export') === 'bulk' && $request->has('ids')) {
                $ids = explode(',', $request->get('ids'));
                $query->whereIn('id', $ids);
            }
            $exportCases = $query->get();
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="closed_cases.csv"',
            ];
            $columns = ['File #', 'Type', 'Lawyer', 'Closed At', 'Claimed', 'Recovered'];
            return response()->streamDownload(function () use ($exportCases, $columns) {
                $out = fopen('php://output', 'w');
                fputcsv($out, $columns);
                foreach ($exportCases as $case) {
                    fputcsv($out, [
                        $case->file_number,
                        $case->type,
                        $case->lawyer->name ?? '-',
                        $case->closed_at,
                        $case->claimed_amount,
                        $case->recovered_amount,
                    ]);
                }
                fclose($out);
            }, 'closed_cases.csv', $headers);
        }
        return view('supervisor.cases.closed', compact('cases', 'stats'));
    }

    public function reports(Request $request): View|StreamedResponse
    {
        $cases = CaseFile::with(['lawyer:id,name','branch:id,name'])
            ->whereNotNull('closed_at')
            ->latest()->paginate(20);
        $stats = [
            'claimed' => CaseFile::whereNotNull('closed_at')->sum('claimed_amount'),
            'recovered' => CaseFile::whereNotNull('closed_at')->sum('recovered_amount'),
        ];
        // Export logic
        if ($request->has('export')) {
            $query = CaseFile::with(['lawyer:id,name','branch:id,name'])->whereNotNull('closed_at')->latest();
            if ($request->get('export') === 'row' && $request->has('id')) {
                $query->where('id', $request->get('id'));
            } elseif ($request->get('export') === 'bulk' && $request->has('ids')) {
                $ids = explode(',', $request->get('ids'));
                $query->whereIn('id', $ids);
            }
            $exportCases = $query->get();
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="report_cases.csv"',
            ];
            $columns = ['File #', 'Type', 'Lawyer', 'Branch', 'Closed At', 'Claimed', 'Recovered'];
            return response()->streamDownload(function () use ($exportCases, $columns) {
                $out = fopen('php://output', 'w');
                fputcsv($out, $columns);
                foreach ($exportCases as $case) {
                    fputcsv($out, [
                        $case->file_number,
                        $case->type,
                        $case->lawyer->name ?? '-',
                        $case->branch->name ?? '-',
                        $case->closed_at,
                        $case->claimed_amount,
                        $case->recovered_amount,
                    ]);
                }
                fclose($out);
            }, 'report_cases.csv', $headers);
        }
        return view('supervisor.cases.reports', compact('cases', 'stats'));
    }

    public function advisory(Request $request): View|StreamedResponse
    {
        $cases = CaseFile::whereNotNull('advisory_requested_at')->latest()->paginate(20);
        // Export logic
        if ($request->has('export')) {
            $query = CaseFile::whereNotNull('advisory_requested_at')->latest();
            if ($request->get('export') === 'row' && $request->has('id')) {
                $query->where('id', $request->get('id'));
            } elseif ($request->get('export') === 'bulk' && $request->has('ids')) {
                $ids = explode(',', $request->get('ids'));
                $query->whereIn('id', $ids);
            }
            $exportCases = $query->get();
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="advisory_cases.csv"',
            ];
            $columns = ['File #', 'Title', 'Requested', 'Lawyer', 'Description'];
            return response()->streamDownload(function () use ($exportCases, $columns) {
                $out = fopen('php://output', 'w');
                fputcsv($out, $columns);
                foreach ($exportCases as $case) {
                    fputcsv($out, [
                        $case->file_number,
                        $case->title,
                        $case->advisory_requested_at,
                        $case->lawyer->name ?? '-',
                        $case->description,
                    ]);
                }
                fclose($out);
            }, 'advisory_cases.csv', $headers);
        }
        return view('supervisor.cases.advisory', compact('cases'));
    }

    /**
     * Bulk approve cases by supervisor.
     */
    public function bulkApprove(Request $request)
    {
        $ids = collect(explode(',', $request->input('ids')))->filter()->unique();
        if ($ids->isEmpty()) {
            return back()->with('error', 'No cases selected.');
        }
        abort_unless(auth()->check() && (auth()->user()->hasAnyRole(['supervisor','Supervisor']) || auth()->user()->hasPermissionTo('approve cases') || (property_exists(auth()->user(), 'role') && auth()->user()->role === 'supervisor')), 403);
        $count = 0;
        DB::beginTransaction();
        try {
            $cases = \App\Models\CaseFile::whereIn('id', $ids)->get();
            foreach ($cases as $case) {
                $case->approved_at = now();
                if (!$case->closed_at) {
                    $case->closed_at = now();
                }
                $case->status = 'Closed';
                $case->save();
                $recipient = $case->lawyer ?: $case->creator;
                if ($recipient) {
                    $recipient->notify(new \App\Notifications\CaseClosureApproved($case));
                }
                $count++;
            }
            DB::commit();
            return back()->with('success', "$count case(s) approved.");
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Bulk approval error', ['ids' => $ids, 'message' => $e->getMessage()]);
            return back()->with('error', 'Error approving cases: ' . $e->getMessage());
        }
    }

    /**
     * Display a case in read-only mode for supervisors.
     */
    public function show(CaseFile $case)
    {
        // Load relations like lawyer controller for detail view
                $case->load([
            'caseType', 'branch', 'workUnit', 'lawyer', 'evidences',
            'securedLoanRecovery.auctions',
            'appeals', 'progressUpdates', 'appointments'
        ]);
        // Determine type-specific data for view compatibility
        $caseTypeData = null;
        $caseType = strtolower(str_replace(' ', '_', $case->caseType->name ?? ''));
        switch ($caseType) {
            case 'clean_loan_recovery':
                $caseTypeData = $case->cleanLoanRecovery;
                break;
            case 'secured_loan_recovery':
                $caseTypeData = $case->securedLoanRecovery;
                break;
            case 'labor_litigation':
                $caseTypeData = $case->laborLitigation;
                break;
            case 'other_civil_litigation':
                $caseTypeData = $case->otherCivilLitigation;
                break;
            case 'criminal_litigation':
                $caseTypeData = $case->criminalLitigation;
                break;
            case 'legal_advisory':
                $caseTypeData = $case->legalAdvisory;
                break;
        }
        return view('lawyer.cases.show', [
            'case' => $case,
            'caseTypeData' => $caseTypeData,
        ]); // same view, action buttons hidden
    }

    /**
     * Reassign a case to a different lawyer.
     */
    public function reassignLawyer(Request $request, CaseFile $case)
    {
        // Check authorization
        abort_unless(auth()->check() && (auth()->user()->hasAnyRole(['supervisor','Supervisor']) || auth()->user()->hasPermissionTo('reassign cases') || (property_exists(auth()->user(), 'role') && auth()->user()->role === 'supervisor')), 403);

        $request->validate([
            'new_lawyer_id' => 'required|exists:users,id',
            'reason' => 'nullable|string|max:500'
        ]);

        $newLawyer = User::findOrFail($request->new_lawyer_id);
        
        // Check if new lawyer has the lawyer role
        if (!$newLawyer->hasAnyRole(['lawyer', 'Lawyer']) && $newLawyer->role !== 'lawyer') {
            return back()->with('error', 'Selected user is not a lawyer.');
        }

        $oldLawyer = $case->lawyer;
        $supervisor = auth()->user();

        // Prevent reassigning to the same lawyer
        if ($oldLawyer && $oldLawyer->id == $newLawyer->id) {
            return back()->with('error', 'Case is already assigned to this lawyer.');
        }

        DB::beginTransaction();
        try {
            // Update case assignment
            $case->lawyer_id = $newLawyer->id;
            $case->save();

            // Log the action
            ActionLog::create([
                'user_id' => $supervisor->id,
'model_type' => get_class($case),
                'model_id' => $case->id,
                'action' => 'reassigned',
                'description' => 'Case reassigned from ' . ($oldLawyer ? $oldLawyer->name : 'Unassigned') . ' to ' . $newLawyer->name . ($request->reason ? ' - Reason: ' . $request->reason : ''),
                'old_values' => ['lawyer_id' => $oldLawyer?->id],
                'new_values' => ['lawyer_id' => $newLawyer->id],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Send notifications immediately
            $notification = new CaseReassigned($case, $oldLawyer, $newLawyer, $supervisor);
            
            // Notify the new lawyer immediately
            $newLawyer->notifyNow($notification);
            
            // Notify the old lawyer immediately if there was one
            if ($oldLawyer) {
                $oldLawyer->notifyNow($notification);
            }

            DB::commit();

            return back()->with('success', 'Case successfully reassigned to ' . $newLawyer->name . '.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Case reassignment error', [
                'case_id' => $case->id,
                'old_lawyer_id' => $oldLawyer?->id,
                'new_lawyer_id' => $newLawyer->id,
                'supervisor_id' => $supervisor->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Error reassigning case: ' . $e->getMessage());
        }
    }


    /**
      * Show the edit history for a case.
      */
    public function editHistory(\App\Models\CaseFile $case)
    {
        // Primary case logs
        $caseLogs = $case->actionLogs()->whereIn('action', ['created','updated','reassigned'])->get();

        // Related case-type logs (secured loan etc.)
        $relatedLogs = collect();
        if($case->securedLoanRecovery){
            $relatedLogs = $case->securedLoanRecovery->actionLogs()->whereIn('action',['created','updated'])->get();
        }
        $editLogs = $caseLogs->concat($relatedLogs)->sortByDesc('created_at');
        return view('supervisor.cases.edit_history', [
            'case' => $case,
            'editLogs' => $editLogs,
        ]);
    }

    /**
     * Show assignment history and monitoring for all cases
     */
    public function assignmentHistory(Request $request): View|StreamedResponse
    {
        try {
            // Get all reassignment logs with case and user information
            $query = ActionLog::with([
                    'user:id,name', 
                    'model' => function($query) {
                        $query->select('id', 'file_number', 'title', 'lawyer_id')
                              ->with('lawyer:id,name');
                    }
                ])
                ->where('action', 'reassigned')
                ->where('model_type', 'App\\Models\\CaseFile')
                ->latest();

            // Apply filters
            if ($request->filled('case_search')) {
                $query->whereHas('model', function($q) use ($request) {
                    $q->where('file_number', 'like', '%' . $request->input('case_search') . '%');
                });
            }

            if ($request->filled('supervisor')) {
                $query->where('user_id', $request->input('supervisor'));
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->input('date_from'));
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->input('date_to'));
            }

            // Export functionality
            if ($request->has('export')) {
                $exportLogs = $query->get();
                $headers = [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="assignment_history.csv"',
                ];
                $columns = ['Date', 'Case Number', 'Case Title', 'Supervisor', 'Action', 'Old Lawyer', 'New Lawyer', 'Reason'];
                
                return response()->streamDownload(function () use ($exportLogs, $columns) {
                    $out = fopen('php://output', 'w');
                    fputcsv($out, $columns);
                    foreach ($exportLogs as $log) {
                        // Parse old and new lawyer from description or values
                        $oldLawyerId = $log->old_values['lawyer_id'] ?? null;
                        $newLawyerId = $log->new_values['lawyer_id'] ?? null;
                        $oldLawyerName = $oldLawyerId ? User::find($oldLawyerId)?->name : 'Unassigned';
                        $newLawyerName = $newLawyerId ? User::find($newLawyerId)?->name : 'Unknown';
                        
                        // Extract reason from description
                        $reason = '';
                        if (str_contains($log->description, ' - Reason: ')) {
                            $reason = explode(' - Reason: ', $log->description)[1];
                        }
                        
                        fputcsv($out, [
                            $log->created_at->format('Y-m-d H:i:s'),
                            $log->model->file_number ?? 'N/A',
                            $log->model->title ?? 'N/A',
                            $log->user->name ?? 'Unknown',
                            'Case Reassigned',
                            $oldLawyerName,
                            $newLawyerName,
                            $reason
                        ]);
                    }
                    fclose($out);
                }, 'assignment_history.csv', $headers);
            }

            $assignmentLogs = $query->paginate(20);
            
            // Get supervisors for filter dropdown
            $supervisors = User::whereHas('roles', function($q) {
                $q->where('name', 'supervisor');
            })->orWhere('role', 'supervisor')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

            return view('supervisor.cases.assignment_history', compact('assignmentLogs', 'supervisors'));
            
        } catch (\Exception $e) {
            \Log::error('Assignment History Error: ' . $e->getMessage());
            return back()->with('error', 'Error loading assignment history: ' . $e->getMessage());
        }
    }
}







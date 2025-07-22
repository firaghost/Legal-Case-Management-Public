<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use App\Models\LitigationCase;
use App\Models\Appeal;
use App\Models\ProgressUpdate;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\FileNumberService;
use Illuminate\Validation\Rule;
use App\Notifications\CaseApprovalRequested;
use App\Models\User;

class LitigationController extends Controller
{   
    public function __construct(private WorkflowService $workflow) {}

    /**
     * Store a newly created litigation case (Clean Loan Recovery).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'branch' => 'required|string',
            'internal_file_no' => 'required|string|unique:litigation_cases,internal_file_no',
            'file_number' => 'sometimes|string|unique:case_files,file_number',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'outstanding_amount' => 'required|numeric',
            'plaintiffs' => 'required|array',
            'defendants' => 'required|array',
        ]);

        return DB::transaction(function () use ($data, $request) {
            $caseFile = CaseFile::create([
                'file_number' => $data['file_number'] ?? $request->input('file_number'),
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'case_type_id' => 1, // assuming ID 1 is Clean Loan Recovery Litigation
                'status' => 'Open',
                'opened_at' => now(),
                'lawyer_id' => $request->user()->id,
                'created_by' => $request->user()->id,
            ]);

            $litigation = LitigationCase::create([
                'case_file_id' => $caseFile->id,
                'branch' => $data['branch'],
                'internal_file_no' => $data['internal_file_no'],
                'outstanding_amount' => $data['outstanding_amount'],
            ]);

            // TODO: insert plaintiffs / defendants arrays

            return response()->json($litigation->load('caseFile'), 201);
        });
    }

    /**
     * Add a progress update.
     */
    public function addProgress(Request $request, CaseFile $caseFile)
    {
        $this->authorize('update', $caseFile);

        $data = $request->validate([
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $update = ProgressUpdate::create([
            'case_file_id' => $caseFile->id,
            'status' => $data['status'],
            'notes' => $data['notes'] ?? null,
            'updated_by' => $request->user()->id,
        ]);

        return response()->json($update, 201);
    }

    /**
     * Add an appeal/cassation.
     */
    public function addAppeal(Request $request, CaseFile $caseFile)
    {
        $this->authorize('update', $caseFile);

        $data = $request->validate([
            'level' => ['required', Rule::in(['Appeal', 'Second Appeal', 'Cassation'])],
            'file_number' => 'required|string',
            'notes' => 'nullable|string',
            'decided_at' => 'nullable|date',
        ]);

        $appeal = $this->workflow->createAppeal($caseFile, $data);

        return response()->json($appeal, 201);
    }

    /**
     * Open execution file if case won.
     */
    public function openExecution(Request $request, CaseFile $caseFile)
    {
        $this->authorize('update', $caseFile);

        $exec = $this->workflow->openExecution($caseFile, $request->user()->id);
        return response()->json($exec, 201);
    }

    /**
     * Close case file.
     */
    public function closeCase(Request $request, CaseFile $caseFile)
    {
        $this->authorize('update', $caseFile);

        $caseFile->update(['status' => 'Closed']);
        return response()->json($caseFile, 201);
    }

    /**
     * Request early closure (to be approved by supervisor).
     */
    public function requestClosure(Request $request, CaseFile $caseFile)
    {
        $this->authorize('update', $caseFile);
        // Flag for supervisor review
        $caseFile->update(['status' => 'Pending Closure', 'closure_requested_at' => now()]);
        // Notify all supervisors
        $supervisors = User::whereHas('roles', function($q) { $q->where('name', 'supervisor'); })->get();
        foreach ($supervisors as $supervisor) {
            $supervisor->notify(new CaseApprovalRequested($caseFile));
        }
        return response()->json(['message' => 'Closure requested'], 202);
    }
}







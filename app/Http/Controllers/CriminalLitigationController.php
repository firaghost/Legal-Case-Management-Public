<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use App\Models\CriminalLitigationCase;

use App\Models\ProgressUpdate;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Notifications\CaseApprovalRequested;
use App\Models\User;

class CriminalLitigationController extends Controller
{
    public function __construct(private WorkflowService $workflow) {}

    /**
     * Store a new Criminal Litigation case (Code 04).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'police_ref_no' => 'required|string',
            'prosecutor_ref_no' => 'nullable|string',
            'evidence_summary' => 'nullable|string',
            'file_number' => 'required|string|unique:case_files,file_number',
            'title' => 'required|string',
            'description' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($data, $request) {
            $caseFile = CaseFile::create([
                'file_number' => $data['file_number'],
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'case_type_id' => 4, // assume 4 is Criminal Litigation
                'status' => 'Open',
                'opened_at' => now(),
                'lawyer_id' => $request->user()->id,
                'created_by' => $request->user()->id,
            ]);

            $criminal = CriminalLitigationCase::create([
                'case_file_id' => $caseFile->id,
                'police_ref_no' => $data['police_ref_no'],
                'prosecutor_ref_no' => $data['prosecutor_ref_no'] ?? null,
                'evidence_summary' => $data['evidence_summary'] ?? null,
            ]);

            return response()->json($criminal->load('caseFile'), 201);
        });
    }

    /**
     * Log progress updates.
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
     * Add appeal / cassation record.
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
    
    public function openExecution(Request $request, CaseFile $caseFile)
    {
        $this->authorize('update', $caseFile);
        $data = $request->validate([
            'level' => ['required', Rule::in(['Appeal', 'Cassation'])],
            'file_number' => 'required|string',
            'notes' => 'nullable|string',
            'decided_at' => 'nullable|date',
        ]);
        $appeal = Appeal::create(array_merge($data, [
            'case_file_id' => $caseFile->id,
        ]));
        return response()->json($appeal, 201);
    }

    /**
     * Request early closure.
     */
    public function requestClosure(Request $request, CaseFile $caseFile)
    {
        $this->authorize('update', $caseFile);
        $caseFile->update(['status' => 'Pending Closure', 'closure_requested_at' => now()]);
        $supervisors = User::whereHas('roles', function($q) { $q->where('name', 'supervisor'); })->get();
        foreach ($supervisors as $supervisor) {
            $supervisor->notify(new CaseApprovalRequested($caseFile));
        }
        return response()->json(['message' => 'Closure requested'], 202);
    }
}







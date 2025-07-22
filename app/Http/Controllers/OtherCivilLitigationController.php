<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use App\Models\OtherCivilLitigationCase;

use App\Models\ProgressUpdate;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Notifications\CaseApprovalRequested;
use App\Models\User;

class OtherCivilLitigationController extends Controller
{
    public function __construct(private WorkflowService $workflow) {}

    /**
     * Store a new Other Civil Litigation case (Code 03).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'claim_type' => ['required', Rule::in(['Money', 'Material', 'Both'])],
            'claim_amount' => 'nullable|numeric',
            'claim_material_desc' => 'nullable|string',
            'file_number' => 'required|string|unique:case_files,file_number',
            'title' => 'required|string',
            'description' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($data, $request) {
            $caseFile = CaseFile::create([
                'file_number' => $data['file_number'],
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'case_type_id' => 3, // assume 3 represents Other Civil Litigation
                'status' => 'Open',
                'opened_at' => now(),
                'lawyer_id' => $request->user()->id,
                'created_by' => $request->user()->id,
            ]);

            $civil = OtherCivilLitigationCase::create([
                'case_file_id' => $caseFile->id,
                'claim_type' => $data['claim_type'],
                'claim_amount' => $data['claim_amount'],
                'claim_material_desc' => $data['claim_material_desc'],
            ]);

            return response()->json($civil->load('caseFile'), 201);
        });
    }

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
            'level' => ['required', Rule::in(['Appeal', 'Second', 'Cassation'])],
            'file_number' => 'required|string',
            'notes' => 'nullable|string',
            'decided_at' => 'nullable|date',
        ]);
        $exec = $this->workflow->openExecution($caseFile, $request->user()->id);
        return response()->json($exec, 201);
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







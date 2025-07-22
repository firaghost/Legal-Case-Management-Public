<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use App\Services\WorkflowService;
use Illuminate\Http\Request;

class SupervisorReviewController extends Controller
{
    public function __construct(private WorkflowService $workflow) {}

    /**
     * Approve early closure for a case file previously marked Pending Closure.
     */
    public function approveClosure(Request $request, CaseFile $caseFile)
    {
        $this->authorize('approveClosure', $caseFile); // Policy check

        if ($caseFile->status !== 'Pending Closure') {
            return response()->json(['error' => 'Case is not pending closure'], 422);
        }

        $case = $this->workflow->closeCase($caseFile, $request->user()->id);
        return response()->json($case);
    }
}







<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class SupervisorApprovalController extends Controller
{
    /**
     * List cases awaiting supervisor approval (execution start / early closure).
     */
    public function index(Request $request)
    {
        $query = CaseFile::query()->whereIn('status', ['Awaiting Supervisor Execution', 'Awaiting Supervisor Closure']);
        if ($search = $request->input('search')) {
            $query->where('company_file_number', 'like', "%{$search}%");
        }
        $perPage = $request->input('perPage', 25);
        $cases = $query->with(['lawyer:id,name'])
            ->paginate($perPage);
        return response()->json($cases);
    }

    /**
     * Approve supervisor request (execution start or closure)
     */
    public function approve(Request $request, CaseFile $caseFile)
    {
        $this->authorize('approve', $caseFile); // ensure supervisor role via policy
        if (!str_contains($caseFile->status, 'Awaiting Supervisor')) {
            return response()->json(['error' => 'Not awaiting supervisor'], 422);
        }
        $newStatus = str_contains($caseFile->status, 'Execution') ? 'Execution Started' : 'Closed';
        $caseFile->update(['status' => $newStatus, 'supervisor_decision_at' => now(), 'supervisor_id' => $request->user()->id]);
        Log::info('Supervisor approved', ['case_id' => $caseFile->id, 'user_id' => $request->user()->id, 'status' => $newStatus]);
        return response()->json($caseFile);
    }

    /**
     * Reject supervisor request with mandatory comment.
     */
    public function reject(Request $request, CaseFile $caseFile)
    {
        $this->authorize('approve', $caseFile);
        $request->validate(['comment' => 'required|string']);
        $caseFile->update(['status' => 'Supervisor Rejected', 'supervisor_decision_at' => now(), 'supervisor_id' => $request->user()->id]);
        Log::info('Supervisor rejected', ['case_id' => $caseFile->id, 'user_id' => $request->user()->id, 'comment' => $request->comment]);
        // TODO: Notify assigned lawyer via notification system
        return response()->json($caseFile);
    }
}







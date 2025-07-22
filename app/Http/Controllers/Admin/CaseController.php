<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\CaseFile;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class CaseController extends Controller
{
    /**
     * Display a listing of all cases.
     */
    public function index(): View
    {
        try {
            $query = CaseFile::with(['caseType', 'lawyer', 'branch']);

            if ($type = request('type')) {
                $query->where('case_type_id', $type);
            }
            if ($status = request('status')) {
                $query->where('status', $status);
            }
            if ($branch = request('branch')) {
                $query->where('branch_id', $branch);
            }
            if ($search = request('search')) {
                $query->where(function($q) use ($search) {
                    $q->where('file_number', 'like', "%$search%")
                      ->orWhere('title', 'like', "%$search%")
                      ->orWhere('description', 'like', "%$search%")
                      ->orWhereHas('lawyer', function($q2) use ($search) {
                          $q2->where('name', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%")
                      ;
                    });
                });
            }

            $cases = $query->latest()->paginate(20)->withQueryString();

            // Get all active branches
            $branches = Branch::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']);

            Log::info('Loading admin cases index', [
                'cases_count' => $cases->total(),
                'branches_count' => $branches->count()
            ]);

            return view('admin.cases.index', compact('cases', 'branches'));
            
        } catch (\Exception $e) {
            Log::error('Error in CaseController@index: ' . $e->getMessage());
            return view('admin.cases.index', [
                'cases' => collect([]),
                'branches' => collect([])
            ]);
        }
    }

    /**
     * Display the specified case.
     */
    public function show(CaseFile $case): View
    {
        $case->load(['lawyer', 'branch', 'plaintiffs', 'defendants', 'progressUpdates']);
        $updates = $case->progressUpdates;
        return view('admin.cases.show', compact('case', 'updates'));
    }

    /**
     * Show the edit history for a case.
     */
    public function editHistory(\App\Models\CaseFile $case)
    {
        $editLogs = $case->actionLogs()
            ->where('action', 'updated')
            ->whereNotNull('old_properties')
            ->get()
            ->filter(function ($log) {
                $fields = array_keys($log->old_properties ?? []);
                $nonSystemFields = array_filter($fields, function ($field) {
                    return $field !== 'updated_at';
                });
                return count($nonSystemFields) > 0;
            });
        return view('admin.cases.edit_history', [
            'case' => $case,
            'editLogs' => $editLogs,
        ]);
    }

    public function create(): View
    {
        $caseTypes = \App\Models\CaseType::orderBy('name')->get();
        $branches = \App\Models\Branch::where('is_active', true)->orderBy('name')->get();
        $workUnits = \App\Models\WorkUnit::where('is_active', true)->orderBy('name')->get();
        $lawyers = \App\Models\User::role('lawyer')->where('is_active', true)->get();
        
        return view('admin.cases.create', compact('caseTypes', 'branches', 'workUnits', 'lawyers'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'case_type_id' => 'required|exists:case_types,id',
            'branch_id' => 'required|exists:branches,id',
            'lawyer_id' => 'nullable|exists:users,id',
            'status' => 'required|string',
            'claimed_amount' => 'nullable|numeric',
            'recovered_amount' => 'nullable|numeric',
            'outstanding_amount' => 'nullable|numeric',
        ]);
        $case = \App\Models\CaseFile::create($data);
        return redirect()->route('admin.cases.index')->with('success', 'Case created successfully.');
    }
}







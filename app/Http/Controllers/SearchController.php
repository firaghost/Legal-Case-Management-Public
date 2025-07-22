<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    /**
     * Search across all cases
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
        ]);

        $query = $request->input('query');
        $searchTerm = '%' . $query . '%';

        // Search in case files
        $caseFiles = CaseFile::where(function($q) use ($searchTerm) {
                $q->where('file_number', 'LIKE', $searchTerm)
                  ->orWhere('title', 'LIKE', $searchTerm);
            })
            ->with(['litigation', 'laborLitigation', 'otherCivilLitigation', 'criminalLitigation', 'securedLoanRecovery'])
            ->limit(50)
            ->get();

        // Search in parties (plaintiffs/defendants)
        $casesByParty = CaseFile::whereHas('plaintiffs', function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', $searchTerm);
            })
            ->orWhereHas('defendants', function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', $searchTerm);
            })
            ->with(['litigation', 'laborLitigation', 'otherCivilLitigation', 'criminalLitigation', 'securedLoanRecovery'])
            ->limit(50)
            ->get();

        // Merge and deduplicate results
        $results = $caseFiles->merge($casesByParty)->unique('id');

        return response()->json([
            'data' => $results->values(),
            'meta' => [
                'total' => $results->count(),
                'query' => $query
            ]
        ]);
    }

    /**
     * Advanced search with filters
     */
    public function advancedSearch(Request $request)
    {
        $query = CaseFile::query();

        // Company File Number alias (maps to file_number)
        if ($request->filled('company_file_number')) {
            $query->where('file_number', 'LIKE', '%' . $request->input('company_file_number') . '%');
        }

        // Legacy / alias file_number
        if ($request->filled('file_number')) {
            $query->where('file_number', 'LIKE', '%' . $request->input('file_number') . '%');
        }

        // Court File Number
        if ($request->filled('court_file_number')) {
            $query->where('court_file_number', 'LIKE', '%' . $request->input('court_file_number') . '%');
        }

        // Party Name (plaintiff / defendant / appellant / respondent)
        if ($request->filled('party') || $request->filled('party_name')) {
            $partyTerm = '%' . ($request->input('party') ?? $request->input('party_name')) . '%';
            $query->where(function($q) use ($partyTerm) {
                $q->whereHas('plaintiffs', function($q) use ($partyTerm) {
                    $q->where('name', 'LIKE', $partyTerm);
                })->orWhereHas('defendants', function($q) use ($partyTerm) {
                    $q->where('name', 'LIKE', $partyTerm);
                });
            });
        }

        // Case type by slug/string (loan, labor, civil, criminal)
        if ($request->filled('case_type')) {
            $caseTypeStr = strtolower($request->input('case_type'));
            $caseTypeId = \App\Models\CaseType::where('slug', $caseTypeStr)->value('id');
            if ($caseTypeId) {
                $query->where('case_type_id', $caseTypeId);
            }
        }

        // Existing integer-based case_type_id filter
        if ($request->filled('case_type_id')) {
            $query->where('case_type_id', $request->input('case_type_id'));
        }

        // Status alias (open, closed, on_appeal, etc.)
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }


        // Filter by date range
        if ($request->has('start_date')) {
            $query->whereDate('opened_at', '>=', $request->input('start_date'));
        }
        if ($request->has('end_date')) {
            $query->whereDate('opened_at', '<=', $request->input('end_date'));
        }

        $results = $query->with([
            'litigation', 
            'laborLitigation', 
            'otherCivilLitigation', 
            'criminalLitigation', 
            'securedLoanRecovery',
            'plaintiffs',
            'defendants',
            'advisoryStakeholders',
            'documentReviews',
            'documentVersions',
            'advisoryCase',
            
        ])
        ->orderBy('opened_at', 'desc')
        ->paginate($request->input('per_page', 15));

        return response()->json($results);
    }
}







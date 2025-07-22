<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use App\Models\Evidence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminMetricsController extends Controller
{
    /**
     * Return summary metrics for the admin dashboard.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', CaseFile::class);

        $metrics = [
            'open_cases' => CaseFile::where('status', 'Open')->count(),
            'closed_cases' => CaseFile::where('status', 'Closed')->count(),
            'judgments_won' => CaseFile::where('result', 'Won')->count(),
            'judgments_lost' => CaseFile::where('result', 'Lost')->count(),
            'evidence_items' => Evidence::count(),
            'total_claimed' => DB::table('litigation_cases')->sum('claim_amount') + DB::table('secured_loan_recovery_cases')->sum('loan_amount'),
            'total_recovered' => DB::table('litigation_cases')->sum('recovered_amount') + DB::table('secured_loan_recovery_cases')->sum('recovered_amount'),
        ];

        return response()->json($metrics);
    }
}







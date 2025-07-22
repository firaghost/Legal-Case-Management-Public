<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use App\Models\SecuredLoanRecoveryCase;
use App\Models\ProgressUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SecuredLoanRecoveryController extends Controller
{
    /**
     * Store a new Secured Loan Recovery case (Code 05).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'loan_amount' => 'required|numeric|min:0',
            'outstanding_amount' => 'required|numeric|min:0',
            'collateral_description' => 'required|string',
            'collateral_value' => 'nullable|numeric|min:0',
            'file_number' => 'required|string|unique:case_files,file_number',
            'title' => 'required|string',
            'description' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($data, $request) {
            $caseFile = CaseFile::create([
                'file_number' => $data['file_number'],
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'case_type_id' => 5, // assume 5 is Secured Loan Recovery
                'status' => 'Open',
                'opened_at' => now(),
                'lawyer_id' => $request->user()->id,
                'created_by' => $request->user()->id,
            ]);

            $recovery = SecuredLoanRecoveryCase::create([
                'case_file_id' => $caseFile->id,
                'loan_amount' => $data['loan_amount'],
                'outstanding_amount' => $data['outstanding_amount'],
                'collateral_description' => $data['collateral_description'],
                'collateral_value' => $data['collateral_value'] ?? null,
                'foreclosure_notice_date' => now(),
            ]);

            return response()->json($recovery->load('caseFile'), 201);
        });
    }

    /**
     * Add progress update for a case.
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
     * Update auction status.
     */
    public function updateAuctionStatus(Request $request, CaseFile $caseFile)
    {
        $this->authorize('update', $caseFile);
        
        $data = $request->validate([
            'auction_round' => ['required', 'in:first,second'],
            'held' => 'required|boolean',
            'recovered_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $update = [];
        $field = $data['auction_round'] === 'first' 
            ? 'first_auction_held' 
            : 'second_auction_held';
            
        $update[$field] = $data['held'];
        
        if (isset($data['recovered_amount'])) {
            $update['recovered_amount'] = $data['recovered_amount'];
        }

        $recovery = $caseFile->securedLoanRecovery->update($update);

        // Add progress note
        if (!empty($data['notes'])) {
            ProgressUpdate::create([
                'case_file_id' => $caseFile->id,
                'status' => 'Auction ' . ucfirst($data['auction_round']) . ' ' . 
                            ($data['held'] ? 'Held' : 'Skipped'),
                'notes' => $data['notes'],
                'updated_by' => $request->user()->id,
            ]);
        }

        return response()->json($recovery);
    }

    /**
     * Close the case.
     */
    public function closeCase(Request $request, CaseFile $caseFile)
    {
        $this->authorize('update', $caseFile);
        
        $data = $request->validate([
            'closure_type' => ['required', Rule::in([
                'fully_repaid',
                'collateral_sold',
                'restructured',
                'settlement',
                'collateral_acquired'
            ])],
            'recovered_amount' => 'required|numeric|min:0',
            'notes' => 'required|string',
        ]);

        $recovery = $caseFile->securedLoanRecovery->update([
            'closure_type' => $data['closure_type'],
            'closure_notes' => $data['notes'],
            'recovered_amount' => $data['recovered_amount'],
            'closed_at' => now(),
            'closed_by' => $request->user()->id,
        ]);

        // Update case file status
        $caseFile->update(['status' => 'Closed']);

        // Add closure progress note
        ProgressUpdate::create([
            'case_file_id' => $caseFile->id,
            'status' => 'Case Closed',
            'notes' => 'Case closed: ' . str_replace('_', ' ', $data['closure_type']),
            'updated_by' => $request->user()->id,
        ]);

        return response()->json($recovery);
    }
}







<?php

namespace App\Http\Controllers\Lawyer;

use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use App\Models\CaseFile;
use App\Models\Plaintiff;
use App\Models\Defendant;
use App\Models\LegalAdvisoryCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Support\Facades\Request as RequestFacade;

class CaseController extends Controller
{
    /**
     * Show list of cases assigned to the lawyer.
     */
    public function index(Request $request): View
    {
        $query = CaseFile::with([
                'caseType',
                'plaintiffs',
                'defendants',
                'appointments' => function($query) {
                    $query->upcoming();
                }
            ])
            ->where('lawyer_id', auth()->id());

        // Apply filters if they exist
        if ($request->has('type') && $request->type !== '') {
            $query->where('case_type_id', $request->type);
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('has_upcoming_hearings')) {
            $query->whereHas('appointments', function ($q) {
                $q->where('appointment_date', '>=', now());
            });
        }

        if ($request->has('date') && $request->date !== '') {
            $query->whereDate('opened_at', $request->date);
        }

        $cases = $query->latest()->paginate(15);
        
        // Get filter values for the form
        $filters = $request->only(['type', 'status', 'date']);
            
        return view('lawyer.cases.index', compact('cases', 'filters'));
    }

    /**
     * Show form to create new case.
     */
    public function create(): View
    {
        $branches = \App\Models\Branch::where('is_active', true)->get();
        $workUnits = \App\Models\WorkUnit::where('is_active', true)->get();
        $lawyers = \App\Models\User::where('role', 'lawyer')->where('is_active', true)->get();

        return view('lawyer.cases.create', compact('branches', 'workUnits', 'lawyers'));
    }

    /**
     * Store a newly created case in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'type' => 'required|string|in:clean_loan,secured_loan,labor,civil,criminal,advisory',
            'file_number' => 'nullable|string|unique:case_files,file_number',
            'title' => 'required|string|max:255',
            'claimed_amount' => 'nullable|numeric|min:0',
            'branch_id' => 'nullable|exists:branches,id',
            'work_unit_id' => 'nullable|exists:work_units,id',
            'court_file_number' => 'nullable|string|max:255',
            'company_file_number' => 'nullable|string|max:255',
            'outstanding_amount' => 'nullable|numeric|min:0',
            'opened_at' => 'required|date',
            'status' => 'required|string|in:open,closed,pending',
            'description' => 'nullable|string',
            
            'court_info' => 'nullable|string',
            // Auction fields (secured loan recovery)
            'auction_round'   => 'nullable|in:1,2',
            'auction_date'    => 'required_with:auction_round|date',
            'auction_result'  => 'required_with:auction_round|in:sold,ORGANIZATION_acquired,failed',
            'sold_amount'     => 'nullable|numeric|min:0',
            'auction_notes'   => 'nullable|string',
            
            // Advisory specific fields
            'advisory_type' => 'required_if:type,advisory|string|in:written_advice,document_review',
            'subject' => 'required_if:type,advisory|string|max:255',
            'request_date' => 'required_if:type,advisory|date',
            'is_own_motion' => 'nullable|boolean',
            'reference_number' => 'nullable|string|max:255',
            'requesting_department' => 'nullable|string|max:255',
            'work_unit_advised' => 'nullable|string|max:255',
            'review_notes' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'stakeholders' => 'required_if:type,advisory|array|min:1',
            'stakeholders.*.name' => 'required_with:stakeholders|string|max:255',
            'stakeholders.*.type' => 'required_with:stakeholders|string|in:requester,reviewer,approver,recipient',
            'stakeholders.*.email' => 'nullable|email|max:255',
            'stakeholders.*.phone' => 'nullable|string|max:20',
            'stakeholders.*.organization' => 'nullable|string|max:255',
            
            // Nested plaintiffs and defendants arrays (dynamic form inputs)
            'plaintiffs' => 'nullable|array',
            'plaintiffs.*.name' => 'required_with:plaintiffs|string|max:255',
            'plaintiffs.*.contact_number' => 'nullable|string|max:255',
            'plaintiffs.*.address' => 'nullable|string|max:255',
            'plaintiffs.*.email' => 'nullable|email',
            'defendants' => 'nullable|array',
            'defendants.*.name' => 'required_with:defendants|string|max:255',
            'defendants.*.contact_number' => 'nullable|string|max:255',
            'defendants.*.address' => 'nullable|string|max:255',
            'defendants.*.email' => 'nullable|email',
        ]);

        try {
            // Start a database transaction
            DB::beginTransaction();

            // Map the type to case_type_id
            $caseTypeMap = [
                'clean_loan' => 1,
                'labor' => 2,
                'civil' => 3,
                'criminal' => 4,
                'secured_loan' => 5,
                'advisory' => 6,
            ];

            $caseTypeId = $caseTypeMap[$validated['type']] ?? 1; // Default to 1 if not found

            // Create the case with all fields
            $caseData = [
                'case_type_id' => $caseTypeId,
                'file_number' => $validated['file_number'] ?? null,
                'title' => $validated['title'] ?? ('Case #' . ($validated['file_number'] ?? '') . ' - ' . ucfirst(str_replace('_', ' ', $validated['type']))),
                'claimed_amount' => $validated['claimed_amount'] ?? 0,
                'lawyer_id' => auth()->id(),
                'created_by' => auth()->id(),
                'status' => $validated['status'] ?? 'open',
                'opened_at' => $validated['opened_at'] ?? now(),
                'description' => $validated['description'] ?? null,
                'branch_id' => $validated['branch_id'] ?? null,
                'work_unit_id' => $validated['work_unit_id'] ?? null,
                'court_file_number' => $validated['court_file_number'] ?? null,
                'company_file_number' => $validated['company_file_number'] ?? null,
                'outstanding_amount' => $validated['outstanding_amount'] ?? 0,
                'court_info' => $validated['court_info'] ?? null,
            ];

            // Create the case
            $case = CaseFile::create($caseData);

            // Save plaintiffs (array or single)
            $plaintiffs = $request->input('plaintiffs', []);
            foreach ($plaintiffs as $p) {
                if (!empty($p['name'])) {
                    $case->plaintiffs()->create([
                        'name'           => $p['name'],
                        'contact_number' => $p['contact_number'] ?? null,
                        'address'        => $p['address'] ?? null,
                        'email'          => $p['email'] ?? null,
                    ]);
                }
            }
            // Single plaintiff fallback
            if ($request->filled('plaintiff')) {
                $case->plaintiffs()->create([
                    'name'           => $validated['plaintiff'],
                    'contact_number' => $request->input('plaintiff_contact'),
                    'address'        => $request->input('plaintiff_address'),
                    'email'          => $request->input('plaintiff_email'),
                ]);
            }

            // Save defendants (array or single)
            $defendants = $request->input('defendants', []);
            foreach ($defendants as $d) {
                if (!empty($d['name'])) {
                    $case->defendants()->create([
                        'name'           => $d['name'],
                        'contact_number' => $d['contact_number'] ?? null,
                        'address'        => $d['address'] ?? null,
                        'email'          => $d['email'] ?? null,
                    ]);
                }
            }
            // Single defendant fallback
            if ($request->filled('defendant')) {
                $case->defendants()->create([
                    'name'           => $validated['defendant'],
                    'contact_number' => $request->input('defendant_contact'),
                    'address'        => $request->input('defendant_address'),
                    'email'          => $request->input('defendant_email'),
                ]);
            }




            // Handle case type specific data
            $this->handleCaseTypeData($case, $request);

            // Notify all supervisors for approval to proceed (not closure)
            $supervisors = \App\Models\User::role('supervisor')->get();
            foreach ($supervisors as $supervisor) {
                $supervisor->notify(new \App\Notifications\CaseApprovalRequested($case));
            }

            // Do NOT set status to closed or trigger closure logic here

            // Commit the transaction
            DB::commit();

            // Redirect to case show page with success message
            return redirect()->route('lawyer.cases.show', $case->id)
                ->with('success', 'Case created successfully!');

        } catch (\Exception $e) {
            // Rollback the transaction on error
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            
            Log::error('Error creating case: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);
            
            return back()->withInput()
                ->with('error', 'Error creating case. Please try again or contact support if the problem persists.');
        }
    }

    // Request closure approval for a case
    public function requestClosure(CaseFile $case)
    {
        // Only the assigned lawyer can request closure
        if ($case->lawyer_id !== auth()->id()) {
            abort(403, 'You are not authorized to request closure for this case.');
        }

        // Set the closure_requested_at flag
        $case->closure_requested_at = now();
        $case->save();

        // Notify all supervisors
        $supervisors = \App\Models\User::role('supervisor')->get();
        foreach ($supervisors as $supervisor) {
            $supervisor->notify(new \App\Notifications\CaseClosureApprovalRequested($case));
        }

        return redirect()->route('lawyer.cases.show', $case->id)
            ->with('success', 'Closure approval requested. Supervisors have been notified.');
    }
    
    /**
     * Handle type-specific case data
     */
    protected function handleTypeSpecificData(CaseFile $case, Request $request)
    {
        // Add type-specific handling here
        // Example:
        // if ($case->type === 'clean_loan') {
        //     // Handle clean loan specific data
        // }
    }
    
    /**
     * Handle case type specific data
     */
    protected function handleCaseTypeData(CaseFile $case, Request $request)
    {
        $typeSlug = $request->input('type');
        $map = [
            'clean_loan'   => 'handleCleanLoanData',
            'secured_loan' => 'handleSecuredLoanData',
            'labor'        => 'handleLaborData',
            'civil'        => 'handleCivilData',
            'criminal'     => 'handleCriminalData',
            'advisory'     => 'handleAdvisoryData',
        ];
        if (isset($map[$typeSlug]) && method_exists($this, $map[$typeSlug])) {
            $this->{$map[$typeSlug]}($case, $request);
        }
    }
    
    /**
     * Handle clean loan case data
     */
    protected function handleCleanLoanData(CaseFile $case, Request $request)
    {
        $case->cleanLoanRecovery()->updateOrCreate(['case_file_id' => $case->id], [
            'branch_id' => $request->input('branch_id'),
            'work_unit_id' => $request->input('work_unit_id'),
            'court_file_number' => $request->input('court_file_number'),
            'company_file_number' => $request->input('company_file_number'),
            'customer_name'     => $request->input('customer_name'),
            'outstanding_amount' => $request->input('outstanding_amount', 0),
            'claimed_amount' => $request->input('claimed_amount', 0),
            'loan_amount'     => $request->input('loan_amount', $request->input('claimed_amount', 0)),

            'recovered_amount' => $request->input('recovered_amount', 0),
            'court_name' => $request->input('court_name'),
            'lawyer_id' => $request->input('lawyer_id'),
            'court_info' => $request->input('court_info'),
        ]);
    }
    
    /**
     * Handle secured loan case data
     */
    protected function mapClosureType(?string $label): ?string
    {
        return match($label) {
            'Fully Paid'        => 'fully_repaid',
            'Fully Repaid'      => 'fully_repaid',
            'Collateral Sold'   => 'collateral_sold',
            'Restructured'      => 'restructured',
            'Settlement'        => 'settlement',
            'Acquired by ORGANIZATION'  => 'collateral_acquired',
            default             => $label ? Str::snake($label) : null,
        };
    }

    protected function handleSecuredLoanData(CaseFile $case, Request $request)
    {
        $payload = [
            'case_file_id'        => $case->id,
            'company_file_number' => $request->input('company_file_number'),
            'customer_name'       => $request->input('customer_name'),
            'outstanding_amount'  => $request->input('outstanding_amount', 0),
            'claimed_amount'      => $request->input('claimed_amount', 0),
            'loan_amount'         => $request->input('loan_amount', $request->input('claimed_amount', 0)),
            'recovered_amount'    => $request->input('recovered_amount', 0),
            // collateral & auction
            'foreclosure_notice_date' => $request->input('foreclosure_notice_date'),
            'collateral_description'  => $request->input('collateral_description'),
            'collateral_value'        => $request->input('collateral_value'),
            'first_auction_held'      => $request->boolean('first_auction_held'),
            'second_auction_held'     => $request->boolean('second_auction_held'),
            // closure
            'closure_type'  => $this->mapClosureType($request->input('closure_type')), 
            'closure_notes' => $request->input('other_notes'),
            
            
            
        ];

        // Handle file uploads (paths stored elsewhere)
        if ($request->hasFile('collateral_estimation')) {
            $payload['collateral_estimation_path'] = $request->file('collateral_estimation')->store('secured-loan/collateral', 'public');
        }
        if ($request->hasFile('warning_doc')) {
            $payload['warning_doc_path'] = $request->file('warning_doc')->store('secured-loan/warnings', 'public');
        }
        if ($request->hasFile('auction_publication')) {
            $payload['auction_publication_path'] = $request->file('auction_publication')->store('secured-loan/auctions', 'public');
        }

        $securedLoan = $case->securedLoanRecovery()->updateOrCreate(['case_file_id' => $case->id], $payload);

        // Handle auction info (round, optional extra fields)
        $auctionRound = $request->input('auction_round');
        if ($auctionRound) {
            $securedLoan->auctions()->updateOrCreate(
                ['round' => $auctionRound],
                [
                    'auction_date' => $request->input('auction_date'),
                    'result'       => $request->input('auction_result'),
                    'sold_amount'  => $request->input('sold_amount'),
                    'notes'        => $request->input('auction_notes'),
                ]
            );
        }
    }
    
    /**
     * Handle labor case data
     */
    protected function handleLaborData(CaseFile $case, Request $request)
    {
        $data = [
            'claim_type'          => $request->input('claim_type'),
            'claim_amount'        => $request->input('claim_amount', 0),
            'claimed_amount'      => $request->input('claimed_amount', 0),
            'claim_material_desc' => $request->input('claim_material_desc'),
            'recovered_amount'    => $request->input('recovered_amount', 0),
            'outstanding_amount'  => $request->input('outstanding_amount', 0),
            'early_settled'       => $request->input('early_settled', false),
            'execution_opened_at' => $request->input('execution_opened_at'),
            'closed_at'           => $request->input('closed_at'),
            'closed_by'           => $request->input('closed_by'),
            'court_file_number'   => $request->input('court_file_number'),
        ];
        \Log::info('Saving labor litigation case', [
            'case_file_id' => $case->id,
            'data' => $data
        ]);
        $case->laborLitigation()->updateOrCreate(['case_file_id' => $case->id], $data);
    }
    
    /**
     * Handle other civil litigation case data
     */
    protected function handleCivilData(CaseFile $case, Request $request)
    {
        $case->otherCivilLitigation()->updateOrCreate(['case_file_id' => $case->id], [
            'court_name' => $request->input('court_name'),
            'court_file_number' => $request->input('court_file_number'),
            'outstanding_amount' => $request->input('outstanding_amount', 0),
            'claimed_amount' => $request->input('claimed_amount', 0),
            'loan_amount'     => $request->input('loan_amount', $request->input('claimed_amount', 0)),

            'recovered_amount' => $request->input('recovered_amount', 0),
        ]);
    }

    protected function handleCriminalData(CaseFile $case, Request $request)
    {
        $case->criminalLitigation()->updateOrCreate(['case_file_id' => $case->id], [
            'police_station' => $request->input('police_station'),
            'police_file_number' => $request->input('police_file_number'),
            'outstanding_amount' => $request->input('outstanding_amount', 0),
            'claimed_amount' => $request->input('claimed_amount', 0),
            'loan_amount'     => $request->input('loan_amount', $request->input('claimed_amount', 0)),

            'recovered_amount' => $request->input('recovered_amount', 0),
        ]);
    }

    /**
     * Handle legal advisory case data
     */
    protected function handleAdvisoryData(CaseFile $case, Request $request)
    {
        // Create the legal advisory case record
        $advisoryData = [
            'case_file_id' => $case->id,
            'advisory_type' => $request->input('advisory_type', 'written_advice'),
            'subject' => $request->input('subject'),
            'description' => $request->input('description'),
            'assigned_lawyer_id' => auth()->id(),
            'request_date' => $request->input('request_date'),
            'is_own_motion' => $request->boolean('is_own_motion', false),
            'reference_number' => $request->input('reference_number'),
            'status' => 'draft',
        ];

        // Handle document upload for document review
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('advisory-documents', 'public');
            $advisoryData['document_path'] = $documentPath;
        }

        // Add review notes if provided
        if ($request->input('review_notes')) {
            $advisoryData['review_notes'] = $request->input('review_notes');
        }

        $advisory = LegalAdvisoryCase::create($advisoryData);

        // Handle stakeholders
        $stakeholders = $request->input('stakeholders', []);
        foreach ($stakeholders as $stakeholder) {
            if (!empty($stakeholder['name']) && !empty($stakeholder['type'])) {
                $advisory->stakeholders()->create([
                    'name' => $stakeholder['name'],
                    'type' => $stakeholder['type'],
                    'email' => $stakeholder['email'] ?? null,
                    'phone' => $stakeholder['phone'] ?? null,
                    'organization' => $stakeholder['organization'] ?? null,
                ]);
            }
        }

        // Update case file with additional advisory-specific data
        $case->update([
            'requesting_department' => $request->input('requesting_department'),
            'work_unit_advised' => $request->input('work_unit_advised'),
        ]);
    }
    
    // Add similar methods for other case types as needed
    
    /**
     * Display the specified case.
     */
    /**
     * Show the form for editing the specified case.
     */
    public function edit(CaseFile $case)
    {
        $this->authorize('update', $case);
        $case->load([
            'caseType',
            'plaintiffs',
            'defendants',
            'evidences',
            // Eager load all possible case-type specific relations so edit views have full data
            'securedLoanRecovery.auctions',
            'cleanLoanRecovery',
            'laborLitigation',
            'otherCivilLitigation',
            'criminalLitigation',
            'legalAdvisory.stakeholders', // include nested stakeholders for advisory cases
        ]);
        $caseType = strtolower(str_replace(' ', '_', $case->caseType->name));
        $caseTypeData = null;
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
        $branches = \App\Models\Branch::where('is_active', true)->get();
        $workUnits = \App\Models\WorkUnit::where('is_active', true)->get();
        $lawyers = \App\Models\User::where('role', 'lawyer')->where('is_active', true)->get();

        // Map plaintiffs and defendants for Blade @json usage
        $plaintiffs = $case->plaintiffs->map(function($p) {
            return [
                'name' => $p->name,
                'contact_number' => $p->contact_number,
                'email' => $p->email,
                'address' => $p->address,
            ];
        })->toArray();
        $defendants = $case->defendants->map(function($d) {
            return [
                'name' => $d->name,
                'contact_number' => $d->contact_number,
                'email' => $d->email,
                'address' => $d->address,
            ];
        })->toArray();

        $viewName = 'lawyer.cases.edit_' . $caseType;
        if (view()->exists($viewName)) {
            return view($viewName, [
                'case' => $case,
                'caseTypeData' => $caseTypeData,
                'branches' => $branches,
                'workUnits' => $workUnits,
                'lawyers' => $lawyers,
                'plaintiffs' => $plaintiffs,
                'defendants' => $defendants,
            ]);
        }
        return view('lawyer.cases.edit', [
            'case' => $case,
            'caseTypeData' => $caseTypeData,
            'branches' => $branches,
            'workUnits' => $workUnits,
            'lawyers' => $lawyers,
            'plaintiffs' => $plaintiffs,
            'defendants' => $defendants,
        ]);
    }

    /**
     * Update the specified case in storage.
     */
    public function update(Request $request, CaseFile $case)
    {
        // Authorize that the lawyer can update this case
        $this->authorize('update', $case);

        // Start database transaction
        DB::beginTransaction();

        try {
            // Validate the request
            $validated = $request->validate([
                'title' => 'sometimes|nullable|string|max:255',
                'status' => 'sometimes|nullable|in:open,closed,pending',
                'claimed_amount' => 'required|numeric|min:0',
                'outstanding_amount' => 'nullable|numeric|min:0',
                'opened_at' => 'sometimes|nullable|date',
                'closed_at' => 'nullable|date|after_or_equal:opened_at',
                'description' => 'nullable|string',
                'court_name' => 'nullable|string|max:255',
                'plaintiffs' => 'sometimes|array',
                'plaintiffs.*.name' => 'sometimes|required|string|max:255',
                'plaintiffs.*.contact_number' => 'nullable|string|max:20',
                'plaintiffs.*.email' => 'nullable|email|max:255',
                'plaintiffs.*.address' => 'nullable|string|max:500',
                'defendants' => 'sometimes|array',
                'defendants.*.name' => 'sometimes|required|string|max:255',
                'defendants.*.contact_number' => 'nullable|string|max:20',
                'defendants.*.email' => 'nullable|email|max:255',
                'defendants.*.address' => 'nullable|string|max:500',
                'edit_description' => 'sometimes|nullable|string|min:5',
                'recovery_docs' => 'nullable|array',
                'recovery_docs.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:8192',
            ]);

            // Update the case
            $case->update([
                'title' => $validated['title'] ?? $case->title,
                'status' => $validated['status'] ?? $case->status,
                'claimed_amount' => $validated['claimed_amount'] ?? null,
                'outstanding_amount' => $validated['outstanding_amount'] ?? null,
                'opened_at' => $validated['opened_at'] ?? $case->opened_at,
                'closed_at' => $validated['closed_at'] ?? null,
                'description' => $validated['description'] ?? null,
                'court_name' => $validated['court_name'] ?? null,
            ]);

            // Sync plaintiffs
            $plaintiffs = [];
            foreach ($request->input('plaintiffs', []) as $plaintiffData) {
                $plaintiffs[] = new Plaintiff([
                    'name' => $plaintiffData['name'],
                    'contact_number' => $plaintiffData['contact_number'] ?? null,
                    'email' => $plaintiffData['email'] ?? null,
                    'address' => $plaintiffData['address'] ?? null,
                ]);
            }
            $case->plaintiffs()->delete(); // Remove existing plaintiffs
            $case->plaintiffs()->saveMany($plaintiffs);

            // Sync defendants
            $defendants = [];
            foreach ($request->input('defendants', []) as $defendantData) {
                $defendants[] = new Defendant([
                    'name' => $defendantData['name'],
                    'contact_number' => $defendantData['contact_number'] ?? null,
                    'email' => $defendantData['email'] ?? null,
                    'address' => $defendantData['address'] ?? null,
                ]);
            }
            $case->defendants()->delete(); // Remove existing defendants
            $case->defendants()->saveMany($defendants);

            // Handle case type specific data
            $this->handleCaseTypeData($case, $request);

            // Handle evidence file uploads
            if ($request->hasFile('recovery_docs')) {
                foreach ($request->file('recovery_docs') as $file) {
                    if ($file && $file->isValid()) {
                        $storedName = $file->store('evidences', 'public');
                        $fileContents = file_get_contents($file->getRealPath());
                        $hash = hash('sha256', $fileContents);
                        $case->evidences()->create([
                            'original_name' => $file->getClientOriginalName(),
                            'stored_name' => basename($storedName),
                            'mime_type' => $file->getClientMimeType(),
                            'size' => $file->getSize(),
                            'uploaded_by' => auth()->id(),
                            'hash' => $hash,
                        ]);
                    }
                }
            }

            // Log the edit description if provided
            if (!empty($validated['edit_description'] ?? null)) {
                if (method_exists($case, 'actionLogs')) {
                    $case->actionLogs()->create([
                        'user_id' => auth()->id(),
                        'action' => 'edit',
                        'description' => $validated['edit_description'],
                    ]);
                } else {
                    \Log::info('Case edit description: ' . $validated['edit_description'], [
                        'case_id' => $case->id,
                        'user_id' => auth()->id(),
                    ]);
                }
            }

            // Commit the transaction
            DB::commit();

            return redirect()->route('lawyer.cases.show', $case)
                ->with('success', 'Case updated successfully!');

        } catch (\Exception $e) {
            // Rollback the transaction on error
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            
            Log::error('Error updating case: ' . $e->getMessage(), [
                'exception' => $e,
                'case_id' => $case->id,
                'input' => $request->all()
            ]);
            
            return back()->withInput()
                ->with('error', 'Error updating case. Please try again or contact support if the problem persists.');
        }
    }

    /**
     * Display the specified case.
     */
    public function show(CaseFile $case)
    {
        // Check if the lawyer can access this case
        $this->authorize('view', $case);
        
        // Mark related unread notifications as read for the authenticated lawyer
        if ($user = auth()->user()) {
            $user->unreadNotifications()
                ->where('data->case_id', $case->id)
                ->update(['read_at' => now()]);
        }
        // Eager load all necessary relationships
        $case->load([
            'caseType',
            'court',
            'lawyer',
            'branch',
            'workUnit',
            'plaintiffs',
            'defendants',
            'litigation',
            'laborLitigation',
            'otherCivilLitigation',
            'criminalLitigation',
            'securedLoanRecovery','securedLoanRecovery.auctions','evidences',
            'legalAdvisory',
            'progressUpdates' => function($query) {
                $query->latest()->take(5);
            },
            'appeals' => function($q) {
                $q->orderBy('created_at');
            },
            'appointments' => function($query) {
                $query->upcoming()->orderBy('appointment_date')->take(5);
            }
        ]);

        // Get the specific case type data based on the case type
        $caseTypeData = null;
        $caseType = strtolower($case->caseType->name ?? '');
        switch ($caseType) {
            case 'clean loan recovery':
                $caseTypeData = $case->cleanLoanRecovery;
                break;
            case 'secured loan recovery':
                $caseTypeData = $case->securedLoanRecovery;
                break;
            case 'labor litigation':
                $caseTypeData = $case->laborLitigation;
                break;
            case 'other civil litigation':
                $caseTypeData = $case->otherCivilLitigation;
                break;
            case 'criminal litigation':
                $caseTypeData = $case->criminalLitigation;
                break;
            case 'legal advisory':
                $caseTypeData = $case->legalAdvisory;
                break;
        }

        // Determine per-type view
        $slug = Str::slug($case->caseType->name, '_');
        $viewPath = view()->exists("lawyer.cases.types.$slug.show") ? "lawyer.cases.types.$slug.show" : 'lawyer.cases.show';

        return view($viewPath, [
            'case' => $case,
            'caseTypeData' => $caseTypeData,
        ]);
    }

    /**
     * Show the form to create a progress update for a case.
     */
    public function createProgress(CaseFile $case)
    {
        $this->authorize('update', $case);
        return view('lawyer.progress.create', compact('case'));
    }

    /**
     * Store a new progress update for a case.
     */
    public function storeProgress(Request $request, CaseFile $case)
    {
        $this->authorize('update', $case);
        
        $request->validate([
            'notes' => 'required|string',
            'status' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048',
        ]);

        $progress = $case->progressUpdates()->create([
            'updated_by' => auth()->id(),
            'notes' => $request->notes,
            'status' => $request->status,
        ]);

        if ($request->hasFile('attachment')) {
            // Store the file and save the path
            $path = $request->file('attachment')->store('attachments', 'public');
            $progress->update(['attachment_path' => $path]);
        }

        return redirect()->route('lawyer.cases.show', $case)->with('success', 'Progress updated successfully.');
    }

    /**
     * Show all progress updates for the lawyer's cases.
     */
    public function progress()
    {
        $timeline = auth()->user()->progressUpdates()
            ->with('caseFile:id,file_number') // Eager load only necessary fields
            ->latest()
            ->paginate(15);

        return view('lawyer.progress', ['timeline' => $timeline]);
    }

    /**
     * Show the edit history for a case.
     */
    public function editHistory(CaseFile $case)
    {
        $this->authorize('view', $case);
        
        // Logs on primary CaseFile
        $caseLogs = $case->actionLogs()
            ->whereIn('action', ['created', 'updated'])
            ->get();

        // Collect logs from related case-type model if it exists
        $relatedLogs = collect();
        if ($case->relationLoaded('securedLoanRecovery') || $case->securedLoanRecovery) {
            $relatedLogs = $case->securedLoanRecovery->actionLogs()
                ->whereIn('action', ['created', 'updated'])
                ->get();
        }
        // TODO: add other case-type relations as needed

        $editLogs = $caseLogs->concat($relatedLogs)->sortByDesc('created_at');
        return view('lawyer.cases.edit_history', [
            'case' => $case,
            'editLogs' => $editLogs,
        ]);
    }
}







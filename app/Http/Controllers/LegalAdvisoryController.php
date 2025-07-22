<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use App\Models\LegalAdvisoryCase;
use App\Models\DocumentVersion;
use App\Models\AdvisoryStakeholder;
// ProgressUpdate model not needed for initial implementation
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class LegalAdvisoryController extends Controller
{
    /**
     * Create a new legal advisory case.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'advisory_type' => ['required', Rule::in(['written_advice', 'document_review'])],
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_own_motion' => 'boolean',
            'request_date' => 'required|date',
            'claimed_amount' => 'nullable|numeric|min:0',
            'stakeholders' => 'required|array',
            'stakeholders.*.name' => 'required|string',
            'stakeholders.*.type' => ['required', Rule::in(['requester', 'reviewer', 'approver', 'recipient'])],
            'stakeholders.*.email' => 'nullable|email',
            'stakeholders.*.phone' => 'nullable|string',
            'stakeholders.*.organization' => 'nullable|string',
        ]);

        $operation = function () use ($data, $request) {
            // Create case file
            $caseFile = CaseFile::create([
                'file_number' => 'ADV-' . strtoupper(Str::random(8)),
                'title' => $data['subject'],
                'description' => $data['description'] ?? null,
                'case_type_id' => 6, // Legal Advisory
                'status' => 'Open',
                'opened_at' => now(),
                'lawyer_id' => $request->user()->id,
                'created_by' => $request->user()->id,
                'claimed_amount' => $data['claimed_amount'] ?? 0,
            ]);

            // Create advisory case
            $advisory = LegalAdvisoryCase::create([
                'case_file_id' => $caseFile->id,
                'advisory_type' => $data['advisory_type'],
                'subject' => $data['subject'],
                'description' => $data['description'] ?? null,
                'assigned_lawyer_id' => $request->user()->id,
                'request_date' => $data['request_date'],
                'is_own_motion' => $data['is_own_motion'] ?? false,
                'status' => 'draft',
            ]);

            // Add stakeholders
            foreach ($data['stakeholders'] as $stakeholder) {
                $advisory->stakeholders()->create($stakeholder);
            }

            // Log action in case file
            $caseFile->update([
                'status' => 'draft',
                'last_updated_by' => auth()->id()
            ]);

                return response()->json($advisory->load(['stakeholders', 'caseFile']), 201);
        };

        // Only wrap in a DB transaction if none is active to avoid nested SAVEPOINT issues
        return DB::transactionLevel() > 0 ? $operation() : DB::transaction($operation);
    }

    /**
     * Upload document for review or as part of advisory.
     */
    public function uploadDocument(Request $request, LegalAdvisoryCase $advisory)
    {
        $this->authorize('update', $advisory->caseFile);

        $data = $request->validate([
            'document' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'version_number' => 'required|string',
            'changes' => 'nullable|string',
        ]);

        $path = $request->file('document')->store('advisory-documents');

        $document = $advisory->documentVersions()->create([
            'version_number' => $data['version_number'],
            'document_path' => $path,
            'changes' => $data['changes'] ?? null,
            'uploaded_by' => $request->user()->id,
        ]);

        // Update advisory with latest document if it's the first one
        if ($advisory->documentVersions()->count() === 1) {
            $advisory->update([
                'document_path' => $path,
                'status' => 'in_review',
            ]);
        }

        return response()->json($document, 201);
    }

    /**
     * Submit review/advice.
     */
    public function submitReview(Request $request, LegalAdvisoryCase $advisory)
    {
        $this->authorize('update', $advisory->caseFile);

        $data = $request->validate([
            'review_notes' => 'required|string',
            'document' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $operation = function () use ($advisory, $data, $request) {
            $updates = [
                'review_notes' => $data['review_notes'],
                'status' => 'in_review',
            ];

            if (isset($data['document'])) {
                $path = $request->file('document')->store('advisory-reviews');
                $updates['reviewed_document_path'] = $path;
            }

            $advisory->update($updates);

            // Log progress
            ProgressUpdate::create([
                'case_file_id' => $advisory->case_file_id,
                'status' => 'Review Submitted',
                'notes' => 'Review submitted for ' . $advisory->advisory_type,
                'updated_by' => $request->user()->id,
            ]);

                        return response()->json($advisory->fresh());
        };
        return DB::transactionLevel() > 0 ? $operation() : DB::transaction($operation);
    }

    /**
     * Approve and close the advisory case.
     */
    public function approve(Request $request, LegalAdvisoryCase $advisory)
    {
        $this->authorize('update', $advisory->caseFile);

        $data = $request->validate([
            'notes' => 'nullable|string',
        ]);

        $operation = function () use ($advisory, $data, $request) {
            $advisory->update([
                'status' => 'approved',
                'approved_by' => $request->user()->id,
                'approved_at' => now(),
            ]);

            // Close the case
            $advisory->update([
                'status' => 'completed',
                'closed_at' => now(),
                'closed_by' => $request->user()->id,
                'closure_notes' => $data['notes'] ?? 'Case completed and approved',
            ]);

            $advisory->caseFile->update(['status' => 'Closed']);

            // Log progress
            ProgressUpdate::create([
                'case_file_id' => $advisory->case_file_id,
                'status' => 'Case Approved & Closed',
                'notes' => $data['notes'] ?? 'Advisory case completed',
                'updated_by' => $request->user()->id,
            ]);

                        return response()->json($advisory->fresh());
        };
        return DB::transactionLevel() > 0 ? $operation() : DB::transaction($operation);
    }

    /**
     * Add a progress update.
     */
    public function addProgress(Request $request, LegalAdvisoryCase $advisory)
    {
        $this->authorize('update', $advisory->caseFile);

        $data = $request->validate([
            'status' => 'required|string',
            'notes' => 'required|string',
        ]);

        $update = ProgressUpdate::create([
            'case_file_id' => $advisory->case_file_id,
            'status' => $data['status'],
            'notes' => $data['notes'],
            'updated_by' => $request->user()->id,
        ]);

        return response()->json($update, 201);
    }

    /**
     * Add or update stakeholders.
     */
    public function updateStakeholders(Request $request, LegalAdvisoryCase $advisory)
    {
        $this->authorize('update', $advisory->caseFile);

        $data = $request->validate([
            'stakeholders' => 'required|array',
            'stakeholders.*.id' => 'nullable|exists:advisory_stakeholders,id',
            'stakeholders.*.name' => 'required|string',
            'stakeholders.*.type' => ['required', Rule::in(['requester', 'reviewer', 'approver', 'recipient'])],
            'stakeholders.*.email' => 'nullable|email',
            'stakeholders.*.phone' => 'nullable|string',
            'stakeholders.*.organization' => 'nullable|string',
        ]);

        $stakeholderIds = [];

        foreach ($data['stakeholders'] as $stakeholderData) {
            if (isset($stakeholderData['id'])) {
                // Update existing stakeholder
                $stakeholder = $advisory->stakeholders()->findOrFail($stakeholderData['id']);
                $stakeholder->update($stakeholderData);
                $stakeholderIds[] = $stakeholder->id;
            } else {
                // Create new stakeholder
                $stakeholder = $advisory->stakeholders()->create($stakeholderData);
                $stakeholderIds[] = $stakeholder->id;
            }
        }

        // Remove stakeholders not in the list
        $advisory->stakeholders()->whereNotIn('id', $stakeholderIds)->delete();

        return response()->json($advisory->stakeholders);
    }
}







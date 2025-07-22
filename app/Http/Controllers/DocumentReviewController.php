<?php

namespace App\Http\Controllers;

use App\Models\DocumentReview;
use App\Models\DocumentVersion;
use App\Models\CaseFile;
use App\Models\AdvisoryStakeholder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class DocumentReviewController extends Controller
{
    /**
     * Submit a document for review
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function __construct()
    {
        $this->authorizeResource(DocumentReview::class, 'documentReview');
    }
    


    /**
     * Submit a document for review
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', DocumentReview::class);
        
        \Log::info('Starting document review submission', ['user_id' => auth()->id()]);
        
        try {
            $validated = $request->validate([
                'subject' => 'required|string|max:255',
                'description' => 'required|string',
                'document' => 'required|file|mimes:pdf,doc,docx|max:10240',
                'deadline' => 'required|date|after:today',
                'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
                'stakeholders' => 'required|array|min:1',
                'stakeholders.*.name' => 'required|string',
                'stakeholders.*.email' => 'required|email',
                'stakeholders.*.type' => ['required', Rule::in(['client', 'lawyer', 'supervisor'])]
            ]);

            // Start database transaction
            \Log::info('Starting database transaction for document review');
            return \DB::transaction(function () use ($validated, $request) {
                $user = Auth::user();
                $file = $request->file('document');
                $filePath = $file->store('document-reviews', 'documents');

                // Create case file
                $caseFile = CaseFile::create([
                    'file_number' => 'DOC-' . strtoupper(Str::random(8)),
                    'title' => $validated['subject'],
                    'description' => $validated['description'],
                    'case_type_id' => 6, // Legal Advisory
                    'status' => 'under_review',
                    'opened_at' => now(),
                    'lawyer_id' => $user->id,
                    'created_by' => $user->id,
                ]);

                // Create document review
                $review = DocumentReview::create([
                    'case_file_id' => $caseFile->id,
                    'document_path' => $filePath,
                    'original_filename' => $file->getClientOriginalName(),
                    'deadline' => $validated['deadline'],
                    'priority' => $validated['priority'],
                    'status' => 'under_review',
                    'assigned_to' => null, // Will be assigned by supervisor
                ]);

                // Save initial version
                try {
                    $version = $review->versions()->create([
                        'version' => 1,
                        'file_path' => $filePath,
                        'notes' => 'Initial submission',
                        'uploaded_by' => $user->id,
                    ]);
                    \Log::info('Document version created', ['version_id' => $version->id]);
                } catch (\Exception $e) {
                    \Log::error('Error creating document version', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }

                // Add stakeholders
                foreach ($validated['stakeholders'] as $stakeholder) {
                    $review->stakeholders()->create([
                        'name' => $stakeholder['name'],
                        'email' => $stakeholder['email'],
                        'type' => $stakeholder['type'],
                        'user_id' => $user->id,
                    ]);
                }

                // Load relationships for response
                $review->load(['versions', 'stakeholders', 'caseFile']);

                return response()->json([
                    'id' => $review->id,
                    'case_file_id' => $review->case_file_id,
                    'advisory_type' => 'document_review',
                    'subject' => $review->caseFile->title,
                    'description' => $review->caseFile->description,
                    'status' => $review->status,
                    'priority' => $review->priority,
                    'deadline' => $review->deadline->toDateString(),
                    'document_path' => $review->document_path,
                    'original_filename' => $review->original_filename,
                    'versions' => $review->versions->map(function ($version) {
                        return [
                            'id' => $version->id,
                            'version' => $version->version,
                            'file_path' => $version->file_path,
                            'notes' => $version->notes,
                            'uploaded_at' => $version->created_at->toDateTimeString(),
                            'uploaded_by' => $version->uploaded_by,
                        ];
                    }),
                    'stakeholders' => $review->stakeholders->map(function ($stakeholder) {
                        return [
                            'id' => $stakeholder->id,
                            'name' => $stakeholder->name,
                            'email' => $stakeholder->email,
                            'type' => $stakeholder->type,
                        ];
                    }),
                    'created_at' => $review->created_at->toDateTimeString(),
                    'updated_at' => $review->updated_at->toDateTimeString(),
                ], 201);
            });
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in DocumentReviewController@store: ' . $e->getMessage());
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in DocumentReviewController@store: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to submit document for review',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Approve or reject a document review
     * 
     * @param Request $request
     * @param DocumentReview $documentReview
     * @return JsonResponse
     */
    /**
     * Approve or reject a document review
     * 
     * @param Request $request
     * @param DocumentReview $documentReview
     * @return JsonResponse
     */
    public function approve(Request $request, DocumentReview $documentReview): JsonResponse
    {
        $this->authorize('approve', $documentReview);
        
        try {
            // Only supervisors can approve/reject
            if (!Auth::user()->hasRole('supervisor')) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $validated = $request->validate([
                'status' => ['required', Rule::in(['approved', 'rejected', 'needs_revision'])],
                'comments' => 'required|string',
                'next_steps' => 'required_if:status,needs_revision|string|nullable',
            ]);

            // Start database transaction
            return \DB::transaction(function () use ($validated, $documentReview) {
                $documentReview->update([
                    'status' => $validated['status'],
                    'review_comments' => $validated['comments'],
                    'reviewed_at' => now(),
                    'reviewed_by' => Auth::id(),
                ]);

                // Update case file status
                $documentReview->caseFile->update(['status' => $validated['status']]);
                
                // Log the approval action
                Log::info("Document review {$documentReview->id} {$validated['status']} by user " . Auth::id());

                // Return a clean response with only necessary data
                return response()->json([
                    'id' => $documentReview->id,
                    'status' => $documentReview->status,
                    'review_comments' => $documentReview->review_comments,
                    'reviewed_at' => $documentReview->reviewed_at->toDateTimeString(),
                    'reviewed_by' => $documentReview->reviewed_by,
                    'case_file' => [
                        'id' => $documentReview->caseFile->id,
                        'status' => $documentReview->caseFile->status,
                    ],
                    'message' => "Document review has been {$validated['status']} successfully"
                ]);
            });
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in DocumentReviewController@approve: ' . $e->getMessage());
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in DocumentReviewController@approve: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to process document review approval',
                'error' => $e->getMessage(),
            ], 500);
        }

        // Notify stakeholders if needed
        // TODO: Implement notification system

        return response()->json($review->load('caseFile'));
    }

    /**
     * Upload a new version of a document
     * 
     * @param Request $request
     * @param DocumentReview $documentReview
     * @return JsonResponse
     */
    public function uploadVersion(Request $request, DocumentReview $documentReview): JsonResponse
    {
        try {
            // Only the document owner or assigned lawyer can upload new versions
            if (Auth::id() !== $documentReview->caseFile->lawyer_id && 
                Auth::id() !== $documentReview->assigned_to) {
                return response()->json([
                    'message' => 'You are not authorized to upload a new version of this document'
                ], 403);
            }

            $validated = $request->validate([
                'document' => 'required|file|mimes:pdf,doc,docx|max:10240',
                'notes' => 'required|string|max:1000',
            ]);

            // Start database transaction
            return \DB::transaction(function () use ($validated, $documentReview, $request) {
                $file = $request->file('document');
                $filePath = $file->store('document-reviews', 'documents');

                // Get next version number
                $versionNumber = $documentReview->versions()->max('version') + 1;

                // Create new version
                $version = $documentReview->versions()->create([
                    'version' => $versionNumber,
                    'file_path' => $filePath,
                    'notes' => $validated['notes'],
                    'uploaded_by' => Auth::id(),
                ]);

                // Update main document path and reset status
                $documentReview->update([
                    'document_path' => $filePath,
                    'status' => 'under_review',
                    'reviewed_at' => null,
                    'reviewed_by' => null,
                    'review_comments' => null,
                ]);

                // Log the version upload
                Log::info("New version {$versionNumber} uploaded for document review {$documentReview->id} by user " . Auth::id());

                // Return the version details
                return response()->json([
                    'id' => $version->id,
                    'version' => $version->version,
                    'file_path' => $version->file_path,
                    'notes' => $version->notes,
                    'uploaded_at' => $version->created_at->toDateTimeString(),
                    'uploaded_by' => $version->uploaded_by,
                    'document_review' => [
                        'id' => $documentReview->id,
                        'status' => $documentReview->status,
                        'document_path' => $documentReview->document_path,
                    ],
                    'message' => 'New version uploaded successfully'
                ], 201);
            });
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in DocumentReviewController@uploadVersion: ' . $e->getMessage());
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in DocumentReviewController@uploadVersion: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to upload document version',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download the document
     * 
     * @param DocumentReview $documentReview
     * @return StreamedResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function download(DocumentReview $documentReview): StreamedResponse
    {
        try {
            // Check if user has permission to view this document
            $this->authorize('view', $documentReview);

            // Check if file exists
            if (!Storage::disk('documents')->exists($documentReview->document_path)) {
                Log::error("Document not found: {$documentReview->document_path}");
                abort(404, 'The requested document could not be found.');
            }

            // Log the download
            Log::info("Document downloaded - DocumentReview ID: {$documentReview->id} by user: " . Auth::id());

            // Return the file for download
            return Storage::disk('documents')->download(
                $documentReview->document_path,
                $documentReview->original_filename,
                [
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0',
                ]
            );
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::warning("Unauthorized download attempt - DocumentReview ID: {$documentReview->id} by user: " . Auth::id());
            abort(403, 'You are not authorized to download this document.');
        } catch (\Exception $e) {
            Log::error('Error downloading document: ' . $e->getMessage());
            abort(500, 'An error occurred while processing your request.');
        }
    }

    /**
     * Preview the document in the browser
     * 
     * @param DocumentReview $documentReview
     * @return StreamedResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function preview(DocumentReview $documentReview): StreamedResponse
    {
        try {
            // Check if user has permission to preview this document
            $this->authorize('preview', $documentReview);

            // Check if file exists
            if (!Storage::disk('documents')->exists($documentReview->document_path)) {
                Log::error("Document not found for preview: {$documentReview->document_path}");
                abort(404, 'The requested document could not be found.');
            }

            // Get the file mime type
            $mimeType = Storage::disk('documents')->mimeType($documentReview->document_path);
            
            // If it's a PDF, we can preview it directly in the browser
            if ($mimeType === 'application/pdf') {
                $headers = [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => 'inline; filename="' . $documentReview->original_filename . '"',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0',
                ];

                // Log the preview
                Log::info("Document previewed - DocumentReview ID: {$documentReview->id} by user: " . Auth::id());

                return Storage::disk('documents')->response(
                    $documentReview->document_path,
                    $documentReview->original_filename,
                    $headers
                );
            }
            
            // For non-PDF files, force download
            return $this->download($documentReview);
            
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::warning("Unauthorized preview attempt - DocumentReview ID: {$documentReview->id} by user: " . Auth::id());
            abort(403, 'You are not authorized to preview this document.');
        } catch (\Exception $e) {
            Log::error('Error previewing document: ' . $e->getMessage());
            abort(500, 'An error occurred while processing your request.');
        }
    }
}







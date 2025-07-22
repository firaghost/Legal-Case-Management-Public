<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LitigationController;
use App\Http\Controllers\LaborLitigationController;
use App\Http\Controllers\OtherCivilLitigationController;
use App\Http\Controllers\CriminalLitigationController;
use App\Http\Controllers\SupervisorReviewController;
use App\Http\Controllers\SupervisorApprovalController;
use App\Http\Controllers\AdminAuditLogController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\SecuredLoanRecoveryController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\WorkUnitController;
use App\Http\Controllers\Api\LawyerController;
use App\Http\Controllers\LegalAdvisoryController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\EvidenceController;
use App\Http\Controllers\AdminMetricsController;
use App\Http\Controllers\CaseFileController;
use App\Http\Controllers\DocumentReviewController;
use App\Http\Controllers\Api\ChatController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes (no auth required)
Route::middleware('guest')->group(function () {
    // Authentication routes
    Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
});

// Protected routes require authentication
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::get('/me', [App\Http\Controllers\Api\AuthController::class, 'me']);
    Route::post('/refresh', [App\Http\Controllers\Api\AuthController::class, 'refresh']);
});

// Protected routes (require authentication)
// Removed GET /branches, /work-units, and /lawyers routes to prevent session confusion

// Include chat routes
require __DIR__.'/api/chat.php';

// Test route for chat functionality
Route::get('/test-chat', function () {
    return response()->json([
        'message' => 'Chat API is working!',
        'timestamp' => now()->toDateTimeString(),
    ]);
})->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {
    // Case Files Resource Routes
    Route::apiResource('case-files', CaseFileController::class)
        ->except(['edit', 'create']);
        
    // Additional Case File Routes
    Route::group(['prefix' => 'case-files/{case}'], function () {
        // Restore soft-deleted case file
        Route::post('restore', [CaseFileController::class, 'restore'])
            ->name('case-files.restore')
            ->withTrashed()
            ->middleware('permission:case_files.restore');
            
        // Get case file action logs
        Route::get('logs', [CaseFileController::class, 'logs'])
            ->name('case-files.logs')
            ->middleware('permission:case_files.view_logs');
    });
    
    // Code 01 – Clean Loan Recovery Litigation
    Route::post('/litigation', [LitigationController::class, 'store'])
        ->middleware('permission:litigation.create');
        
    Route::post('/litigation/{caseFile}/progress', [LitigationController::class, 'addProgress'])
        ->middleware('permission:litigation.update');
        
    Route::post('/litigation/{caseFile}/appeals', [LitigationController::class, 'addAppeal'])
        ->middleware('permission:litigation.update');
    
    // Code 02 – Labor Litigation
    Route::post('/labor-litigation', [LaborLitigationController::class, 'store'])
        ->middleware('permission:labor_litigation.create');
        
    Route::post('/labor-litigation/{caseFile}/progress', [LaborLitigationController::class, 'addProgress'])
        ->middleware('permission:labor_litigation.update');
        
    Route::post('/labor-litigation/{caseFile}/appeals', [LaborLitigationController::class, 'addAppeal'])
        ->middleware('permission:labor_litigation.update');
    
    // Code 03 – Other Civil Litigation
    Route::post('/other-civil-litigation', [OtherCivilLitigationController::class, 'store'])
        ->middleware('permission:other_civil_litigation.create');
        
    Route::post('/other-civil-litigation/{caseFile}/progress', [OtherCivilLitigationController::class, 'addProgress'])
        ->middleware('permission:other_civil_litigation.update');
        
    Route::post('/other-civil-litigation/{caseFile}/appeals', [OtherCivilLitigationController::class, 'addAppeal'])
        ->middleware('permission:other_civil_litigation.update');
    
    // Code 04 – Criminal Litigation
    Route::post('/criminal-litigation', [CriminalLitigationController::class, 'store'])
        ->middleware('permission:criminal_litigation.create');
        
    Route::post('/criminal-litigation/{caseFile}/progress', [CriminalLitigationController::class, 'addProgress'])
        ->middleware('permission:criminal_litigation.update');
        
    Route::post('/criminal-litigation/{caseFile}/appeals', [CriminalLitigationController::class, 'addAppeal'])
        ->middleware('permission:criminal_litigation.update');
    
    // Code 05 – Secured Loan Recovery
    Route::post('/secured-loan-recovery', [SecuredLoanRecoveryController::class, 'store'])
        ->middleware('permission:secured_loan_recovery.create');
        
    Route::post('/secured-loan-recovery/{caseFile}/progress', [SecuredLoanRecoveryController::class, 'addProgress'])
        ->middleware('permission:secured_loan_recovery.update');
        
    Route::post('/secured-loan-recovery/{caseFile}/auction', [SecuredLoanRecoveryController::class, 'updateAuctionStatus'])
        ->middleware('permission:secured_loan_recovery.update');
        
    Route::post('/secured-loan-recovery/{caseFile}/close', [SecuredLoanRecoveryController::class, 'closeCase'])
        ->middleware('permission:secured_loan_recovery.update');
    
    // Code 06 – Legal Advisory
    Route::post('/legal-advisory', [LegalAdvisoryController::class, 'store'])
        ->middleware('permission:legal_advisory.create');
        
    Route::post('/legal-advisory/{advisory}/document', [LegalAdvisoryController::class, 'uploadDocument'])
        ->middleware('permission:legal_advisory.upload_document');
        
    Route::post('/legal-advisory/{advisory}/review', [LegalAdvisoryController::class, 'submitReview'])
        ->middleware('permission:legal_advisory.review');
        
    Route::post('/legal-advisory/{advisory}/approve', [LegalAdvisoryController::class, 'approve'])
        ->middleware('permission:legal_advisory.approve');
        
    Route::post('/legal-advisory/{advisory}/progress', [LegalAdvisoryController::class, 'addProgress'])
        ->middleware('permission:legal_advisory.update');
        
    Route::post('/legal-advisory/{advisory}/stakeholders', [LegalAdvisoryController::class, 'updateStakeholders'])
            ->middleware('permission:legal_advisory.update');

        // Document Review Routes
    Route::prefix('document-reviews')->group(function () {
        // Create new document review
        Route::post('/', [DocumentReviewController::class, 'store'])
            ->middleware('permission:document_review.create');
            
        // Approve/reject document review
        Route::post('/{documentReview}/approve', [DocumentReviewController::class, 'approve'])
            ->middleware('permission:document_review.approve');
            
        // Upload new version
        Route::post('/{documentReview}/upload-version', [DocumentReviewController::class, 'uploadVersion'])
            ->middleware('permission:document_review.upload_version');
            
        // Download document
        Route::get('/{documentReview}/download', [DocumentReviewController::class, 'download'])
            ->middleware('permission:document_review.view')
            ->name('documents.download');
            
        // Preview document
        Route::get('/{documentReview}/preview', [DocumentReviewController::class, 'preview'])
            ->middleware('permission:document_review.view')
            ->name('documents.preview');
    });
    
    // Evidence upload
    Route::post('/case-files/{caseFile}/evidence', [EvidenceController::class, 'store'])
        ->middleware('permission:case_files.update');

    // Supervisor approves pending closure
    Route::post('/case-files/{caseFile}/approve-closure', [SupervisorReviewController::class, 'approveClosure'])
        ->middleware('role:supervisor');

    // Execution and closure requests (Litigation)
    Route::post('/litigation/{caseFile}/execution', [LitigationController::class, 'openExecution'])
        ->middleware('permission:litigation.update');
    Route::post('/litigation/{caseFile}/request-closure', [LitigationController::class, 'requestClosure'])
        ->middleware('permission:litigation.update');

        // Appointment CRUD
        Route::apiResource('case-files.appointments', AppointmentController::class)
            ->shallow()
            ->middleware('permission:case_files.update');

        // Admin metrics
        Route::get('/admin/metrics', [AdminMetricsController::class, 'index'])
            ->middleware('role:admin');

        // PDF report export
        Route::get('/reports/{period}/pdf', [ReportController::class, 'downloadPdf'])
            ->middleware('permission:reports.view');

    // Search routes
    Route::prefix('search')->group(function () {
        Route::get('/', [SearchController::class, 'search'])
            ->middleware('permission:search.basic');
            
        Route::get('/advanced', [SearchController::class, 'advancedSearch'])
            ->middleware('permission:search.advanced');
    });

    // Supervisor approval routes
        Route::prefix('supervisor')->middleware('role:supervisor')->group(function(){
            Route::get('/approvals', [SupervisorApprovalController::class, 'index']);
            Route::post('/approvals/{caseFile}/approve', [SupervisorApprovalController::class, 'approve']);
            Route::post('/approvals/{caseFile}/reject', [SupervisorApprovalController::class, 'reject']);
        });

        // Admin audit routes
        Route::prefix('admin')->middleware('role:admin')->group(function(){
            Route::get('/audit-logs', [AdminAuditLogController::class, 'index']);
            Route::get('/audit-logs/export', [AdminAuditLogController::class, 'export']);
        });

        // Report routes
    Route::prefix('reports')->group(function () {
        // Generate reports
        Route::get('/{period}', [ReportController::class, 'generateReport'])
            ->middleware('permission:reports.view');
        
        // Export reports
        Route::get('/export/{format}/{period}', [ReportController::class, 'exportReport'])
            ->where('format', 'pdf|xlsx|csv')
            ->middleware('permission:reports.export');
    });
});






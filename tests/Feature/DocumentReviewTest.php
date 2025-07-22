<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\DocumentReview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;

class DocumentReviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
        Storage::fake('documents');
        
        // Ensure users exist and are properly set up
        $this->ensureUsersExist();
    }
    
    private function ensureUsersExist()
    {
        // Make sure the seeded users have the correct attributes
        $lawyer = User::where('email', 'lawyer@lcms.test')->first();
        $supervisor = User::where('email', 'supervisor@lcms.test')->first();
        
        if ($lawyer) {
            $lawyer->update(['is_active' => true, 'role' => 'Lawyer']);
        }
        
        if ($supervisor) {
            $supervisor->update(['is_active' => true, 'role' => 'Supervisor']);
        }
    }

    #[Test]
    public function lawyer_can_submit_document_for_review()
    {
        $lawyer = User::where('email', 'lawyer@lcms.test')->first();
        Sanctum::actingAs($lawyer, ['*']);

        $file = UploadedFile::fake()->create('document.pdf', 1024);

        $response = $this->postJson('/api/document-reviews', [
            'subject' => 'Contract Review Request',
            'description' => 'Please review this contract for potential risks.',
            'document' => $file,
            'deadline' => now()->addDays(5)->toDateString(),
            'priority' => 'high',
            'stakeholders' => [
                [
                    'name' => 'Client Representative',
                    'email' => 'client@example.com',
                    'type' => 'client'
                ]
            ]
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'advisory_type' => 'document_review',
                'status' => 'under_review',
                'subject' => 'Contract Review Request',
            ]);

        // Assert file was stored
        Storage::disk('documents')->assertExists('document-reviews/' . $file->hashName());
    }

    #[Test]
    public function supervisor_can_approve_document_review()
    {
        $supervisor = User::where('email', 'supervisor@lcms.test')->first();
        $lawyer = User::where('email', 'lawyer@lcms.test')->first();
        
        // Submit document as lawyer
        Sanctum::actingAs($lawyer, ['*']);
        $file = UploadedFile::fake()->create('approval-test.pdf');
        $response = $this->postJson('/api/document-reviews', [
            'subject' => 'Contract Review for Approval',
            'description' => 'Review needed for approval test',
            'document' => $file,
            'deadline' => now()->addDays(5)->toDateString(),
            'priority' => 'high',
            'stakeholders' => [
                [
                    'name' => 'Approval Stakeholder',
                    'email' => 'approval@example.com',
                    'type' => 'client'
                ]
            ]
        ]);
        $documentReview = $response->json();
        $documentId = $documentReview['id'];

        // Approve as supervisor
        Sanctum::actingAs($supervisor, ['*']);
        $response = $this->postJson("/api/document-reviews/{$documentId}/approve", [
            'status' => 'approved',
            'comments' => 'Approved with minor revisions',
            'next_steps' => 'Proceed with execution'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'approved',
                'review_comments' => 'Approved with minor revisions'
            ]);
    }

    #[Test]
    public function user_can_download_document()
    {
        $user = User::where('email', 'lawyer@lcms.test')->first();
        Sanctum::actingAs($user, ['*']);

        // Create a document review
        $file = UploadedFile::fake()->create('test.pdf', 1024);
        $response = $this->postJson('/api/document-reviews', [
            'subject' => 'Test Document',
            'description' => 'Test document for download',
            'document' => $file,
            'deadline' => now()->addDays(5)->toDateString(),
            'priority' => 'low',
            'stakeholders' => [
                [
                    'name' => 'Test Stakeholder',
                    'email' => 'test@example.com',
                    'type' => 'client'
                ]
            ]
        ]);
        $documentReview = $response->json();
        $documentId = $documentReview['id'];

        // Test download
        $response = $this->getJson("/api/document-reviews/{$documentId}/download");
        $response->assertStatus(200)
                ->assertHeader('Content-Disposition', 'attachment; filename=test.pdf');
    }

    #[Test]
    public function user_can_preview_document()
    {
        $user = User::where('email', 'lawyer@lcms.test')->first();
        Sanctum::actingAs($user, ['*']);

        // Create a document review
        $file = UploadedFile::fake()->create('test-preview.pdf', 1024);
        $response = $this->postJson('/api/document-reviews', [
            'subject' => 'Test Preview Document',
            'description' => 'Test document for preview',
            'document' => $file,
            'deadline' => now()->addDays(5)->toDateString(),
            'priority' => 'low',
            'stakeholders' => [
                [
                    'name' => 'Preview Stakeholder',
                    'email' => 'preview@example.com',
                    'type' => 'client'
                ]
            ]
        ]);
        $documentReview = $response->json();
        $documentId = $documentReview['id'];

        // Test preview
        $response = $this->getJson("/api/document-reviews/{$documentId}/preview");
        $response->assertStatus(200)
                ->assertHeader('Content-Disposition', 'inline; filename="test-preview.pdf"');
    }
}







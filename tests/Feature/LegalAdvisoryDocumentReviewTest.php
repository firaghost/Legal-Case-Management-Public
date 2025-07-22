<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class LegalAdvisoryDocumentReviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
        Storage::fake('documents');
    }

    /** @test */
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

        if ($response->status() === 500) {
            $content = $response->getContent();
            $this->fail('Received 500 status code. Response: ' . $content);
        }
        
        $response->assertStatus(201)
            ->assertJson([
                'advisory_type' => 'document_review',
                'status' => 'under_review',
                'subject' => 'Contract Review Request',
            ]);

        // Assert file was stored
        Storage::disk('documents')->assertExists('document-reviews/' . $file->hashName());
    }

    /** @test */
    public function supervisor_can_approve_document_review()
    {
        // Create and authenticate a lawyer user to create the document review
        $lawyer = User::where('email', 'lawyer@lcms.test')->first();
        Sanctum::actingAs($lawyer, ['*']);

        // Create a document review request as lawyer
        $file = UploadedFile::fake()->create('document.pdf');
        $response = $this->postJson('/api/document-reviews', [
            'subject' => 'Contract Review',
            'description' => 'Review needed',
            'document' => $file,
            'deadline' => now()->addDays(7)->toDateString(),
            'priority' => 'high',
            'stakeholders' => [
                [
                    'name' => 'Test Stakeholder',
                    'email' => 'stakeholder@example.com',
                    'type' => 'client'
                ]
            ]
        ]);
        
        $response->assertStatus(201);
        $caseId = $response->json('id');
        
        // Verify the document review was created
        $this->assertDatabaseHas('document_reviews', [
            'id' => $caseId,
            'status' => 'under_review'
        ]);

        // Now switch to supervisor user for approval
        $supervisor = User::where('email', 'supervisor@lcms.test')->first();
        
        // Ensure we're using the web guard for permission assignment
        $permission = Permission::findOrCreate('document_review.approve', 'web');
        $supervisor->givePermissionTo($permission);
        
        Sanctum::actingAs($supervisor, ['*']);

        // Approve the review
        $response = $this->postJson("/api/document-reviews/{$caseId}/approve", [
            'comments' => 'Approved with minor revisions',
            'status' => 'approved',
            'next_steps' => 'Proceed with execution'
        ]);

        // Dump response for debugging
        if ($response->status() !== 200) {
            dump('Response status: ' . $response->status());
            dump('Response content: ' . $response->content());
        }

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'approved',
                'review_comments' => 'Approved with minor revisions'
            ]);
    }
}







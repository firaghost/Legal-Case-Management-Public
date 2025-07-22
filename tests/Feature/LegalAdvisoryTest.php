<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class LegalAdvisoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed roles, permissions, users, and case types
        $this->artisan('db:seed');
    }

    /** @test */
    public function supervisor_can_create_legal_advisory_request(): void
    {
        $supervisor = User::where('email', 'supervisor@lcms.test')->first();
        Sanctum::actingAs($supervisor, ['*']);

        $payload = [
            'advisory_type' => 'written_advice',
            'subject' => 'Contract Interpretation for Branch X',
            'description' => 'Need advice on interpreting clause 5 of contract ABC.',
            'is_own_motion' => false,
            'request_date' => now()->toDateString(),
            'stakeholders' => [
                [
                    'name' => 'Branch Manager',
                    'type' => 'requester',
                    'email' => 'manager@branch.test',
                ],
            ],
        ];

        // Debug payload
        dump('Request Payload:', $payload);

        $response = $this->postJson('/api/legal-advisory', $payload);

        // Debug response
        dump('Response Status:', $response->status());
        dump('Response Content:', $response->content());

        $response->assertStatus(201)
                 ->assertJsonPath('advisory_type', 'written_advice')
                 ->assertJsonPath('subject', $payload['subject'])
                 ->assertJsonPath('status', 'draft')
                 ->assertJsonPath('stakeholders.0.name', 'Branch Manager');
    }
}







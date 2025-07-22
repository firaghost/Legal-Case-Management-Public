<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class SecuredLoanRecoveryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
        
        // Ensure users exist and are properly set up
        $this->ensureUsersExist();
    }
    
    private function ensureUsersExist()
    {
        // Make sure the seeded users have the correct attributes
        $supervisor = User::where('email', 'supervisor@lcms.test')->first();
        
        if ($supervisor) {
            $supervisor->update(['is_active' => true, 'role' => 'Supervisor']);
        }
    }

    
    public function test_supervisor_can_create_secured_loan_recovery_case()
    {
        $supervisor = User::where('email', 'supervisor@lcms.test')->first();
        Sanctum::actingAs($supervisor, ['*']);

        $payload = [
            'loan_amount' => 100000,
            'outstanding_amount' => 95000,
            'collateral_description' => 'House in Addis Ababa',
            'collateral_value' => 120000,
            'file_number' => '05-' . time(), // Make unique for test
            'title' => 'Defaulted Mortgage Loan',
            'description' => 'Test case for secured loan recovery',
        ];

        // Debug: Show the request payload
        dump('Request Payload:', $payload);
        
        // Make the request
        $response = $this->postJson('/api/secured-loan-recovery', $payload);
        
        // Debug: Show the response
        dump('Response Status:', $response->status());
        dump('Response Content:', $response->content());
        
        // Assert the response
        $response->assertStatus(201)
                 ->assertJsonPath('loan_amount', '100000.00')
                 ->assertJsonPath('outstanding_amount', '95000.00')
                 ->assertJsonPath('collateral_value', '120000.00');
    }
}







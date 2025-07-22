<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Console\Command;

class TestAuditLog extends Command
{
    protected $signature = 'test:audit-log';
    protected $description = 'Test audit log functionality';

    public function handle()
    {
        // Get the first user or create one if none exists
        $user = User::first();
        
        if (!$user) {
            $user = User::factory()->create();
            $this->info('Created test user with ID: ' . $user->id);
        }

        // Test logging a user action
        $this->info('Testing audit log...');
        
        try {
            AuditLogService::log(
                'TEST_ACTION',
                get_class($user),
                $user->id,
                ['test' => 'This is a test audit log entry']
            );
            
            $this->info('Successfully logged test audit entry');
            
            // Check if the log was created
            $count = \App\Models\AuditLog::where('action', 'TEST_ACTION')->count();
            $this->info("Found $count test audit log entries");
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
        
        return 0;
    }
}







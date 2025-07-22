<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckAuditLogs extends Command
{
    protected $signature = 'check:audit-logs';
    protected $description = 'Check if audit logs table exists and has data';

    public function handle()
    {
        $tableExists = Schema::hasTable('audit_logs');
        $this->info('Audit logs table exists: ' . ($tableExists ? 'Yes' : 'No'));
        
        if ($tableExists) {
            $count = DB::table('audit_logs')->count();
            $this->info("Number of audit log entries: $count");
            
            if ($count > 0) {
                $this->info("\nLatest 5 entries with full details:");
                $entries = DB::table('audit_logs')
                    ->leftJoin('users', 'audit_logs.user_id', '=', 'users.id')
                    ->select('audit_logs.*', 'users.name as user_name', 'users.email as user_email')
                    ->latest('audit_logs.created_at')
                    ->limit(5)
                    ->get();
                
                foreach ($entries as $entry) {
                    $this->info("\n" . str_repeat('=', 80));
                    $this->info("ID: " . $entry->id);
                    $this->info("Action: " . $entry->action);
                    $this->info("User: " . ($entry->user_name ? "{$entry->user_name} <{$entry->user_email}>" : 'System'));
                    $this->info("Model: " . ($entry->auditable_type ? class_basename($entry->auditable_type) . " (ID: {$entry->auditable_id})" : 'N/A'));
                    $this->info("IP: " . $entry->ip_address);
                    $this->info("User Agent: " . $entry->user_agent);
                    $this->info("Created At: " . $entry->created_at);
                    
                    // Format and display changes
                    $changes = json_decode($entry->changes, true);
                    $this->info("Changes:");
                    $this->line(json_encode($changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                }
                $this->info("\n" . str_repeat('=', 80));
            }
        } else {
            $this->info("\nTo create the audit logs table, run:");
            $this->info("php artisan migrate");
        }
        
        return 0;
    }
}







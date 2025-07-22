<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckTablesCommand extends Command
{
    protected $signature = 'db:check-tables';
    protected $description = 'List all database tables';

    public function handle()
    {
        $tables = DB::select('SHOW TABLES');
        $db = 'Tables_in_' . DB::getDatabaseName();
        
        $this->info('Database Tables:');
        foreach ($tables as $table) {
            $this->line('- ' . $table->$db);
        }
        
        return 0;
    }
}







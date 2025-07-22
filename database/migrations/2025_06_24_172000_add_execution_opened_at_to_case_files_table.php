<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('case_files', function (Blueprint $table) {
            if (!Schema::hasColumn('case_files', 'execution_opened_at')) {
                $table->date('execution_opened_at')->nullable()->after('recovered_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('case_files', function (Blueprint $table) {
            if (Schema::hasColumn('case_files', 'execution_opened_at')) {
                $table->dropColumn('execution_opened_at');
            }
        });
    }
};







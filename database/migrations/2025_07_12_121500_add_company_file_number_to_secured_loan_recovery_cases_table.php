<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('secured_loan_recovery_cases', function (Blueprint $table) {
            if (!Schema::hasColumn('secured_loan_recovery_cases', 'company_file_number')) {
                $table->string('company_file_number')->after('court_file_number')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('secured_loan_recovery_cases', function (Blueprint $table) {
            if (Schema::hasColumn('secured_loan_recovery_cases', 'company_file_number')) {
                $table->dropColumn('company_file_number');
            }
        });
    }
};







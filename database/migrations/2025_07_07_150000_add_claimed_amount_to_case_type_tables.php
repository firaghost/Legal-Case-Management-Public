<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'clean_loan_recovery_cases',
            'secured_loan_recovery_cases',
            'litigation_cases',
            'labor_litigation_cases',
            'other_civil_litigation_cases',
            'criminal_litigation_cases',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'claimed_amount')) {
                    // place after outstanding_amount if it exists, otherwise at the end
                    if (Schema::hasColumn($tableName, 'outstanding_amount')) {
                        $table->decimal('claimed_amount', 15, 2)->default(0)->after('outstanding_amount');
                    } else {
                        $table->decimal('claimed_amount', 15, 2)->default(0);
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'clean_loan_recovery_cases',
            'secured_loan_recovery_cases',
            'litigation_cases',
            'labor_litigation_cases',
            'other_civil_litigation_cases',
            'criminal_litigation_cases',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'claimed_amount')) {
                    $table->dropColumn('claimed_amount');
                }
            });
        }
    }
};







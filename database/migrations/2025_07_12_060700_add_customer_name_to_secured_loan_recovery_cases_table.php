<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('secured_loan_recovery_cases', function (Blueprint $table) {
            if (!Schema::hasColumn('secured_loan_recovery_cases', 'customer_name')) {
                $table->string('customer_name')->after('claimed_amount')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('secured_loan_recovery_cases', function (Blueprint $table) {
            if (Schema::hasColumn('secured_loan_recovery_cases', 'customer_name')) {
                $table->dropColumn('customer_name');
            }
        });
    }
};







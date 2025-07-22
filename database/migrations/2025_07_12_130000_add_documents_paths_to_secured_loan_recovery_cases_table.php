<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('secured_loan_recovery_cases', function (Blueprint $table) {
            $table->string('collateral_estimation_path')->nullable()->after('collateral_value');
            $table->string('warning_doc_path')->nullable()->after('collateral_estimation_path');
            $table->string('auction_publication_path')->nullable()->after('warning_doc_path');
        });
    }

    public function down(): void
    {
        Schema::table('secured_loan_recovery_cases', function (Blueprint $table) {
            $table->dropColumn(['collateral_estimation_path', 'warning_doc_path', 'auction_publication_path']);
        });
    }
};







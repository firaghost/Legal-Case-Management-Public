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
        // Code 05: Secured Loan Recovery
        Schema::create('secured_loan_recovery_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_file_id')->constrained('case_files')->cascadeOnDelete();
            $table->decimal('loan_amount', 15, 2);
            $table->decimal('outstanding_amount', 15, 2);
            $table->decimal('claimed_amount', 15, 2)->nullable();
            $table->date('foreclosure_notice_date')->nullable();
            $table->text('collateral_description')->nullable();
            $table->decimal('collateral_value', 15, 2)->nullable();
            $table->boolean('first_auction_held')->default(false);
            $table->boolean('second_auction_held')->default(false);
            $table->decimal('recovered_amount', 15, 2)->default(0);

            // Closure conditions
            $table->enum('closure_type', [
                'fully_repaid',
                'collateral_sold',
                'restructured',
                'settlement',
                'collateral_acquired'
            ])->nullable();

            $table->text('closure_notes')->nullable();
            $table->date('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('secured_loan_recovery_cases');
    }
};







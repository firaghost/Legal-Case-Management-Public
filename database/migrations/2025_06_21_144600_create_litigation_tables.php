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
        // Table for litigation specific data (Clean Loan Recovery)
        Schema::create('litigation_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_file_id')->unique()->constrained('case_files')->cascadeOnDelete();
            $table->string('branch');
            $table->string('internal_file_no')->unique();
            $table->decimal('outstanding_amount', 15, 2);
            $table->decimal('recovered_amount', 15, 2)->default(0);
            $table->date('execution_opened_at')->nullable();
            $table->boolean('early_closed')->default(false);
            $table->date('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        // Appeals table (covers appeal / second level / cassation)
        Schema::create('appeals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_file_id')->constrained('case_files')->cascadeOnDelete();
            $table->enum('level', ['Appeal', 'Second', 'Cassation']);
            $table->string('file_number');
            $table->text('notes')->nullable();
            $table->date('decided_at')->nullable();
            $table->timestamps();
        });

        // Clean Loan Recovery Cases table
        Schema::create('clean_loan_recovery_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_file_id')->unique()->constrained('case_files')->cascadeOnDelete();
            $table->decimal('outstanding_amount', 15, 2);
            $table->decimal('recovered_amount', 15, 2)->default(0);
            // Add any other fields needed for clean loan recovery
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appeals');
        Schema::dropIfExists('litigation_cases');
        Schema::dropIfExists('clean_loan_recovery_cases');
    }
};







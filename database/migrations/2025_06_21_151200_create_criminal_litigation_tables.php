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
        // Code 04: Criminal Litigation
        Schema::create('criminal_litigation_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_file_id')->unique()->constrained('case_files')->cascadeOnDelete();
            $table->string('police_ref_no')->nullable();
            $table->string('prosecutor_ref_no')->nullable();
            $table->text('evidence_summary')->nullable();
            $table->enum('status', ['Submitted', 'ProsecutorReview', 'Court', 'Closed'])->default('Submitted');
            $table->decimal('recovered_amount', 15, 2)->default(0);
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
        Schema::dropIfExists('criminal_litigation_cases');
    }
};







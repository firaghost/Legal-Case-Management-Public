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
        // Code 06: Legal Advisory & Document Service
        Schema::create('legal_advisory_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_file_id')->unique()->constrained('case_files')->cascadeOnDelete();
            
            // Advisory Type: 'written_advice' or 'document_review'
            $table->enum('advisory_type', ['written_advice', 'document_review']);
            
            // Common fields
            $table->string('subject');
            $table->text('description')->nullable();
            $table->foreignId('assigned_lawyer_id')->constrained('users');
            $table->date('request_date');
            $table->date('submission_date')->nullable();
            $table->enum('status', ['draft', 'in_review', 'approved', 'completed', 'cancelled'])->default('draft');
            
            // For document review
            $table->string('document_path')->nullable();
            $table->text('review_notes')->nullable();
            $table->string('reviewed_document_path')->nullable();
            
            // For internal tracking
            $table->boolean('is_own_motion')->default(false);
            $table->string('reference_number')->nullable();
            
            // Approval
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            
            // Closure
            $table->text('closure_notes')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users');
            
            $table->timestamps();
        });
        
        // Table for tracking document versions
        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('legal_advisory_case_id')->constrained('legal_advisory_cases')->cascadeOnDelete();
            $table->string('version_number');
            $table->string('document_path');
            $table->text('changes')->nullable();
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
        });
        
        // Table for advisory stakeholders (internal/external)
        Schema::create('advisory_stakeholders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('legal_advisory_case_id')->constrained('legal_advisory_cases')->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('organization')->nullable();
            $table->enum('type', ['requester', 'reviewer', 'approver', 'recipient']);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advisory_stakeholders');
        Schema::dropIfExists('document_versions');
        Schema::dropIfExists('legal_advisory_cases');
    }
};







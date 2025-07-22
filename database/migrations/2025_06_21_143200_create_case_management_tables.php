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
        // Role-User pivot table (roles table is created in a separate migration)
        if (!Schema::hasTable('role_user')) {
            Schema::create('role_user', function (Blueprint $table) {
                $table->foreignId('role_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->primary(['role_id', 'user_id']);
                $table->timestamps();
            });
        }

        // Case types (Litigation, Loan Recovery, etc.)
        Schema::create('case_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Core case file table
        Schema::create('case_files', function (Blueprint $table) {
            $table->id();
            $table->string('file_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('case_type_id')->constrained('case_types');
            // Note: court_file_number is not present here; it should be added to type-specific tables if needed.
            $table->string('court_name')->nullable(); // Manual entry for court name
            $table->enum('status', ['Open', 'Closed', 'Suspended'])->default('Open');
            $table->date('opened_at')->nullable();
            $table->date('closed_at')->nullable();
            $table->foreignId('lawyer_id')->nullable()->constrained('users');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        // Plaintiffs table
        Schema::create('plaintiffs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_file_id')->constrained('case_files')->cascadeOnDelete();
            $table->string('name');
            $table->string('contact_info')->nullable();
            $table->timestamps();
        });

        // Defendants table
        Schema::create('defendants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_file_id')->constrained('case_files')->cascadeOnDelete();
            $table->string('name');
            $table->string('contact_info')->nullable();
            $table->timestamps();
        });

        // Progress updates on a case
        Schema::create('progress_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_file_id')->constrained('case_files')->cascadeOnDelete();
            $table->string('status');
            $table->text('notes')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        // Advisory requests (specific to legal advisory module)
        Schema::create('advisory_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_file_id')->nullable()->constrained('case_files')->nullOnDelete();
            $table->foreignId('requester_id')->constrained('users');
            $table->text('details')->nullable();
            $table->enum('status', ['Pending', 'Resolved', 'Escalated'])->default('Pending');
            $table->timestamps();
        });

        // Documents linked to a case
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_file_id')->constrained('case_files')->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users');
            $table->string('original_name');
            $table->string('path');
            $table->string('doc_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
        Schema::dropIfExists('advisory_requests');
        Schema::dropIfExists('progress_updates');
        Schema::dropIfExists('defendants');
        Schema::dropIfExists('plaintiffs');
        Schema::dropIfExists('case_files');
        // Removed courts table drop
        Schema::dropIfExists('case_types');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('roles');
    }
};







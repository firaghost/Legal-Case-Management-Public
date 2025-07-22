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
        // Code 02: Labor Litigation specific table
        Schema::create('labor_litigation_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_file_id')->unique()->constrained('case_files')->cascadeOnDelete();
            $table->enum('claim_type', ['Money', 'Material', 'Both']);
            $table->decimal('claim_amount', 15, 2)->nullable();
            $table->text('claim_material_desc')->nullable();
            $table->decimal('recovered_amount', 15, 2)->default(0);
            $table->boolean('early_settled')->default(false);
            $table->date('execution_opened_at')->nullable();
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
        Schema::dropIfExists('labor_litigation_cases');
    }
};







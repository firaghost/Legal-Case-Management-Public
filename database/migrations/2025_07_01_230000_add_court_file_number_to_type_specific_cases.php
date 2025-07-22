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
        Schema::table('clean_loan_recovery_cases', function (Blueprint $table) {
            $table->string('court_file_number')->nullable()->after('id');
        });
        Schema::table('secured_loan_recovery_cases', function (Blueprint $table) {
            $table->string('court_file_number')->nullable()->after('id');
        });
        Schema::table('labor_litigation_cases', function (Blueprint $table) {
            $table->string('court_file_number')->nullable()->after('id');
        });
        Schema::table('other_civil_litigation_cases', function (Blueprint $table) {
            $table->string('court_file_number')->nullable()->after('id');
        });
        Schema::table('criminal_litigation_cases', function (Blueprint $table) {
            $table->string('court_file_number')->nullable()->after('id');
        });
        Schema::table('legal_advisory_cases', function (Blueprint $table) {
            $table->string('court_file_number')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clean_loan_recovery_cases', function (Blueprint $table) {
            $table->dropColumn('court_file_number');
        });
        Schema::table('secured_loan_recovery_cases', function (Blueprint $table) {
            $table->dropColumn('court_file_number');
        });
        Schema::table('labor_litigation_cases', function (Blueprint $table) {
            $table->dropColumn('court_file_number');
        });
        Schema::table('other_civil_litigation_cases', function (Blueprint $table) {
            $table->dropColumn('court_file_number');
        });
        Schema::table('criminal_litigation_cases', function (Blueprint $table) {
            $table->dropColumn('court_file_number');
        });
        Schema::table('legal_advisory_cases', function (Blueprint $table) {
            $table->dropColumn('court_file_number');
        });
    }
}; 






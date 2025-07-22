<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Update all case type tables with missing columns
     */
    public function up(): void
    {
        // 1. Update Clean Loan Recovery Cases table
        if (Schema::hasTable('clean_loan_recovery_cases')) {
            Schema::table('clean_loan_recovery_cases', function (Blueprint $table) {
                if (!Schema::hasColumn('clean_loan_recovery_cases', 'branch_id')) {
                    $table->foreignId('branch_id')->nullable()->constrained('branches')->after('case_file_id');
                }
                if (!Schema::hasColumn('clean_loan_recovery_cases', 'work_unit_id')) {
                    $table->foreignId('work_unit_id')->nullable()->constrained('work_units')->after('branch_id');
                }
                if (!Schema::hasColumn('clean_loan_recovery_cases', 'company_file_number')) {
                    $table->string('company_file_number')->nullable()->after('court_file_number');
                }
                if (!Schema::hasColumn('clean_loan_recovery_cases', 'customer_name')) {
                    $table->string('customer_name')->nullable()->after('company_file_number');
                }
                if (!Schema::hasColumn('clean_loan_recovery_cases', 'loan_amount')) {
                    $table->decimal('loan_amount', 15, 2)->nullable()->after('outstanding_amount');
                }
                if (!Schema::hasColumn('clean_loan_recovery_cases', 'court_name')) {
                    $table->string('court_name')->nullable()->after('loan_amount');
                }
                if (!Schema::hasColumn('clean_loan_recovery_cases', 'lawyer_id')) {
                    $table->foreignId('lawyer_id')->nullable()->constrained('users')->after('court_name');
                }
                if (!Schema::hasColumn('clean_loan_recovery_cases', 'court_info')) {
                    $table->text('court_info')->nullable()->after('lawyer_id');
                }
            });
        }

        // 2. Update Labor Litigation Cases table
        if (Schema::hasTable('labor_litigation_cases')) {
            Schema::table('labor_litigation_cases', function (Blueprint $table) {
                if (!Schema::hasColumn('labor_litigation_cases', 'outstanding_amount')) {
                    $table->decimal('outstanding_amount', 15, 2)->default(0)->after('claim_material_desc');
                }
                if (!Schema::hasColumn('labor_litigation_cases', 'loan_amount')) {
                    $table->decimal('loan_amount', 15, 2)->nullable()->after('outstanding_amount');
                }
            });
        }

        // 3. Update Other Civil Litigation Cases table
        if (Schema::hasTable('other_civil_litigation_cases')) {
            Schema::table('other_civil_litigation_cases', function (Blueprint $table) {
                if (!Schema::hasColumn('other_civil_litigation_cases', 'outstanding_amount')) {
                    $table->decimal('outstanding_amount', 15, 2)->default(0)->after('claim_material_desc');
                }
                if (!Schema::hasColumn('other_civil_litigation_cases', 'loan_amount')) {
                    $table->decimal('loan_amount', 15, 2)->nullable()->after('outstanding_amount');
                }
                if (!Schema::hasColumn('other_civil_litigation_cases', 'court_name')) {
                    $table->string('court_name')->nullable()->after('loan_amount');
                }
            });
        }

        // 4. Update Criminal Litigation Cases table
        if (Schema::hasTable('criminal_litigation_cases')) {
            Schema::table('criminal_litigation_cases', function (Blueprint $table) {
                if (!Schema::hasColumn('criminal_litigation_cases', 'outstanding_amount')) {
                    $table->decimal('outstanding_amount', 15, 2)->default(0)->after('status');
                }
                if (!Schema::hasColumn('criminal_litigation_cases', 'loan_amount')) {
                    $table->decimal('loan_amount', 15, 2)->nullable()->after('outstanding_amount');
                }
                if (!Schema::hasColumn('criminal_litigation_cases', 'police_station')) {
                    $table->string('police_station')->nullable()->after('prosecutor_ref_no');
                }
                if (!Schema::hasColumn('criminal_litigation_cases', 'police_file_number')) {
                    $table->string('police_file_number')->nullable()->after('police_station');
                }
            });
        }

        // 5. Update Secured Loan Recovery Cases table (already comprehensive, but add missing if any)
        if (Schema::hasTable('secured_loan_recovery_cases')) {
            Schema::table('secured_loan_recovery_cases', function (Blueprint $table) {
                if (!Schema::hasColumn('secured_loan_recovery_cases', 'work_unit_id')) {
                    $table->foreignId('work_unit_id')->nullable()->constrained('work_units')->after('case_file_id');
                }
                if (!Schema::hasColumn('secured_loan_recovery_cases', 'branch_id')) {
                    $table->foreignId('branch_id')->nullable()->constrained('branches')->after('work_unit_id');
                }
            });
        }

        // 6. Update Legal Advisory Cases table
        if (Schema::hasTable('legal_advisory_cases')) {
            Schema::table('legal_advisory_cases', function (Blueprint $table) {
                if (!Schema::hasColumn('legal_advisory_cases', 'outstanding_amount')) {
                    $table->decimal('outstanding_amount', 15, 2)->default(0)->after('status');
                }
                if (!Schema::hasColumn('legal_advisory_cases', 'recovered_amount')) {
                    $table->decimal('recovered_amount', 15, 2)->default(0)->after('outstanding_amount');
                }
                if (!Schema::hasColumn('legal_advisory_cases', 'claimed_amount')) {
                    $table->decimal('claimed_amount', 15, 2)->default(0)->after('recovered_amount');
                }
                if (!Schema::hasColumn('legal_advisory_cases', 'loan_amount')) {
                    $table->decimal('loan_amount', 15, 2)->nullable()->after('claimed_amount');
                }
            });
        }

        // 7. Update Litigation Cases table (Clean Loan Recovery legacy table)
        if (Schema::hasTable('litigation_cases')) {
            Schema::table('litigation_cases', function (Blueprint $table) {
                if (!Schema::hasColumn('litigation_cases', 'court_file_number')) {
                    $table->string('court_file_number')->nullable()->after('case_file_id');
                }
                if (!Schema::hasColumn('litigation_cases', 'loan_amount')) {
                    $table->decimal('loan_amount', 15, 2)->nullable()->after('outstanding_amount');
                }
            });
        }
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        // Remove columns added in up() method
        $tables = [
            'clean_loan_recovery_cases' => ['branch_id', 'work_unit_id', 'company_file_number', 'customer_name', 'loan_amount', 'court_name', 'lawyer_id', 'court_info'],
            'labor_litigation_cases' => ['outstanding_amount', 'loan_amount'],
            'other_civil_litigation_cases' => ['outstanding_amount', 'loan_amount', 'court_name'],
            'criminal_litigation_cases' => ['outstanding_amount', 'loan_amount', 'police_station', 'police_file_number'],
            'secured_loan_recovery_cases' => ['work_unit_id', 'branch_id'],
            'legal_advisory_cases' => ['outstanding_amount', 'recovered_amount', 'claimed_amount', 'loan_amount'],
            'litigation_cases' => ['court_file_number', 'loan_amount'],
        ];

        foreach ($tables as $tableName => $columns) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($columns, $tableName) {
                    foreach ($columns as $column) {
                        if (Schema::hasColumn($tableName, $column)) {
                            $table->dropColumn($column);
                        }
                    }
                });
            }
        }
    }
};







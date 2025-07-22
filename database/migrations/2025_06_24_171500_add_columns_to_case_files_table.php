<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Add missing financial & workflow columns to case_files.
     */
    public function up(): void
    {
        Schema::table('case_files', function (Blueprint $table) {
            if (!Schema::hasColumn('case_files', 'closure_requested_at')) {
                $table->timestamp('closure_requested_at')->nullable()->after('closed_at');
            }
            if (!Schema::hasColumn('case_files', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('closure_requested_at');
            }
            if (!Schema::hasColumn('case_files', 'claimed_amount')) {
                $table->decimal('claimed_amount', 15, 2)->default(0)->after('approved_at');
            }
            if (!Schema::hasColumn('case_files', 'recovered_amount')) {
                $table->decimal('recovered_amount', 15, 2)->default(0)->after('claimed_amount');
            }
        });
    }

    /**
     * Rollback added columns.
     */
    public function down(): void
    {
        Schema::table('case_files', function (Blueprint $table) {
            if (Schema::hasColumn('case_files', 'recovered_amount')) {
                $table->dropColumn('recovered_amount');
            }
            if (Schema::hasColumn('case_files', 'claimed_amount')) {
                $table->dropColumn('claimed_amount');
            }
            if (Schema::hasColumn('case_files', 'approved_at')) {
                $table->dropColumn('approved_at');
            }
            if (Schema::hasColumn('case_files', 'closure_requested_at')) {
                $table->dropColumn('closure_requested_at');
            }
        });
    }
};







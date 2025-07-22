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
        Schema::table('case_files', function (Blueprint $table) {
            // Check if columns exist before creating indexes
            if (Schema::hasColumn('case_files', 'lawyer_id')) {
                $table->index(['lawyer_id', 'status'], 'case_files_lawyer_status_index');
            }
            if (Schema::hasColumn('case_files', 'case_type_id')) {
                $table->index(['case_type_id', 'status'], 'case_files_type_status_index');
            }
            if (Schema::hasColumn('case_files', 'branch_id')) {
                $table->index(['branch_id', 'status'], 'case_files_branch_status_index');
            }
            if (Schema::hasColumn('case_files', 'work_unit_id')) {
                $table->index(['work_unit_id', 'status'], 'case_files_work_unit_status_index');
            }
            $table->index(['created_at', 'status'], 'case_files_created_status_index');
            $table->index(['updated_at'], 'case_files_updated_index');
            $table->index(['file_number'], 'case_files_file_number_index');
            $table->index(['status'], 'case_files_status_index');
            if (Schema::hasColumn('case_files', 'closure_requested_at')) {
                $table->index(['closure_requested_at'], 'case_files_closure_requested_index');
            }
            if (Schema::hasColumn('case_files', 'approved_at')) {
                $table->index(['approved_at'], 'case_files_approved_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('case_files', function (Blueprint $table) {
            // Drop indexes if they exist
            $indexes = ['case_files_lawyer_status_index', 'case_files_type_status_index', 
                       'case_files_branch_status_index', 'case_files_work_unit_status_index',
                       'case_files_created_status_index', 'case_files_updated_index',
                       'case_files_file_number_index', 'case_files_status_index',
                       'case_files_closure_requested_index', 'case_files_approved_index'];
            
            foreach ($indexes as $index) {
                try {
                    $table->dropIndex($index);
                } catch (Exception $e) {
                    // Index doesn't exist, continue
                }
            }
        });
    }
};







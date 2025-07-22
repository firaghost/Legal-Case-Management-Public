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
        Schema::table('progress_updates', function (Blueprint $table) {
            $table->index(['case_file_id', 'created_at'], 'progress_updates_case_created_index');
            $table->index(['updated_by', 'created_at'], 'progress_updates_user_created_index');
            $table->index(['status'], 'progress_updates_status_index');
            $table->index(['created_at'], 'progress_updates_created_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progress_updates', function (Blueprint $table) {
            $table->dropIndex('progress_updates_case_created_index');
            $table->dropIndex('progress_updates_user_created_index');
            $table->dropIndex('progress_updates_status_index');
            $table->dropIndex('progress_updates_created_index');
        });
    }
};







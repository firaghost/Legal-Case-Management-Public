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
            if (!Schema::hasColumn('case_files', 'advisory_requested_at')) {
                $table->timestamp('advisory_requested_at')->nullable()->after('closure_requested_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('case_files', function (Blueprint $table) {
            if (Schema::hasColumn('case_files', 'advisory_requested_at')) {
                $table->dropColumn('advisory_requested_at');
            }
        });
    }
};







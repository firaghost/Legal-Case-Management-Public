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
        // Ensure soft delete column exists for branches
        if (!Schema::hasColumn('branches', 'deleted_at')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->softDeletes()->after('updated_at');
            });
        }

        // Ensure soft delete column exists for work_units
        if (!Schema::hasColumn('work_units', 'deleted_at')) {
            Schema::table('work_units', function (Blueprint $table) {
                $table->softDeletes()->after('updated_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('branches', 'deleted_at')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('work_units', 'deleted_at')) {
            Schema::table('work_units', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};







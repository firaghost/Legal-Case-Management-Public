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
        // Add is_active boolean to branches if missing
        if (!Schema::hasColumn('branches', 'is_active')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('email');
            });
        }

        // Add is_active boolean to work_units if missing
        if (!Schema::hasColumn('work_units', 'is_active')) {
            Schema::table('work_units', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('code');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('branches', 'is_active')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }

        if (Schema::hasColumn('work_units', 'is_active')) {
            Schema::table('work_units', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }
};







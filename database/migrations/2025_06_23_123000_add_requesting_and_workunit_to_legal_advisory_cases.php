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
        Schema::table('legal_advisory_cases', function (Blueprint $table) {
            $table->string('requesting_department')->nullable()->after('description');
            $table->string('work_unit_advised')->nullable()->after('requesting_department');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('legal_advisory_cases', function (Blueprint $table) {
            $table->dropColumn(['requesting_department', 'work_unit_advised']);
        });
    }
};







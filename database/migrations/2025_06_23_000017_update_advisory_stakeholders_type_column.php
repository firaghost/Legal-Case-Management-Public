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
        Schema::table('advisory_stakeholders', function (Blueprint $table) {
            $table->string('type', 20)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advisory_stakeholders', function (Blueprint $table) {
            // Revert to the original column type if needed
            $table->string('type', 10)->change();
        });
    }
};







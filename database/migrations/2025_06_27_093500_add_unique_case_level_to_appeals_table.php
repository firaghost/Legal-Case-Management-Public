<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add unique composite index case_id + level to prevent duplicates
        Schema::table('appeals', function (Blueprint $table) {
            $table->unique(['case_file_id', 'level'], 'appeals_case_level_unique');
        });
    }

    public function down(): void
    {
        Schema::table('appeals', function (Blueprint $table) {
            $table->dropUnique('appeals_case_level_unique');
        });
    }
};







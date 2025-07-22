<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('case_files', function (Blueprint $table) {
            $table->string('status', 20)->default('Open')->change();
        });
    }

    public function down(): void
    {
        // No need to revert enum changes explicitly
    }
};







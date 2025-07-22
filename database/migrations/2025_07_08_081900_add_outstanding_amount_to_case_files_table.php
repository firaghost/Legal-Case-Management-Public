<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('case_files', function (Blueprint $table) {
            if (!Schema::hasColumn('case_files', 'outstanding_amount')) {
                $table->decimal('outstanding_amount', 15, 2)->default(0)->after('approved_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('case_files', function (Blueprint $table) {
            if (Schema::hasColumn('case_files', 'outstanding_amount')) {
                $table->dropColumn('outstanding_amount');
            }
        });
    }
};







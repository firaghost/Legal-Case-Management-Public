<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('appeals', 'level')) {
            Schema::table('appeals', function (Blueprint $table) {
                $table->string('level', 20)->default('Appeal')->after('id'); // Appeal, Second Appeal, Cassation
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('appeals', 'level')) {
            Schema::table('appeals', function (Blueprint $table) {
                $table->dropColumn('level');
            });
        }
    }
};







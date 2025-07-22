<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'reply_to')) {
                $table->unsignedBigInteger('reply_to')->nullable()->after('type');
                $table->foreign('reply_to')->references('id')->on('messages')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasColumn('messages', 'reply_to')) {
                $table->dropForeign(['reply_to']);
                $table->dropColumn('reply_to');
            }
        });
    }
};







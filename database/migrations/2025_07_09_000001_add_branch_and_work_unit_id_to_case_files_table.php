<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('case_files', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable()->after('title');
            $table->unsignedBigInteger('work_unit_id')->nullable()->after('branch_id');
        });
    }

    public function down()
    {
        Schema::table('case_files', function (Blueprint $table) {
            $table->dropColumn(['branch_id', 'work_unit_id']);
        });
    }
}; 






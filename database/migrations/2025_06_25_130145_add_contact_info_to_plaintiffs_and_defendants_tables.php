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
        // Add columns to plaintiffs table
        Schema::table('plaintiffs', function (Blueprint $table) {
            $table->string('contact_number')->nullable()->after('name');
            $table->text('address')->nullable()->after('contact_number');
            $table->string('email')->nullable()->after('address');
        });

        // Add columns to defendants table
        Schema::table('defendants', function (Blueprint $table) {
            $table->string('contact_number')->nullable()->after('name');
            $table->text('address')->nullable()->after('contact_number');
            $table->string('email')->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop columns from plaintiffs table
        Schema::table('plaintiffs', function (Blueprint $table) {
            $table->dropColumn(['contact_number', 'address', 'email']);
        });

        // Drop columns from defendants table
        Schema::table('defendants', function (Blueprint $table) {
            $table->dropColumn(['contact_number', 'address', 'email']);
        });
    }
};







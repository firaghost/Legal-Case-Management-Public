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
        // Remove court_id foreign key and column from case_files
        Schema::table('case_files', function (Blueprint $table) {
            if (Schema::hasColumn('case_files', 'court_id')) {
                $table->dropForeign(['court_id']);
                $table->dropColumn('court_id');
            }
        });
        // Drop courts table if it exists
        if (Schema::hasTable('courts')) {
            Schema::drop('courts');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate courts table (minimal definition)
        Schema::create('courts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address')->nullable();
            $table->timestamps();
        });
        // Add court_id back to case_files
        Schema::table('case_files', function (Blueprint $table) {
            $table->foreignId('court_id')->nullable()->constrained('courts');
        });
    }
}; 






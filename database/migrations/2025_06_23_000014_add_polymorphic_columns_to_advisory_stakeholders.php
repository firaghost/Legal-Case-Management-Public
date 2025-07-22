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
        // First, add the new columns as nullable
        Schema::table('advisory_stakeholders', function (Blueprint $table) {
            $table->unsignedBigInteger('stakeholderable_id')->nullable()->after('legal_advisory_case_id');
            $table->string('stakeholderable_type')->nullable()->after('stakeholderable_id');
        });

        // Then update the new columns with data from the old column
        \DB::table('advisory_stakeholders')
            ->whereNotNull('legal_advisory_case_id')
            ->update([
                'stakeholderable_id' => \DB::raw('legal_advisory_case_id'),
                'stakeholderable_type' => 'App\\Models\\LegalAdvisoryCase'
            ]);

        // Finally, make the new columns not nullable and drop the old column
        Schema::table('advisory_stakeholders', function (Blueprint $table) {
            $table->unsignedBigInteger('stakeholderable_id')->nullable(false)->change();
            $table->string('stakeholderable_type')->nullable(false)->change();
            
            // Drop the old foreign key and column
            $table->dropForeign(['legal_advisory_case_id']);
            $table->dropColumn('legal_advisory_case_id');
            
            // Add index for better performance with a custom, shorter name
            $table->index(
                ['stakeholderable_id', 'stakeholderable_type'],
                'adv_stakeholderable_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advisory_stakeholders', function (Blueprint $table) {
            // Re-add the old column
            $table->unsignedBigInteger('legal_advisory_case_id')->nullable()->after('id');
            
            // Copy data back
            if (Schema::hasColumn('advisory_stakeholders', 'stakeholderable_id')) {
                \DB::table('advisory_stakeholders')
                    ->where('stakeholderable_type', 'App\\Models\\LegalAdvisoryCase')
                    ->update(['legal_advisory_case_id' => \DB::raw('stakeholderable_id')]);
                
                // Make the column not nullable after populating it
                $table->unsignedBigInteger('legal_advisory_case_id')->nullable(false)->change();
                
                // Add foreign key constraint
                $table->foreign('legal_advisory_case_id')
                    ->references('id')
                    ->on('legal_advisory_cases')
                    ->onDelete('cascade');
            }
            
            // Drop the index and columns
            $table->dropIndex('adv_stakeholderable_index');
            $table->dropColumn(['stakeholderable_id', 'stakeholderable_type']);
        });
    }
};







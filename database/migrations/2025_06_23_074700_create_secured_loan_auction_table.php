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
        Schema::create('secured_loan_auctions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('secured_loan_recovery_case_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('round'); // 1 or 2
            $table->date('auction_date');
            $table->enum('result', ['sold', 'ORGANIZATION_acquired', 'failed']);
            $table->decimal('sold_amount', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('secured_loan_auctions');
    }
};







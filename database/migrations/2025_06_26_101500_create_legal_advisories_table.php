<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legal_advisories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_file_id')->unique()->constrained('case_files')->cascadeOnDelete();
            $table->string('requesting_unit')->nullable();
            $table->string('advisory_type')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_advisories');
    }
}; 






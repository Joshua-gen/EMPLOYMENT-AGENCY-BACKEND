<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employer_job_opening', function (Blueprint $table) {
            $table->foreignId('job_opening_id')->constrained('job_openings')->cascadeOnDelete();
            $table->foreignId('employer_id')->constrained('employers')->restrictOnDelete();
            $table->timestamps();

            $table->primary(['job_opening_id', 'employer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employer_job_opening');
    }
};

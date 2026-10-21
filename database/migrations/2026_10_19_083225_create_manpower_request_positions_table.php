<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manpower_request_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manpower_request_id')->constrained('manpower_requests')->cascadeOnDelete();
            $table->string('position');
            $table->unsignedSmallInteger('number_required');
            $table->text('description');

            // Pagkatapos i-link ni admin sa job opening
            $table->foreignId('job_opening_id')->nullable()->constrained('job_openings')->nullOnDelete();
            $table->unsignedSmallInteger('linked_vacancies')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manpower_request_positions');
    }
};

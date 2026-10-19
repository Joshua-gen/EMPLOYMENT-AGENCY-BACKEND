<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable()->unique(); // APP-2026-013, itinatakda pagkatapos ng create

            // Pinanggalingan (hindi nagbabago kahit ma-assign), channel, at assignment
            $table->enum('source', ['job', 'direct'])->index();
            $table->enum('channel', ['online', 'walk_in'])->default('online')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('job_opening_id')->nullable()->constrained('job_openings')->restrictOnDelete();
            $table->foreignId('employer_id')->nullable()->constrained('employers')->restrictOnDelete();

            // Personal (name, phone, birthday, gender lang ang laging required; pwedeng kulang pa ang walk-in)
            $table->string('name');
            $table->string('email')->nullable()->index();
            $table->string('phone', 50);
            $table->date('birthday');
            $table->string('gender', 20);
            $table->string('civil_status', 20)->nullable();
            $table->text('address')->nullable();

            // Education
            $table->string('educational_attainment', 50)->nullable();
            $table->string('school')->nullable();
            $table->string('course')->nullable();
            $table->unsignedSmallInteger('education_start_year')->nullable();
            $table->unsignedSmallInteger('education_graduated_year')->nullable();

            // Work experience (work_start at work_end ay buwan at taon lang, naka-store bilang unang araw ng buwan)
            $table->string('previous_employer')->nullable();
            $table->text('previous_employer_address')->nullable();
            $table->string('previous_position')->nullable();
            $table->date('work_start')->nullable();
            $table->date('work_end')->nullable(); // walang laman = kasalukuyang nagtatrabaho

            $table->text('skills')->nullable();
            $table->text('certifications')->nullable();
            $table->string('languages')->nullable();

            // Recruitment
            $table->enum('status', [
                'Submitted', 'Under Review', 'Shortlisted', 'Interview',
                'Selected', 'Processing', 'Deployed', 'Rejected',
            ])->default('Submitted')->index();
            $table->text('remarks')->nullable();
            $table->date('applied_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};

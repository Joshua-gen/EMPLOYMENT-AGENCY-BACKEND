<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manpower_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable()->unique(); // MR-2026-001, itinatakda pagkatapos ng create
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending')->index();
            $table->foreignId('employer_id')->nullable()->constrained('employers')->restrictOnDelete();

            // Business details, eksaktong isinumite ng employer (snapshot)
            $table->string('business_name');
            $table->foreignId('business_category_id')->constrained('business_categories')->restrictOnDelete();
            $table->string('country')->index();
            $table->text('business_address');
            $table->string('license_no', 100)->nullable();

            // Contact information
            $table->string('contact_person');
            $table->string('contact_position');
            $table->string('contact_tel', 50);
            $table->string('contact_email');
            $table->string('website')->nullable();

            // Review
            $table->text('remarks')->nullable();
            $table->date('requested_at');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manpower_requests');
    }
};

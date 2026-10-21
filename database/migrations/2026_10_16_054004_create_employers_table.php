<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employers', function (Blueprint $table) {
            $table->id();

            // Business details
            $table->string('name')->unique();

            $table->foreignId('business_category_id')
                ->constrained('business_categories')
                ->restrictOnDelete();

           // $table->string('business_category_name');

            $table->string('country')->index();
            $table->text('address');
            $table->string('license_no')->nullable()->unique();

            // Contact information
            $table->string('contact_person');
            $table->string('contact_position');
            $table->string('contact_tel');
            $table->string('contact_email');
            $table->string('website')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employers');
    }
};

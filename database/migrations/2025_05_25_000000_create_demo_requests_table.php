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
        Schema::create('demo_requests', function (Blueprint $table) {
            $table->id();

            // Section A: Basic Details
            $table->string('full_name');
            $table->string('email');
            $table->string('organization');
            $table->string('job_title');
            $table->string('phone_number');
            $table->string('country');

            // Section B: Event Details
            $table->string('event_type');
            $table->string('event_name')->nullable();
            $table->date('event_date');
            $table->string('event_location');
            $table->string('estimated_attendees');

            // Section C: Needs & Intent
            $table->json('primary_objectives')->nullable(); // array of selected checkboxes
            $table->json('deployment_timeline')->nullable(); // array of selected checkboxes

            // Section D: Qualifier
            $table->string('budget_range')->nullable();

            // Section E: Final Input
            $table->text('additional_notes')->nullable();

            // Metadata
            $table->timestamp('submitted_at')->useCurrent();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            // Indexing for better query performance
            $table->index('email');
            $table->index('submitted_at');
            $table->index('organization');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demo_requests');
    }
};

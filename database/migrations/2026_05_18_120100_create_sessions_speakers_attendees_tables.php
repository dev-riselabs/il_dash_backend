<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('speakers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable()->index();
            $table->string('organization')->nullable();
            $table->string('job_title')->nullable();
            $table->text('bio')->nullable();
            $table->string('photo_url')->nullable();
            $table->string('country')->nullable();
            $table->timestamps();
        });

        Schema::create('attendees', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('job_title')->nullable();
            $table->string('organization')->nullable();
            $table->string('country')->nullable();
            $table->string('region')->nullable();
            $table->string('gender', 16)->nullable(); // male|female|other
            $table->string('category', 64)->nullable(); // Investor|Government|Private Sector|SME/Startup|...
            $table->dateTime('checked_in_at')->nullable();
            $table->boolean('is_new_today')->default(false);
            $table->timestamps();
        });

        // Use `event_sessions` to avoid clashing with the framework's `sessions` table.
        Schema::create('event_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('event_day_id')->nullable()->constrained('event_days')->nullOnDelete();
            $table->foreignId('track_id')->nullable()->constrained('tracks')->nullOnDelete();
            $table->foreignId('venue_id')->nullable()->constrained('venues')->nullOnDelete();
            $table->foreignId('sector_id')->nullable()->constrained('sectors')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type', 32)->default('panel'); // plenary|panel|keynote|roundtable|showcase
            $table->string('status', 32)->default('upcoming'); // upcoming|next|live|completed|cancelled|delayed
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->text('ai_summary')->nullable();
            $table->string('transcript_url')->nullable();
            $table->unsignedInteger('attendance_in_person')->default(0);
            $table->unsignedInteger('attendance_virtual')->default(0);
            $table->unsignedTinyInteger('average_rating_x10')->nullable(); // store rating *10 (e.g. 47 = 4.7)
            $table->timestamps();
            $table->index(['status', 'starts_at']);
        });

        Schema::create('session_speakers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_session_id')->constrained('event_sessions')->cascadeOnDelete();
            $table->foreignId('speaker_id')->constrained('speakers')->cascadeOnDelete();
            $table->string('role', 32)->default('panelist'); // keynote|panelist|moderator
            $table->timestamps();
            $table->unique(['event_session_id', 'speaker_id']);
        });

        Schema::create('session_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_session_id')->constrained('event_sessions')->cascadeOnDelete();
            $table->foreignId('attendee_id')->constrained('attendees')->cascadeOnDelete();
            $table->string('mode', 16)->default('in_person'); // in_person|virtual
            $table->dateTime('joined_at')->nullable();
            $table->dateTime('left_at')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->timestamps();
            $table->unique(['event_session_id', 'attendee_id']);
        });

        Schema::create('session_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_session_id')->constrained('event_sessions')->cascadeOnDelete();
            $table->string('name');
            $table->string('type', 16); // PDF|PPTX|ZIP|MP4
            $table->string('url');
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->timestamps();
        });

        Schema::create('session_timeline_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_session_id')->constrained('event_sessions')->cascadeOnDelete();
            $table->dateTime('occurred_at');
            $table->string('kind', 64); // Started|OpeningRemarks|Keynote|PanelDiscussion|QA|Ended
            $table->string('actor_name')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('session_insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_session_id')->constrained('event_sessions')->cascadeOnDelete();
            $table->text('body');
            $table->string('kind', 32)->default('insight'); // insight|theme
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('attendance_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_day_id')->constrained('event_days')->cascadeOnDelete();
            $table->dateTime('captured_at');
            $table->unsignedInteger('total')->default(0);
            $table->unsignedInteger('in_person')->default(0);
            $table->unsignedInteger('virtual')->default(0);
            $table->timestamps();
            $table->index(['event_day_id', 'captured_at']);
        });

        Schema::create('speaker_engagement_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('speaker_id')->constrained('speakers')->cascadeOnDelete();
            $table->foreignId('event_day_id')->nullable()->constrained('event_days')->nullOnDelete();
            $table->unsignedInteger('sessions_count')->default(0);
            $table->unsignedTinyInteger('score')->default(0); // 0..100
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('speaker_engagement_scores');
        Schema::dropIfExists('attendance_snapshots');
        Schema::dropIfExists('session_insights');
        Schema::dropIfExists('session_timeline_events');
        Schema::dropIfExists('session_resources');
        Schema::dropIfExists('session_attendance');
        Schema::dropIfExists('session_speakers');
        Schema::dropIfExists('event_sessions');
        Schema::dropIfExists('attendees');
        Schema::dropIfExists('speakers');
    }
};

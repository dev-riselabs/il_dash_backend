<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->string('type', 64); // Crowd Congestion|Access Control|Medical|Fire|Other
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('venue_id')->nullable()->constrained('venues')->nullOnDelete();
            $table->dateTime('occurred_at');
            $table->string('severity', 16)->default('medium'); // low|medium|high|critical
            $table->string('status', 16)->default('open'); // open|responding|resolved
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('resolved_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'severity']);
        });

        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('severity', 16)->default('info'); // info|low|medium|high|critical|warning
            $table->string('source', 64)->nullable(); // Programme Tracker|Security|Sentiment|Deal Room|System
            $table->unsignedBigInteger('source_ref_id')->nullable();
            $table->string('status', 16)->default('unread'); // unread|read|resolved
            $table->dateTime('resolved_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'severity']);
        });

        Schema::create('security_stats_snapshots', function (Blueprint $table) {
            $table->id();
            $table->dateTime('captured_at');
            $table->unsignedInteger('personnel_on_duty')->default(0);
            $table->unsignedInteger('incidents_today')->default(0);
            $table->unsignedInteger('incidents_resolved')->default(0);
            $table->unsignedInteger('avg_response_seconds')->default(0);
            $table->string('safety_level', 16)->default('high'); // low|medium|high|critical
            $table->json('venue_status')->nullable();
            $table->timestamps();
        });

        Schema::create('cameras', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('venue_id')->nullable()->constrained('venues')->nullOnDelete();
            $table->string('stream_url')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->string('status', 16)->default('live'); // live|delayed|offline
            $table->timestamps();
        });

        Schema::create('actions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('related_to', 32)->nullable(); // session|incident|deal|resolution
            $table->unsignedBigInteger('related_id')->nullable();
            $table->foreignId('sector_id')->nullable()->constrained('sectors')->nullOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 16)->default('pending'); // pending|in_progress|done|blocked
            $table->dateTime('due_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'due_at']);
        });

        Schema::create('quick_actions_log', function (Blueprint $table) {
            $table->id();
            $table->string('action_type', 64); // Broadcast|AlertSecurity|UpdateSessionStatus|...
            $table->json('payload')->nullable();
            $table->foreignId('triggered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('triggered_at');
            $table->timestamps();
        });

        Schema::create('command_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('body');
            $table->string('attached_to_type', 64)->nullable();
            $table->unsignedBigInteger('attached_to_id')->nullable();
            $table->timestamps();
        });

        Schema::create('broadcasts', function (Blueprint $table) {
            $table->id();
            $table->string('channel', 32)->default('all'); // all|attendees|security|investors
            $table->string('title');
            $table->text('message');
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('sent_at');
            $table->timestamps();
        });

        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('kind', 32); // executive_summary|attendance|investment|sentiment
            $table->foreignId('event_day_id')->nullable()->constrained('event_days')->nullOnDelete();
            $table->dateTime('range_start')->nullable();
            $table->dateTime('range_end')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('generated_at');
            $table->string('file_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
        Schema::dropIfExists('broadcasts');
        Schema::dropIfExists('command_notes');
        Schema::dropIfExists('quick_actions_log');
        Schema::dropIfExists('actions');
        Schema::dropIfExists('cameras');
        Schema::dropIfExists('security_stats_snapshots');
        Schema::dropIfExists('alerts');
        Schema::dropIfExists('incidents');
    }
};

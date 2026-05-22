<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resolutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_session_id')->nullable()->constrained('event_sessions')->nullOnDelete();
            $table->foreignId('track_id')->nullable()->constrained('tracks')->nullOnDelete();
            $table->foreignId('sector_id')->nullable()->constrained('sectors')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category', 32)->default('commitment'); // commitment|partnership|policy|keynote|panel
            $table->string('committed_by')->nullable();
            $table->string('stage', 32)->default('commitment'); // commitment|negotiation|signed|fulfilled
            $table->string('status', 32)->default('open'); // open|in_progress|completed
            $table->unsignedBigInteger('estimated_impact_naira')->nullable(); // in kobo
            $table->dateTime('recorded_at');
            $table->timestamps();
            $table->index(['stage', 'recorded_at']);
        });

        Schema::create('session_quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_session_id')->constrained('event_sessions')->cascadeOnDelete();
            $table->foreignId('speaker_id')->nullable()->constrained('speakers')->nullOnDelete();
            $table->text('quote_text');
            $table->dateTime('said_at');
            $table->timestamps();
            $table->index(['event_session_id', 'said_at']);
        });

        Schema::create('feedback_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendee_id')->nullable()->constrained('attendees')->nullOnDelete();
            $table->foreignId('event_session_id')->nullable()->constrained('event_sessions')->nullOnDelete();
            $table->string('channel', 32)->default('qr'); // qr|mobile|website|other
            $table->unsignedTinyInteger('star_rating'); // 1..5
            $table->text('review_text')->nullable();
            $table->text('key_takeaway')->nullable();
            $table->string('sentiment_label', 16)->nullable(); // positive|neutral|negative
            $table->decimal('sentiment_score', 4, 3)->nullable(); // -1.000..1.000
            $table->dateTime('submitted_at');
            $table->timestamps();
            $table->index(['event_session_id', 'submitted_at']);
            $table->index('sentiment_label');
        });

        Schema::create('live_polls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_session_id')->nullable()->constrained('event_sessions')->nullOnDelete();
            $table->string('prompt');
            $table->json('options'); // ["Excellent","Good","Average","Not Good"]
            $table->string('status', 16)->default('open'); // open|closed
            $table->dateTime('opened_at')->nullable();
            $table->dateTime('closed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('live_poll_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_poll_id')->constrained('live_polls')->cascadeOnDelete();
            $table->foreignId('attendee_id')->nullable()->constrained('attendees')->nullOnDelete();
            $table->string('option');
            $table->dateTime('submitted_at');
            $table->timestamps();
            $table->index(['live_poll_id', 'option']);
        });

        Schema::create('sentiment_scores', function (Blueprint $table) {
            $table->id();
            $table->string('scope', 32); // overall|session|sector|track
            $table->unsignedBigInteger('scope_id')->nullable();
            $table->dateTime('captured_at');
            $table->decimal('positive_pct', 5, 2)->default(0);
            $table->decimal('neutral_pct', 5, 2)->default(0);
            $table->decimal('negative_pct', 5, 2)->default(0);
            $table->unsignedTinyInteger('score_0_100')->default(0);
            $table->timestamps();
            $table->index(['scope', 'scope_id', 'captured_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sentiment_scores');
        Schema::dropIfExists('live_poll_responses');
        Schema::dropIfExists('live_polls');
        Schema::dropIfExists('feedback_submissions');
        Schema::dropIfExists('session_quotes');
        Schema::dropIfExists('resolutions');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_themes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('mention_count')->default(0);
            $table->timestamps();
        });

        Schema::create('social_hashtags', function (Blueprint $table) {
            $table->id();
            $table->string('tag')->unique();
            $table->unsignedInteger('mention_count')->default(0);
            $table->timestamps();
        });

        Schema::create('social_mentions', function (Blueprint $table) {
            $table->id();
            $table->string('platform', 32); // twitter|linkedin|facebook|instagram|youtube
            $table->string('author_handle')->nullable();
            $table->string('author_name')->nullable();
            $table->string('author_avatar')->nullable();
            $table->boolean('author_verified')->default(false);
            $table->string('post_id')->nullable()->index();
            $table->text('content');
            $table->dateTime('posted_at');
            $table->unsignedInteger('likes')->default(0);
            $table->unsignedInteger('retweets')->default(0);
            $table->unsignedInteger('comments')->default(0);
            $table->unsignedInteger('impressions')->default(0);
            $table->unsignedInteger('reach')->default(0);
            $table->string('sentiment_label', 16)->nullable(); // positive|neutral|negative
            $table->decimal('sentiment_score', 4, 3)->nullable();
            $table->foreignId('theme_id')->nullable()->constrained('social_themes')->nullOnDelete();
            $table->json('hashtags')->nullable();
            $table->string('location')->nullable();
            $table->timestamps();
            $table->index(['platform', 'posted_at']);
            $table->index('sentiment_label');
        });

        Schema::create('mentions_timeseries', function (Blueprint $table) {
            $table->id();
            $table->dateTime('captured_at');
            $table->string('platform', 32)->nullable();
            $table->unsignedInteger('mentions_count')->default(0);
            $table->timestamps();
            $table->index(['captured_at', 'platform']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mentions_timeseries');
        Schema::dropIfExists('social_mentions');
        Schema::dropIfExists('social_hashtags');
        Schema::dropIfExists('social_themes');
    }
};

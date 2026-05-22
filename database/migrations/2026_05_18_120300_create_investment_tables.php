<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type', 32)->default('institutional'); // institutional|VC|bank|sovereign|corporate
            $table->string('country')->nullable();
            $table->string('region', 32)->nullable(); // Africa|Europe|North America|Asia|Other
            $table->string('logo_url')->nullable();
            $table->json('sectors_of_interest')->nullable();
            $table->timestamps();
            $table->index(['region', 'type']);
        });

        Schema::create('investment_signals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investor_id')->nullable()->constrained('investors')->nullOnDelete();
            $table->foreignId('sector_id')->nullable()->constrained('sectors')->nullOnDelete();
            $table->foreignId('event_session_id')->nullable()->constrained('event_sessions')->nullOnDelete();
            $table->string('confidence', 16)->default('medium'); // high|medium|low
            $table->unsignedBigInteger('estimated_value_naira')->nullable(); // in kobo
            $table->text('note')->nullable();
            $table->dateTime('recorded_at');
            $table->timestamps();
            $table->index(['sector_id', 'recorded_at']);
        });

        Schema::create('sector_investment_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sector_id')->constrained('sectors')->cascadeOnDelete();
            $table->unsignedInteger('signals_count')->default(0);
            $table->unsignedBigInteger('estimated_value_naira')->default(0);
            $table->decimal('trend_percent', 6, 2)->default(0);
            $table->dateTime('captured_at');
            $table->timestamps();
            $table->index(['sector_id', 'captured_at']);
        });

        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('investor_id')->nullable()->constrained('investors')->nullOnDelete();
            $table->foreignId('sector_id')->nullable()->constrained('sectors')->nullOnDelete();
            $table->string('stage', 32)->default('discussion'); // discussion|negotiation|commitment|closed_won|closed_lost
            $table->unsignedBigInteger('value_naira')->default(0); // in kobo
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('opened_at');
            $table->timestamps();
            $table->index(['stage', 'updated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deals');
        Schema::dropIfExists('sector_investment_summaries');
        Schema::dropIfExists('investment_signals');
        Schema::dropIfExists('investors');
    }
};

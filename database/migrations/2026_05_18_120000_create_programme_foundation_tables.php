<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->string('status', 32)->default('upcoming'); // upcoming|live|completed|cancelled
            $table->timestamps();
        });

        Schema::create('event_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->unsignedSmallInteger('day_no');
            $table->date('date');
            $table->string('label')->nullable(); // e.g. "Day 1 (May 10, 2026)"
            $table->timestamps();
            $table->unique(['event_id', 'day_no']);
        });

        Schema::create('tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->nullable()->constrained('events')->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('color', 16)->nullable();
            $table->timestamps();
            $table->unique(['event_id', 'slug']);
        });

        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('capacity')->nullable();
            $table->string('status', 32)->default('open'); // open|closed|restricted
            $table->timestamps();
        });

        Schema::create('sectors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color', 16)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sectors');
        Schema::dropIfExists('venues');
        Schema::dropIfExists('tracks');
        Schema::dropIfExists('event_days');
        Schema::dropIfExists('events');
    }
};

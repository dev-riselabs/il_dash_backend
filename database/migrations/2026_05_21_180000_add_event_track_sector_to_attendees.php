<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendees', function (Blueprint $table) {
            $table->foreignId('event_id')->nullable()->after('id')->constrained('events')->nullOnDelete();
            $table->foreignId('track_id')->nullable()->after('event_id')->constrained('tracks')->nullOnDelete();
            $table->foreignId('sector_id')->nullable()->after('track_id')->constrained('sectors')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('attendees', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Event::class);
            $table->dropForeignIdFor(\App\Models\Track::class);
            $table->dropForeignIdFor(\App\Models\Sector::class);
        });
    }
};

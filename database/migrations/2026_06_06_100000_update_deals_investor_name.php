<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            // Drop foreign keys and columns
            $table->dropForeign(['investor_id']);
            $table->dropForeign(['owner_id']);
            $table->dropColumn(['investor_id', 'owner_id']);
            
            // Add investor_name column
            $table->string('investor_name', 255)->nullable()->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            // Remove investor_name
            $table->dropColumn('investor_name');
            
            // Add back old columns
            $table->foreignId('investor_id')->nullable()->constrained('investors')->nullOnDelete()->after('title');
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete()->after('opened_at');
        });
    }
};

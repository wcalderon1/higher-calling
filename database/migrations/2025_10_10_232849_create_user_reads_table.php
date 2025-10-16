<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // if you're only tracking Devotionals for now:
            $table->foreignId('devotional_id')->constrained()->cascadeOnDelete();
            $table->date('read_on')->index(); // the calendar day the user read it
            $table->timestamps();

            $table->unique(['user_id', 'read_on']); // one “read” per day per user (Phase 1)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_reads');
    }
};

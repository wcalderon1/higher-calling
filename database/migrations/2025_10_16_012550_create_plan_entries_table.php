<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('plan_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('day_number');              // 1..length_days
            $table->foreignId('devotional_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title')->nullable();               // fallback label if no devotional
            $table->text('scripture_ref')->nullable();         // e.g., "John 1:1–18"
            $table->timestamps();

            $table->unique(['plan_id', 'day_number']);
            $table->index(['plan_id', 'devotional_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('plan_entries');
    }
};

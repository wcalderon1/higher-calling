<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_plan_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_plan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_entry_id')->constrained()->cascadeOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_plan_id', 'plan_entry_id']);
            $table->index('completed_at');
        });
    }
    public function down(): void {
        Schema::dropIfExists('user_plan_entries');
    }
};

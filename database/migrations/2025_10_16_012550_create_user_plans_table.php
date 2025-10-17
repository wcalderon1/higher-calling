<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->date('start_date')->nullable();
            $table->enum('status', ['active','paused','completed'])->default('active');
            $table->timestamps();

            $table->unique(['user_id', 'plan_id']);
            $table->index(['plan_id', 'status']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('user_plans');
    }
};

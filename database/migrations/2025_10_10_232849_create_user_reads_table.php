<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('devotional_id')->constrained('devotionals')->cascadeOnDelete();
            $table->date('read_on');                       // one row per user per day
            $table->unique(['user_id','read_on']);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('user_reads');
    }
};

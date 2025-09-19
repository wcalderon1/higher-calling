<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('devotional_tag', function (Blueprint $table) {
            $table->foreignId('devotional_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['devotional_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devotional_tag');
        Schema::dropIfExists('tags');
    }
};

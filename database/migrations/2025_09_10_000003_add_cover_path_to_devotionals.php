<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('devotionals', function (Blueprint $table) {
            $table->string('cover_path')->nullable()->after('excerpt');
        });
    }

    public function down(): void
    {
        Schema::table('devotionals', function (Blueprint $table) {
            $table->dropColumn('cover_path');
        });
    }
};

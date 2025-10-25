<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->boolean('is_admin')->default(false)->after('password');
        });

        Schema::table('devotionals', function (Blueprint $t) {
            $t->boolean('is_curated')->default(false)->after('published_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->dropColumn('is_admin');
        });

        Schema::table('devotionals', function (Blueprint $t) {
            $t->dropColumn('is_curated');
        });
    }
};

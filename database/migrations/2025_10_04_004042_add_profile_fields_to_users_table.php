<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('display_name', 80)->nullable()->after('name');
        $table->string('avatar_path')->nullable()->after('display_name');
        $table->text('bio')->nullable()->after('avatar_path');
        $table->index('display_name');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropIndex(['display_name']);
        $table->dropColumn(['display_name','avatar_path','bio']);
    });
}

};

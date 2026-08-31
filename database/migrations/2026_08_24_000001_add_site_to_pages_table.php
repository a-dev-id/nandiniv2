<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table): void {
            $table->string('site', 10)->default('main')->after('id');
            $table->index(['site', 'slug', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table): void {
            $table->dropIndex(['site', 'slug', 'is_active']);
            $table->dropColumn('site');
        });
    }
};

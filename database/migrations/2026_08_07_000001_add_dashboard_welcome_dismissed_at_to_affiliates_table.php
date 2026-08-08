<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('affiliates', function (Blueprint $table): void {
            $table->timestamp('dashboard_welcome_dismissed_at')->nullable()->after('short_link_activated_at');
        });
    }

    public function down(): void
    {
        Schema::table('affiliates', function (Blueprint $table): void {
            $table->dropColumn('dashboard_welcome_dismissed_at');
        });
    }
};

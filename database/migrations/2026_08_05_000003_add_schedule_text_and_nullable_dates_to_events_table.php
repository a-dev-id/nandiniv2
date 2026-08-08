<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->dateTime('event_start_at')->nullable()->change();
            $table->dateTime('event_end_at')->nullable()->change();
            $table->string('schedule_text')->nullable()->after('event_type');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->dropColumn('schedule_text');
            $table->dateTime('event_start_at')->nullable(false)->change();
            $table->dateTime('event_end_at')->nullable(false)->change();
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->unsignedSmallInteger('travel_time_minutes')->nullable()->after('duration');
        });
    }

    public function down(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->dropColumn('travel_time_minutes');
        });
    }
};

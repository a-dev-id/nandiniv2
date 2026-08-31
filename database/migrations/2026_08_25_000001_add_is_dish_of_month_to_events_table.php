<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->boolean('is_dish_of_month')
                ->default(false)
                ->after('schedule_text')
                ->index();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->dropIndex(['is_dish_of_month']);
            $table->dropColumn('is_dish_of_month');
        });
    }
};

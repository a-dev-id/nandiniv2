<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('synced_webhotelier_bookings', function (Blueprint $table) {
            $table->boolean('member_assigned_manually')
                ->default(false)
                ->after('member_id');
        });
    }

    public function down(): void
    {
        Schema::table('synced_webhotelier_bookings', function (Blueprint $table) {
            $table->dropColumn('member_assigned_manually');
        });
    }
};

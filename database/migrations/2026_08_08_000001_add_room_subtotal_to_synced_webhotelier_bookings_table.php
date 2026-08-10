<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('synced_webhotelier_bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('synced_webhotelier_bookings', 'room_subtotal')) {
                $table->decimal('room_subtotal', 14, 2)->nullable()->after('currency');
            }
        });
    }

    public function down(): void
    {
        Schema::table('synced_webhotelier_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('synced_webhotelier_bookings', 'room_subtotal')) {
                $table->dropColumn('room_subtotal');
            }
        });
    }
};

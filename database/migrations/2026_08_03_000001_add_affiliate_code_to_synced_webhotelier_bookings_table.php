<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('synced_webhotelier_bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('synced_webhotelier_bookings', 'affiliate_code')) {
                $table->string('affiliate_code')->nullable()->index()->after('source_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('synced_webhotelier_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('synced_webhotelier_bookings', 'affiliate_code')) {
                $table->dropColumn('affiliate_code');
            }
        });
    }
};

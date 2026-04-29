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
        Schema::table('offers', function (Blueprint $table) {
            if (! Schema::hasColumn('offers', 'booking_checkin_date')) {
                $table->date('booking_checkin_date')->nullable()->after('valid_end_date');
            }

            if (! Schema::hasColumn('offers', 'booking_nights')) {
                $table->unsignedTinyInteger('booking_nights')->nullable()->after('booking_checkin_date');
            }

            if (! Schema::hasColumn('offers', 'booking_rooms')) {
                $table->unsignedTinyInteger('booking_rooms')->nullable()->after('booking_nights');
            }

            if (! Schema::hasColumn('offers', 'booking_adults')) {
                $table->unsignedTinyInteger('booking_adults')->nullable()->after('booking_rooms');
            }

            if (! Schema::hasColumn('offers', 'booking_rate_code')) {
                $table->string('booking_rate_code', 100)->nullable()->after('booking_adults');
            }

            if (! Schema::hasColumn('offers', 'booking_bkcode')) {
                $table->string('booking_bkcode', 100)->nullable()->after('booking_rate_code');
            }

            if (! Schema::hasColumn('offers', 'booking_url_override')) {
                $table->text('booking_url_override')->nullable()->after('booking_bkcode');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $columns = [
                'booking_checkin_date',
                'booking_nights',
                'booking_rooms',
                'booking_adults',
                'booking_rate_code',
                'booking_bkcode',
                'booking_url_override',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('offers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

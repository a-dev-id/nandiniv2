<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            if (! Schema::hasColumn('members', 'booking_check_in')) {
                $table->date('booking_check_in')->nullable()->after('address');
            }

            if (! Schema::hasColumn('members', 'booking_check_out')) {
                $table->date('booking_check_out')->nullable()->after('booking_check_in');
                $table->index('booking_check_out');
            }

            if (! Schema::hasColumn('members', 'checkout_notification_sent_at')) {
                $table->timestamp('checkout_notification_sent_at')->nullable()->after('membership_expiry_reminder_sent_at');
                $table->index('checkout_notification_sent_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            foreach (['booking_check_in', 'booking_check_out', 'checkout_notification_sent_at'] as $column) {
                if (Schema::hasColumn('members', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

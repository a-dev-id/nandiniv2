<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->date('booking_check_in')->nullable()->after('address');
            $table->date('booking_check_out')->nullable()->after('booking_check_in');
            $table->timestamp('checkout_notification_sent_at')->nullable()->after('membership_expiry_reminder_sent_at');

            $table->index('booking_check_out');
            $table->index('checkout_notification_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropIndex(['booking_check_out']);
            $table->dropIndex(['checkout_notification_sent_at']);
            $table->dropColumn([
                'booking_check_in',
                'booking_check_out',
                'checkout_notification_sent_at',
            ]);
        });
    }
};

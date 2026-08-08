<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('affiliate_bookings', function (Blueprint $table): void {
            $table->string('manual_booking_status')->nullable()->after('source_status');
            $table->text('manual_status_reason')->nullable()->after('manual_booking_status');
            $table->foreignId('manual_status_set_by')->nullable()->after('manual_status_reason')->constrained('users')->nullOnDelete();
            $table->timestamp('manual_status_set_at')->nullable()->after('manual_status_set_by');
        });
    }

    public function down(): void
    {
        Schema::table('affiliate_bookings', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('manual_status_set_by');
            $table->dropColumn(['manual_booking_status', 'manual_status_reason', 'manual_status_set_at']);
        });
    }
};

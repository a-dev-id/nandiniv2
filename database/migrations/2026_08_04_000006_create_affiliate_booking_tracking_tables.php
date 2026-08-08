<?php

use App\Enums\AffiliateBookingStatus;
use App\Enums\AffiliateCommissionState;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_bookings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('affiliate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('synced_webhotelier_booking_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source_system', 50);
            $table->string('external_booking_id', 120);
            $table->string('external_booking_reference')->nullable();
            $table->string('affiliate_code_snapshot')->index();
            $table->date('check_in_date')->index();
            $table->date('check_out_date')->index();
            $table->unsignedSmallInteger('stay_nights');
            $table->decimal('room_revenue_amount', 15, 2)->nullable();
            $table->string('currency', 10)->nullable();
            $table->string('booking_status')->default(AffiliateBookingStatus::Unknown->value)->index();
            $table->string('source_status')->nullable();
            $table->decimal('commission_rate_snapshot', 5, 2);
            $table->decimal('estimated_commission_amount', 15, 2)->nullable();
            $table->string('commission_state')->default(AffiliateCommissionState::CalculationUnavailable->value)->index();
            $table->string('attribution_warning')->nullable();
            $table->string('calculation_unavailable_reason')->nullable();
            $table->text('synchronization_warning')->nullable();
            $table->timestamp('source_created_at')->nullable();
            $table->timestamp('source_updated_at')->nullable()->index();
            $table->timestamp('last_synced_at')->index();
            $table->char('data_fingerprint', 64);
            $table->timestamps();

            $table->unique(['source_system', 'external_booking_id'], 'affiliate_booking_source_unique');
            $table->index(['affiliate_id', 'check_in_date'], 'affiliate_booking_affiliate_checkin_index');
            $table->index(['affiliate_id', 'booking_status'], 'affiliate_booking_affiliate_status_index');
            $table->index(['affiliate_id', 'commission_state'], 'affiliate_booking_affiliate_commission_index');
        });

        Schema::create('affiliate_booking_rooms', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('affiliate_booking_id')->constrained()->cascadeOnDelete();
            $table->string('external_room_id', 120);
            $table->string('room_type_name');
            $table->unsignedSmallInteger('room_quantity')->default(1);
            $table->unsignedSmallInteger('stay_nights');
            $table->decimal('room_revenue_amount', 15, 2)->nullable();
            $table->string('currency', 10)->nullable();
            $table->char('line_fingerprint', 64);
            $table->timestamps();

            $table->unique(['affiliate_booking_id', 'external_room_id'], 'affiliate_booking_room_unique');
        });

        Log::info('Affiliate booking tracking migration executed.');
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_booking_rooms');
        Schema::dropIfExists('affiliate_bookings');
    }
};

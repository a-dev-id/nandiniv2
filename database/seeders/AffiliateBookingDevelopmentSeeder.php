<?php

namespace Database\Seeders;

use App\Models\Affiliate;
use App\Services\Affiliate\Booking\AffiliateBookingData;
use App\Services\Affiliate\Booking\SyncAffiliateBookingService;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class AffiliateBookingDevelopmentSeeder extends Seeder
{
    public function run(): void
    {
        $affiliate = Affiliate::query()->where('status', 'approved')->first();

        if (! $affiliate || blank($affiliate->affiliate_code)) {
            return;
        }

        $now = CarbonImmutable::now();
        $fixtures = [
            ['confirmed', 14, 18, 'Jungle View Villa', '3686000.00', 'IDR', 1],
            ['checked_in', -1, 2, 'Panorama View Villa', '4200000.00', 'IDR', 1],
            ['completed', -12, -9, 'Jungle View Villa', '5000000.00', 'IDR', 1],
            ['cancelled', 21, 24, 'Sunrise View Villa', '3600000.00', 'IDR', 1],
            ['no_show', -7, -4, 'Jungle View Villa', '3200000.00', 'IDR', 1],
            ['refunded', -20, -17, 'Panorama View Villa', '4700000.00', 'IDR', 1],
            ['awaiting_review', 30, 33, 'Jungle View Villa', '3900000.00', 'IDR', 1],
            ['confirmed', 40, 43, 'Room details unavailable', null, 'IDR', 1],
            ['confirmed', 50, 54, 'Two-room stay', '2200.00', 'USD', 2],
        ];

        foreach ($fixtures as $index => [$status, $checkInOffset, $checkOutOffset, $roomType, $revenue, $currency, $quantity]) {
            $rooms = $index === 8
                ? [
                    ['external_room_id' => 'room-a', 'room_type_name' => 'Jungle View Villa', 'room_quantity' => 1, 'stay_nights' => 99, 'room_revenue_amount' => '1200.00', 'currency' => 'USD'],
                    ['external_room_id' => 'room-b', 'room_type_name' => 'Panorama View Villa', 'room_quantity' => 1, 'room_revenue_amount' => '1000.00', 'currency' => 'USD'],
                ]
                : [[
                    'external_room_id' => 'room-1',
                    'room_type_name' => $roomType,
                    'room_quantity' => $quantity,
                    'room_revenue_amount' => $revenue,
                    'currency' => $currency,
                ]];

            $data = new AffiliateBookingData(
                sourceSystem: 'local_development_fixture',
                externalBookingId: 'local-affiliate-booking-'.$affiliate->id.'-'.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                externalBookingReference: 'LOCAL-'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                affiliateCode: $affiliate->affiliate_code,
                roomItems: $rooms,
                checkInDate: $now->addDays($checkInOffset)->toDateString(),
                checkOutDate: $now->addDays($checkOutOffset)->toDateString(),
                roomRevenueAmount: $revenue,
                currency: $currency,
                bookingStatus: $status,
                sourceCreatedAt: $now->subDay(),
                sourceUpdatedAt: $now,
            );

            app(SyncAffiliateBookingService::class)->sync($data);

            if ($index === 0) {
                app(SyncAffiliateBookingService::class)->sync($data);
            }
        }

        app(SyncAffiliateBookingService::class)->sync(new AffiliateBookingData(
            sourceSystem: 'local_development_fixture',
            externalBookingId: 'local-unknown-affiliate-code',
            externalBookingReference: null,
            affiliateCode: 'unknown-local-code',
            roomItems: [],
            checkInDate: $now->addDays(10)->toDateString(),
            checkOutDate: $now->addDays(12)->toDateString(),
            roomRevenueAmount: '1000.00',
            currency: 'USD',
            bookingStatus: 'confirmed',
            sourceUpdatedAt: $now,
        ));

        $cancelled = new AffiliateBookingData(
            sourceSystem: 'local_development_fixture',
            externalBookingId: 'local-stale-update-check-'.$affiliate->id,
            externalBookingReference: null,
            affiliateCode: $affiliate->affiliate_code,
            roomItems: [['external_room_id' => 'room-1', 'room_type_name' => 'Jungle View Villa']],
            checkInDate: $now->addDays(60)->toDateString(),
            checkOutDate: $now->addDays(62)->toDateString(),
            roomRevenueAmount: '2000000.00',
            currency: 'IDR',
            bookingStatus: 'cancelled',
            sourceUpdatedAt: $now,
        );
        app(SyncAffiliateBookingService::class)->sync($cancelled);
        app(SyncAffiliateBookingService::class)->sync(new AffiliateBookingData(
            sourceSystem: $cancelled->sourceSystem,
            externalBookingId: $cancelled->externalBookingId,
            externalBookingReference: null,
            affiliateCode: $cancelled->affiliateCode,
            roomItems: $cancelled->roomItems,
            checkInDate: $cancelled->checkInDate,
            checkOutDate: $cancelled->checkOutDate,
            roomRevenueAmount: $cancelled->roomRevenueAmount,
            currency: $cancelled->currency,
            bookingStatus: 'confirmed',
            sourceUpdatedAt: $now->subHour(),
        ));
    }
}

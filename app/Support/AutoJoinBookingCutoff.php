<?php

namespace App\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Throwable;

class AutoJoinBookingCutoff
{
    private const BOOKING_CREATED_AFTER = '2026-07-01';

    public static function wasCreatedAfterCutoff(array $payload): bool
    {
        $bookingCreatedAt = self::bookingCreatedAt($payload);

        if (! $bookingCreatedAt) {
            return false;
        }

        return $bookingCreatedAt->gt(Carbon::parse(self::BOOKING_CREATED_AFTER)->endOfDay());
    }

    private static function bookingCreatedAt(array $payload): ?Carbon
    {
        foreach (self::bookingCreatedAtKeys() as $key) {
            $value = self::payloadValue($payload, $key);

            if (! filled($value)) {
                continue;
            }

            try {
                return Carbon::parse($value);
            } catch (Throwable) {
                continue;
            }
        }

        return null;
    }

    private static function payloadValue(array $payload, string $key): mixed
    {
        if (array_key_exists($key, $payload)) {
            return $payload[$key];
        }

        return Arr::get($payload, $key);
    }

    /**
     * @return array<int, string>
     */
    private static function bookingCreatedAtKeys(): array
    {
        return [
            'booking_created_at',
            'booking_created_date',
            'booking_date',
            'booked_at',
            'booked_on',
            'reservation_created_at',
            'reservation_date',
            'created_at',
            'createdAt',
            'dateCreated',
            'date_created',
            'bookInfo.created',
            'bookInfo.createdAt',
            'bookInfo.date',
            'bookInfo.bookingDate',
        ];
    }
}

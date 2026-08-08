<?php

namespace App\Services\Affiliate\Booking;

use App\Enums\AffiliateBookingStatus;
use App\Models\AffiliateAuditEvent;
use App\Models\AffiliateBooking;
use App\Models\AffiliateProgramSetting;
use App\Services\Affiliate\Finance\SynchronizeAffiliateCommissionItemService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncAffiliateBookingService
{
    public function __construct(
        private readonly AffiliateBookingMatcher $matcher,
        private readonly AffiliateBookingStatusMapper $statuses,
        private readonly AffiliateCommissionEstimator $commissions,
        private readonly SynchronizeAffiliateCommissionItemService $commissionItems,
    ) {}

    public function sync(AffiliateBookingData $data): AffiliateBookingSyncResult
    {
        $sourceSystem = mb_strtolower(trim($data->sourceSystem));
        $externalId = trim($data->externalBookingId);

        if ($sourceSystem === '' || $externalId === '') {
            return $this->failedValidation('Source system and external booking ID are required.');
        }

        if (mb_strlen($sourceSystem) > 50 || mb_strlen($externalId) > 120) {
            return $this->failedValidation(
                'Source system or external booking ID exceeds the supported length.',
                mb_substr($sourceSystem, 0, 50),
                mb_substr($externalId, 0, 120),
            );
        }

        $existing = AffiliateBooking::query()
            ->where('source_system', $sourceSystem)
            ->where('external_booking_id', $externalId)
            ->first();

        if ($existing && $this->isStale($existing, $data)) {
            $this->audit($existing->affiliate_id, 'affiliate_booking.stale_update_ignored', [
                'affiliate_booking_id' => $existing->id,
                'source_system' => $sourceSystem,
                'external_booking_id' => $externalId,
                'stored_source_updated_at' => $existing->source_updated_at?->toIso8601String(),
                'incoming_source_updated_at' => $data->sourceUpdatedAt?->toIso8601String(),
            ]);
            $existing->forceFill(['last_synced_at' => now()])->saveQuietly();

            return new AffiliateBookingSyncResult('stale_update_ignored', $existing);
        }

        $incomingCode = $this->matcher->normalize($data->affiliateCode);
        $warnings = [];

        if ($existing) {
            $affiliate = $existing->affiliate;

            if ($incomingCode !== null && $incomingCode !== mb_strtolower($existing->affiliate_code_snapshot)) {
                $warnings[] = 'Incoming Affiliate code differed; historical attribution was preserved.';
            }
        } else {
            if ($incomingCode === null) {
                return $this->skipped('skipped_no_affiliate_code', $sourceSystem, $externalId);
            }

            $affiliate = $this->matcher->match($incomingCode);

            if (! $affiliate) {
                return $this->skipped('skipped_unknown_affiliate', $sourceSystem, $externalId);
            }
        }

        $dates = $this->dates($data->checkInDate, $data->checkOutDate);

        if (! $dates) {
            return $this->failedValidation('Check-out must be after check-in.', $sourceSystem, $externalId);
        }

        [$checkIn, $checkOut, $stayNights] = $dates;
        $status = $this->statuses->map($data->bookingStatus);
        $sourceStatus = $this->statuses->normalize($data->bookingStatus);
        $roomRevenue = $this->decimal($data->roomRevenueAmount);
        $currency = $this->currency($data->currency);

        if ($status === AffiliateBookingStatus::Unknown) {
            $warnings[] = 'Unknown source status was stored for internal review.';
        }

        if ($roomRevenue === null) {
            $warnings[] = 'Verified room revenue is unavailable from the booking source.';
        }

        if ($data->roomRevenueAmount !== null && $roomRevenue === null) {
            return $this->failedValidation('Room revenue must be a non-negative decimal amount.', $sourceSystem, $externalId);
        }

        if ($roomValidationWarning = $this->validateRoomItems($data->roomItems)) {
            return $this->failedValidation($roomValidationWarning, $sourceSystem, $externalId);
        }

        $rooms = $this->normalizeRooms($data->roomItems, $stayNights, $currency, $warnings);

        if (collect($rooms)->pluck('external_room_id')->duplicates()->isNotEmpty()) {
            return $this->failedValidation(
                'Room identifiers must be unique within a booking.',
                $sourceSystem,
                $externalId,
            );
        }
        $commissionRate = $existing?->commission_rate_snapshot
            ?? AffiliateProgramSetting::current()->affiliate_commission_percentage;
        $effectiveStatus = $existing?->manual_booking_status ?? $status;
        $estimate = $this->commissions->estimate($effectiveStatus, $roomRevenue, $currency, (string) $commissionRate);
        $attributionWarning = $affiliate->isApproved()
            ? null
            : 'Affiliate was not approved when this source update was synchronized.';

        $attributes = [
            'affiliate_id' => $existing?->affiliate_id ?? $affiliate->id,
            'synced_webhotelier_booking_id' => $data->syncedWebhotelierBookingId,
            'source_system' => $sourceSystem,
            'external_booking_id' => $externalId,
            'external_booking_reference' => $this->nullableString($data->externalBookingReference),
            'affiliate_code_snapshot' => $existing?->affiliate_code_snapshot ?? $affiliate->affiliate_code,
            'check_in_date' => $checkIn->toDateString(),
            'check_out_date' => $checkOut->toDateString(),
            'stay_nights' => $stayNights,
            'room_revenue_amount' => $roomRevenue,
            'currency' => $currency,
            'booking_status' => $status,
            'source_status' => $sourceStatus,
            'commission_rate_snapshot' => (string) $commissionRate,
            'estimated_commission_amount' => $estimate->amount,
            'commission_state' => $estimate->state,
            'attribution_warning' => $attributionWarning,
            'calculation_unavailable_reason' => $estimate->unavailableReason,
            'synchronization_warning' => $warnings === [] ? null : implode(' ', array_unique($warnings)),
            'source_created_at' => $data->sourceCreatedAt,
            'source_updated_at' => $data->sourceUpdatedAt,
            'last_synced_at' => now(),
        ];
        $attributes['data_fingerprint'] = $this->fingerprint($attributes, $rooms);

        if ($existing && hash_equals($existing->data_fingerprint, $attributes['data_fingerprint'])) {
            $existing->forceFill([
                'synced_webhotelier_booking_id' => $data->syncedWebhotelierBookingId ?: $existing->synced_webhotelier_booking_id,
                'source_updated_at' => $data->sourceUpdatedAt ?: $existing->source_updated_at,
                'last_synced_at' => now(),
            ])->saveQuietly();

            return new AffiliateBookingSyncResult('unchanged', $existing->fresh('rooms'), $warnings);
        }

        return DB::transaction(function () use ($existing, $attributes, $rooms, $affiliate, $warnings): AffiliateBookingSyncResult {
            $trackedFields = [
                'booking_status',
                'check_in_date',
                'check_out_date',
                'room_revenue_amount',
                'estimated_commission_amount',
                'commission_state',
            ];
            $before = $existing
                ? collect($trackedFields)->mapWithKeys(
                    fn (string $field): array => [$field => $existing->getRawOriginal($field)]
                )->all()
                : [];
            $beforeRoomTypes = $existing
                ? $existing->rooms()->pluck('room_type_name')->sort()->values()->all()
                : [];

            $booking = $existing ?: new AffiliateBooking;
            $booking->fill($attributes)->save();
            $this->syncRooms($booking, $rooms);
            $this->commissionItems->synchronize($booking->fresh());
            $afterRoomTypes = $booking->rooms()->pluck('room_type_name')->sort()->values()->all();

            if (! $existing) {
                $safeMetadata = $this->auditMetadata($booking);
                $this->audit($affiliate->id, 'affiliate_booking.created', $safeMetadata);
                $this->audit($affiliate->id, 'affiliate_booking.attribution_matched', $safeMetadata);

                return new AffiliateBookingSyncResult('created', $booking->fresh('rooms'), $warnings);
            }

            $changedFields = collect($before)
                ->filter(fn ($value, string $field): bool => (string) $value !== (string) $booking->getRawOriginal($field))
                ->keys()
                ->all();

            $this->audit($booking->affiliate_id, 'affiliate_booking.updated', [
                ...$this->auditMetadata($booking),
                'changed_fields' => $changedFields,
            ]);

            if (in_array('booking_status', $changedFields, true)) {
                $this->audit($booking->affiliate_id, 'affiliate_booking.status_changed', $this->changeMetadata($booking, 'booking_status', $before['booking_status']));
            }

            if (array_intersect(['check_in_date', 'check_out_date'], $changedFields)) {
                $this->audit($booking->affiliate_id, 'affiliate_booking.dates_changed', [
                    ...$this->auditMetadata($booking),
                    'from' => ['check_in' => (string) $before['check_in_date'], 'check_out' => (string) $before['check_out_date']],
                    'to' => ['check_in' => $booking->check_in_date->toDateString(), 'check_out' => $booking->check_out_date->toDateString()],
                ]);
            }

            if ($beforeRoomTypes !== $afterRoomTypes) {
                $this->audit($booking->affiliate_id, 'affiliate_booking.room_type_changed', [
                    ...$this->auditMetadata($booking),
                    'from' => $beforeRoomTypes,
                    'to' => $afterRoomTypes,
                ]);
            }

            if (in_array('room_revenue_amount', $changedFields, true)) {
                $this->audit($booking->affiliate_id, 'affiliate_booking.room_revenue_changed', $this->changeMetadata($booking, 'room_revenue_amount', $before['room_revenue_amount']));
            }

            if (in_array('estimated_commission_amount', $changedFields, true)) {
                $this->audit($booking->affiliate_id, 'affiliate_booking.commission_estimate_changed', $this->changeMetadata($booking, 'estimated_commission_amount', $before['estimated_commission_amount']));
            }

            if ($booking->commission_state->value === 'ineligible' && (string) $before['commission_state'] !== 'ineligible') {
                $this->audit($booking->affiliate_id, 'affiliate_booking.became_ineligible', $this->auditMetadata($booking));
            }

            return new AffiliateBookingSyncResult('updated', $booking->fresh('rooms'), $warnings);
        });
    }

    private function isStale(AffiliateBooking $booking, AffiliateBookingData $data): bool
    {
        return $booking->source_updated_at
            && $data->sourceUpdatedAt
            && $data->sourceUpdatedAt->lessThan($booking->source_updated_at);
    }

    /** @return array{CarbonImmutable, CarbonImmutable, int}|null */
    private function dates(?string $checkIn, ?string $checkOut): ?array
    {
        if (blank($checkIn) || blank($checkOut)) {
            return null;
        }

        try {
            $start = CarbonImmutable::parse((string) $checkIn)->startOfDay();
            $end = CarbonImmutable::parse((string) $checkOut)->startOfDay();

            if ($end->lessThanOrEqualTo($start) || $start->diffInDays($end) > 65535) {
                return null;
            }

            return [$start, $end, (int) $start->diffInDays($end)];
        } catch (Throwable) {
            return null;
        }
    }

    /** @param array<int, array<string, mixed>> $items
     * @param  array<int, string>  $warnings
     * @return array<int, array<string, mixed>>
     */
    private function normalizeRooms(array $items, int $stayNights, ?string $bookingCurrency, array &$warnings): array
    {
        if ($items === []) {
            $items = [['room_type_name' => 'Room details unavailable', 'room_quantity' => 1]];
        }

        return collect($items)->values()->map(function (mixed $item, int $index) use ($stayNights, $bookingCurrency, &$warnings): array {
            $item = is_array($item) ? $item : [];
            $type = $this->nullableString($item['room_type_name'] ?? null) ?: 'Room details unavailable';
            $quantity = max(1, (int) ($item['room_quantity'] ?? 1));
            $revenue = $this->decimal($item['room_revenue_amount'] ?? null);
            $currency = $this->currency($item['currency'] ?? $bookingCurrency);
            $sourceNights = isset($item['stay_nights']) && is_numeric($item['stay_nights']) ? (int) $item['stay_nights'] : null;

            if ($sourceNights !== null && $sourceNights !== $stayNights) {
                $warnings[] = 'Source room nights differed from the date calculation; date-derived nights were used.';
            }

            $identity = [
                'position' => $index,
                'type' => $type,
                'quantity' => $quantity,
                'revenue' => $revenue,
                'currency' => $currency,
            ];
            $externalRoomId = $this->nullableString($item['external_room_id'] ?? null)
                ?: 'line-'.hash('sha256', json_encode($identity, JSON_THROW_ON_ERROR));
            $lineFingerprint = hash('sha256', json_encode([
                ...$identity,
                'external_room_id' => $externalRoomId,
                'stay_nights' => $stayNights,
            ], JSON_THROW_ON_ERROR));

            return [
                'external_room_id' => mb_substr($externalRoomId, 0, 120),
                'room_type_name' => mb_substr($type, 0, 191),
                'room_quantity' => $quantity,
                'stay_nights' => $stayNights,
                'room_revenue_amount' => $revenue,
                'currency' => $currency,
                'line_fingerprint' => $lineFingerprint,
            ];
        })->all();
    }

    /** @param array<int, array<string, mixed>> $rooms */
    private function syncRooms(AffiliateBooking $booking, array $rooms): void
    {
        $identifiers = collect($rooms)->pluck('external_room_id')->all();
        $booking->rooms()->whereNotIn('external_room_id', $identifiers)->delete();

        foreach ($rooms as $room) {
            $booking->rooms()->updateOrCreate(
                ['external_room_id' => $room['external_room_id']],
                $room,
            );
        }
    }

    /** @param array<string, mixed> $attributes
     * @param  array<int, array<string, mixed>>  $rooms
     */
    private function fingerprint(array $attributes, array $rooms): string
    {
        $safe = collect($attributes)->except([
            'external_booking_reference',
            'source_created_at',
            'source_updated_at',
            'last_synced_at',
            'data_fingerprint',
        ])->all();
        $safe['rooms'] = collect($rooms)->sortBy('external_room_id')->values()->all();

        return hash('sha256', json_encode($safe, JSON_THROW_ON_ERROR));
    }

    private function decimal(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $normalized = str_replace(',', '', trim((string) $value));

        return preg_match('/^\d+(?:\.\d+)?$/', $normalized)
            && bccomp($normalized, '0', 6) >= 0
            && bccomp($normalized, '9999999999999.99', 2) <= 0
            ? bcadd($normalized, '0', 2)
            : null;
    }

    /** @param array<int, array<string, mixed>> $items */
    private function validateRoomItems(array $items): ?string
    {
        foreach ($items as $item) {
            if (! is_array($item)) {
                return 'Every room item must be a valid room record.';
            }

            if (array_key_exists('room_quantity', $item)) {
                $quantity = trim((string) $item['room_quantity']);

                if (! preg_match('/^\d+$/', $quantity) || (int) $quantity < 1 || (int) $quantity > 65535) {
                    return 'Room quantity must be a positive supported integer.';
                }
            }

            if (array_key_exists('room_revenue_amount', $item)
                && $item['room_revenue_amount'] !== null
                && $item['room_revenue_amount'] !== ''
                && $this->decimal($item['room_revenue_amount']) === null) {
                return 'Room-line revenue must be a non-negative decimal amount.';
            }
        }

        return null;
    }

    private function currency(mixed $value): ?string
    {
        $currency = mb_strtoupper(trim((string) $value));

        return preg_match('/^[A-Z]{3,10}$/', $currency) ? $currency : null;
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : mb_substr($value, 0, 191);
    }

    private function audit(int $affiliateId, string $event, array $metadata): void
    {
        AffiliateAuditEvent::query()->create([
            'affiliate_id' => $affiliateId,
            'actor_user_id' => null,
            'event' => $event,
            'metadata' => $metadata,
        ]);
    }

    /** @return array<string, mixed> */
    private function auditMetadata(AffiliateBooking $booking): array
    {
        return [
            'affiliate_booking_id' => $booking->id,
            'source_system' => $booking->source_system,
            'external_booking_id' => $booking->external_booking_id,
            'booking_status' => $booking->booking_status->value,
            'commission_state' => $booking->commission_state->value,
        ];
    }

    /** @return array<string, mixed> */
    private function changeMetadata(AffiliateBooking $booking, string $field, mixed $from): array
    {
        return [
            ...$this->auditMetadata($booking),
            'field' => $field,
            'from' => $from,
            'to' => $booking->getRawOriginal($field),
        ];
    }

    private function skipped(string $state, string $sourceSystem, string $externalId): AffiliateBookingSyncResult
    {
        Log::info('Affiliate booking synchronization skipped.', [
            'source_system' => $sourceSystem,
            'external_booking_id' => $externalId,
            'result' => $state,
        ]);

        return new AffiliateBookingSyncResult($state);
    }

    private function failedValidation(string $warning, ?string $sourceSystem = null, ?string $externalId = null): AffiliateBookingSyncResult
    {
        Log::warning('Affiliate booking synchronization validation failed.', array_filter([
            'source_system' => $sourceSystem,
            'external_booking_id' => $externalId,
            'result' => 'failed_validation',
            'reason' => $warning,
        ]));

        return new AffiliateBookingSyncResult('failed_validation', warnings: [$warning]);
    }
}

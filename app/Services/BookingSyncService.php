<?php

namespace App\Services;

use App\Enums\AffiliateCommissionState;
use App\Models\AffiliateBooking;
use App\Models\BookingSyncLog;
use App\Models\Member;
use App\Models\SyncedWebhotelierBooking;
use App\Services\Affiliate\Booking\SyncAffiliateBookingService;
use App\Services\Affiliate\Booking\SyncedWebhotelierAffiliateBookingSource;
use App\Services\Affiliate\AffiliateNotificationService;
use App\Services\Affiliate\Operations\AffiliateOperationalStateService;
use App\Support\AutoJoinBookingCutoff;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class BookingSyncService
{
    private const AUTOMATIC_SYNC_LOOKBACK_HOURS = 48;

    public function __construct(
        private readonly MembershipBookingApiService $api,
        private readonly SyncedWebhotelierAffiliateBookingSource $affiliateBookingSource,
        private readonly SyncAffiliateBookingService $affiliateBookingSync,
        private readonly AffiliateNotificationService $affiliateNotifications,
    ) {}

    public function sync(?string $sinceOverride = null): array
    {
        $log = BookingSyncLog::create([
            'started_at' => now(),
            'status' => BookingSyncLog::STATUS_RUNNING,
            'message' => 'Booking sync started.',
        ]);
        $state = app(AffiliateOperationalStateService::class);
        $state->attempted('booking_sync', 'Booking synchronization started.');

        $summary = [
            'success' => false,
            'message' => 'Booking sync failed.',
            'bookings_received' => 0,
            'bookings_created' => 0,
            'bookings_updated' => 0,
            'members_created' => 0,
            'members_updated' => 0,
            'affiliate_bookings' => [],
            'affiliate_booking_warnings' => [],
            'since_used' => null,
            'membership_api_url_called' => null,
            'membership_api_success' => false,
            'membership_api_bookings_count' => 0,
            'membership_api_message' => null,
            'welcome_email_messages' => [],
        ];

        try {
            $since = filled($sinceOverride) ? trim((string) $sinceOverride) : $this->lastSuccessfulSyncTime();
            $summary['since_used'] = $since;

            $bookings = $this->api->fetchBookings($since);
            $voucherFieldDetected = collect($bookings)->contains(fn ($payload): bool => is_array($payload) && array_key_exists('voucher_code', $payload));
            $summary = array_merge($summary, $this->api->debugData());
            $summary['bookings_received'] = count($bookings);

            DB::transaction(function () use ($bookings, &$summary): void {
                foreach ($bookings as $payload) {
                    if (! is_array($payload)) {
                        continue;
                    }

                    $this->syncBooking($payload, $summary);
                }

                $this->recalculatePendingAffiliateBookings($summary);
            });

            $summary['success'] = true;
            $summary['message'] = 'Booking sync completed.';
            $logMessage = $this->logMessageWithWelcomeEmailDetails($summary);

            $log->update([
                'finished_at' => now(),
                'status' => BookingSyncLog::STATUS_SUCCESS,
                ...array_intersect_key($summary, array_flip([
                    'bookings_received',
                    'bookings_created',
                    'bookings_updated',
                    'members_created',
                    'members_updated',
                ])),
                'message' => $logMessage,
            ]);
            $state->succeeded('booking_sync', 'Booking synchronization completed; '.number_format($summary['bookings_received']).' booking(s) received.', [
                'bookings_received' => $summary['bookings_received'],
                'voucher_field_detected' => $voucherFieldDetected,
            ]);

            return $summary;
        } catch (Throwable $e) {
            report($e);

            $summary['message'] = $this->publicErrorMessage($e);
            $summary = array_merge($summary, $this->api->debugData());
            $logMessage = $this->logMessageWithWelcomeEmailDetails($summary);

            $log->update([
                'finished_at' => now(),
                'status' => BookingSyncLog::STATUS_FAILED,
                'bookings_received' => $summary['bookings_received'],
                'bookings_created' => $summary['bookings_created'],
                'bookings_updated' => $summary['bookings_updated'],
                'members_created' => $summary['members_created'],
                'members_updated' => $summary['members_updated'],
                'message' => $logMessage,
            ]);
            $state->failed('booking_sync', 'Booking synchronization failed with '.$e::class.'.', ['error_class' => $e::class]);

            return $summary;
        }
    }

    private function syncBooking(array $payload, array &$summary): void
    {
        $bookingNumber = trim((string) ($payload['booking_number'] ?? ''));
        $email = $this->normalizeEmail($payload['email'] ?? null);

        if ($bookingNumber === '' || $email === '') {
            return;
        }

        $booking = SyncedWebhotelierBooking::firstOrNew([
            'booking_number' => $bookingNumber,
        ]);

        $member = null;
        $memberWasUpdated = false;

        if ($booking->exists && $booking->member_assigned_manually && $booking->member_id) {
            $member = $booking->member;
            $summary['welcome_email_messages'][] = 'Welcome email skipped because booking is manually assigned to a member.';
        } else {
            $memberResult = $this->firstOrCreateMember(
                $email,
                $payload,
                AutoJoinBookingCutoff::wasCreatedAfterCutoff($payload)
            );
            $member = $memberResult['member'];

            if ($memberResult['created']) {
                $summary['members_created']++;
                $summary['welcome_email_messages'][] = $this->sendWelcomeEmail($member, $bookingNumber, $payload);
            } elseif ($memberResult['updated']) {
                $summary['members_updated']++;
                $memberWasUpdated = true;
                $summary['welcome_email_messages'][] = 'Welcome email skipped because member already exists.';
            } elseif ($memberResult['skipped'] ?? false) {
                $summary['welcome_email_messages'][] = $memberResult['reason'];
            } else {
                $summary['welcome_email_messages'][] = 'Welcome email skipped because member already exists.';
            }
        }

        $attributes = [
            'member_id' => $member?->getKey(),
            'guest_name' => $this->nullableString($payload['guest_name'] ?? null),
            'email' => $email,
            'phone' => $this->nullableString($payload['phone'] ?? null),
            'check_in' => $this->date($payload['check_in'] ?? null),
            'check_out' => $this->date($payload['check_out'] ?? null),
            'rooms' => $this->integer($payload['rooms'] ?? null),
            'room_type' => $this->nullableString($payload['room_type'] ?? null),
            'room_name' => $this->nullableString($payload['room_name'] ?? null),
            'rate_name' => $this->nullableString($payload['rate_name'] ?? null),
            'currency' => $this->nullableString($payload['currency'] ?? null),
            'booking_total' => $this->decimal($payload['booking_total'] ?? null),
            'status' => $this->nullableString($payload['status'] ?? null),
            'source_name' => $this->nullableString($payload['source_name'] ?? null),
            'affiliate_code' => $this->nullableString($payload['voucher_code'] ?? null),
            'remote_updated_at' => $this->dateTime($payload['remote_updated_at'] ?? null),
            'last_synced_at' => now(),
        ];

        $exists = $booking->exists;
        $booking->fill($attributes);
        $booking->save();

        $exists ? $summary['bookings_updated']++ : $summary['bookings_created']++;

        $affiliateResult = $this->affiliateBookingSync->sync(
            $this->affiliateBookingSource->normalize($booking->fresh())
        );
        $summary['affiliate_bookings'][$affiliateResult->state] =
            ($summary['affiliate_bookings'][$affiliateResult->state] ?? 0) + 1;

        if ($affiliateResult->state === 'created' && $affiliateResult->booking) {
            $this->affiliateNotifications->afterCommitNewBooking($affiliateResult->booking);
        }

        foreach ($affiliateResult->warnings as $warning) {
            $warningKey = $this->affiliateBookingWarningKey($warning);
            $summary['affiliate_booking_warnings'][$warningKey] =
                ($summary['affiliate_booking_warnings'][$warningKey] ?? 0) + 1;
        }

        if ($member && app(MemberStayDateBackfillService::class)->fillMissingDatesForMember($member) && ! $memberWasUpdated) {
            $summary['members_updated']++;
        }
    }

    private function recalculatePendingAffiliateBookings(array &$summary): void
    {
        AffiliateBooking::query()
            ->with('syncedWebhotelierBooking')
            ->where('commission_state', AffiliateCommissionState::CalculationUnavailable->value)
            ->whereHas('syncedWebhotelierBooking', fn ($query) => $query->whereNotNull('booking_total'))
            ->each(function (AffiliateBooking $affiliateBooking) use (&$summary): void {
                $sourceBooking = $affiliateBooking->syncedWebhotelierBooking;

                if (! $sourceBooking) {
                    return;
                }

                $result = $this->affiliateBookingSync->sync(
                    $this->affiliateBookingSource->normalize($sourceBooking)
                );

                $summary['affiliate_bookings'][$result->state] =
                    ($summary['affiliate_bookings'][$result->state] ?? 0) + 1;

                foreach ($result->warnings as $warning) {
                    $warningKey = $this->affiliateBookingWarningKey($warning);
                    $summary['affiliate_booking_warnings'][$warningKey] =
                        ($summary['affiliate_booking_warnings'][$warningKey] ?? 0) + 1;
                }
            });
    }

    private function firstOrCreateMember(string $email, array $payload, bool $canCreateMember): array
    {
        $member = Member::whereRaw('LOWER(email) = ?', [$email])->first();
        $created = false;
        $updated = false;

        if (! $member) {
            if (! $canCreateMember) {
                return [
                    'member' => null,
                    'created' => false,
                    'updated' => false,
                    'skipped' => true,
                    'reason' => 'Welcome email skipped because booking was not created after 1 July 2026.',
                ];
            }

            $guestName = $this->nullableString($payload['guest_name'] ?? null) ?: $email;
            [$firstName, $lastName] = $this->splitName($guestName);
            $temporaryPassword = trim((string) ($payload['booking_number'] ?? ''));

            $member = Member::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'name' => $guestName,
                'email' => $email,
                'phone_number' => $this->nullableString($payload['phone'] ?? null),
                'booking_check_in' => $this->date($payload['check_in'] ?? null),
                'booking_check_out' => $this->date($payload['check_out'] ?? null),
                'password' => $temporaryPassword,
                'must_change_password' => true,
                'member_source' => Member::SOURCE_AUTO_JOIN,
                'tier' => Member::TIER_BRONZE,
                'points' => 0,
                'membership_started_at' => now(),
                'membership_expires_at' => now()->addYear(),
                'email_verified_at' => now(),
            ]);

            return ['member' => $member, 'created' => true, 'updated' => false];
        }

        $updates = [];

        if (! $member->phone_number && filled($payload['phone'] ?? null)) {
            $updates['phone_number'] = $this->nullableString($payload['phone']);
        }

        if (! $member->name && filled($payload['guest_name'] ?? null)) {
            $updates['name'] = $this->nullableString($payload['guest_name']);
        }

        if (! $member->booking_check_in && filled($payload['check_in'] ?? null)) {
            $updates['booking_check_in'] = $this->date($payload['check_in']);
        }

        if (! $member->booking_check_out && filled($payload['check_out'] ?? null)) {
            $updates['booking_check_out'] = $this->date($payload['check_out']);
        }

        if ($updates !== []) {
            $member->forceFill($updates)->save();
            $updated = true;
        }

        return compact('member', 'created', 'updated');
    }

    private function lastSuccessfulSyncTime(): ?string
    {
        $last = BookingSyncLog::query()
            ->where('status', BookingSyncLog::STATUS_SUCCESS)
            ->whereNotNull('finished_at')
            ->latest('finished_at')
            ->value('finished_at');

        return $last
            ? Carbon::parse($last)->subHours(self::AUTOMATIC_SYNC_LOOKBACK_HOURS)->toDateTimeString()
            : null;
    }

    private function sendWelcomeEmail(Member $member, string $bookingNumber, array $payload): string
    {
        if ($member->member_source !== Member::SOURCE_AUTO_JOIN) {
            return 'Welcome email skipped because member is not auto join.';
        }

        if ($member->welcome_email_sent_at) {
            return 'Welcome email skipped because it was already sent.';
        }

        if (blank($member->email)) {
            return 'Welcome email skipped because member email is empty.';
        }

        try {
            // Booking sync uses the same welcome template, but delivery is delegated to the relay API.
            $result = app(MembershipEmailRelayService::class)->sendView('emails.membership.auto-join-welcome', [
                'member' => $member,
                'bookingNumber' => $bookingNumber,
                'roomName' => $this->nullableString($payload['room_name'] ?? null),
                'checkinDate' => $this->nullableString($payload['check_in'] ?? null),
                'checkoutDate' => $this->nullableString($payload['check_out'] ?? null),
                'loginUrl' => route('membership.login'),
                'passwordResetUrl' => route('membership.password.request'),
            ], [
                'to' => $member->email,
                'bcc' => $this->guestBcc(),
                'subject' => 'Welcome to Nandini Inner Circle',
            ]);

            if (! $result['success']) {
                Log::warning('Booking sync auto-join welcome email relay failed.', [
                    'member_id' => $member->id,
                    'result' => 'delivery_failed',
                ]);

                return 'Welcome email failed.';
            }

            $member->forceFill([
                'welcome_email_sent_at' => now(),
            ])->save();

            return 'Welcome email sent to member.';
        } catch (Throwable $e) {
            Log::warning('Booking sync auto-join welcome email failed.', [
                'member_id' => $member->id,
                'exception' => $e::class,
            ]);

            return 'Welcome email failed.';
        }
    }

    private function logMessageWithWelcomeEmailDetails(array $summary): string
    {
        $messages = collect($summary['welcome_email_messages'] ?? [])
            ->filter()
            ->unique()
            ->values()
            ->all();

        if ($messages === []) {
            return trim((string) $summary['message'].' '.$this->affiliateBookingSummary($summary));
        }

        return trim((string) $summary['message'].' '.implode(' ', $messages).' '.$this->affiliateBookingSummary($summary));
    }

    private function affiliateBookingSummary(array $summary): string
    {
        $results = collect($summary['affiliate_bookings'] ?? [])
            ->map(fn (int $count, string $state): string => $state.': '.$count)
            ->implode(', ');

        $warnings = collect($summary['affiliate_booking_warnings'] ?? [])
            ->map(fn (int $count, string $warning): string => $warning.': '.$count)
            ->implode(', ');

        return trim(
            ($results === '' ? '' : 'Affiliate booking results: '.$results.'.').' '.
            ($warnings === '' ? '' : 'Affiliate booking warnings: '.$warnings.'.')
        );
    }

    private function affiliateBookingWarningKey(string $warning): string
    {
        return match (true) {
            str_contains($warning, 'room revenue') => 'missing_room_revenue',
            str_contains($warning, 'Unknown source status') => 'unknown_source_status',
            str_contains($warning, 'date') || str_contains($warning, 'nights') => 'date_or_nights_warning',
            str_contains($warning, 'Affiliate') => 'attribution_warning',
            default => 'validation_warning',
        };
    }

    private function normalizeEmail(mixed $email): string
    {
        $email = trim(strtolower((string) $email));
        $email = preg_replace('/^mailto:/', '', $email) ?? $email;
        $email = trim($email, " <>[]()\t\n\r\0\x0B");

        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '';
    }

    private function splitName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name), 2) ?: [];

        return [$parts[0] ?? null, $parts[1] ?? null];
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function date(mixed $value): ?string
    {
        try {
            return filled($value) ? Carbon::parse($value)->toDateString() : null;
        } catch (Throwable) {
            return null;
        }
    }

    private function dateTime(mixed $value): ?Carbon
    {
        try {
            return filled($value) ? Carbon::parse($value) : null;
        } catch (Throwable) {
            return null;
        }
    }

    private function integer(mixed $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    private function decimal(mixed $value): ?string
    {
        return is_numeric($value) ? number_format((float) $value, 2, '.', '') : null;
    }

    private function publicErrorMessage(Throwable $e): string
    {
        return $e->getMessage() ?: 'Booking sync failed.';
    }

    /**
     * @return array<int, string>
     */
    private function guestBcc(): array
    {
        $bcc = trim((string) config('mail.guest_bcc'));

        return $bcc === '' ? [] : [$bcc];
    }
}

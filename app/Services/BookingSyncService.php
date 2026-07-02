<?php

namespace App\Services;

use App\Models\BookingSyncLog;
use App\Models\Member;
use App\Models\SyncedWebhotelierBooking;
use App\Support\AutoJoinBookingCutoff;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class BookingSyncService
{
    public function __construct(
        private readonly MembershipBookingApiService $api,
    ) {}

    public function sync(?string $sinceOverride = null): array
    {
        $log = BookingSyncLog::create([
            'started_at' => now(),
            'status' => BookingSyncLog::STATUS_RUNNING,
            'message' => 'Booking sync started.',
        ]);

        $summary = [
            'success' => false,
            'message' => 'Booking sync failed.',
            'bookings_received' => 0,
            'bookings_created' => 0,
            'bookings_updated' => 0,
            'members_created' => 0,
            'members_updated' => 0,
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
            $summary = array_merge($summary, $this->api->debugData());
            $summary['bookings_received'] = count($bookings);

            DB::transaction(function () use ($bookings, &$summary): void {
                foreach ($bookings as $payload) {
                    if (! is_array($payload)) {
                        continue;
                    }

                    $this->syncBooking($payload, $summary);
                }
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
            'remote_updated_at' => $this->dateTime($payload['remote_updated_at'] ?? null),
            'last_synced_at' => now(),
        ];

        $exists = $booking->exists;
        $booking->fill($attributes);
        $booking->save();

        $exists ? $summary['bookings_updated']++ : $summary['bookings_created']++;
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

        return $last ? Carbon::parse($last)->toDateTimeString() : null;
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
                    'email' => $member->email,
                    'relay_response' => $result,
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
                'email' => $member->email,
                'message' => $e->getMessage(),
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
            return (string) $summary['message'];
        }

        return trim((string) $summary['message'] . ' ' . implode(' ', $messages));
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

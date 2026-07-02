<?php

namespace App\Services;

use App\Models\Member;
use App\Support\AutoJoinBookingCutoff;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MemberAutoJoinService
{
    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function autoJoinFromWebhotelierReservation(int|string $reservationId, array $data): array
    {
        $clientInfo = Arr::get($data, 'clientInfo', []);

        if (! is_array($clientInfo)) {
            $clientInfo = [];
        }

        $email = strtolower(trim((string) (
            Arr::get($clientInfo, 'email')
            ?: Arr::get($data, 'customer.email')
            ?: Arr::get($data, 'guest.email')
            ?: Arr::get($data, 'booker.email')
            ?: Arr::get($data, 'email')
        )));

        if ($email === '') {
            return [
                'created' => false,
                'skipped' => true,
                'reason' => 'Guest email is missing.',
            ];
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'created' => false,
                'skipped' => true,
                'reason' => 'Guest email is invalid.',
                'email' => $email,
            ];
        }

        $statusCode = strtolower(trim((string) (
            Arr::get($data, 'statusCode')
            ?: Arr::get($data, 'status_code')
            ?: Arr::get($data, 'booking_status')
            ?: Arr::get($data, 'bookInfo.status')
        )));

        if (in_array($statusCode, [
            'cancel',
            'canceled',
            'cancelled',
            'cancelled_by_guest',
            'canceled_by_guest',
        ], true)) {
            return [
                'created' => false,
                'skipped' => true,
                'reason' => 'Reservation is cancelled.',
                'email' => $email,
            ];
        }

        if (! AutoJoinBookingCutoff::wasCreatedAfterCutoff($data)) {
            return [
                'created' => false,
                'skipped' => true,
                'reason' => 'Booking was not created after 1 July 2026.',
                'email' => $email,
            ];
        }

        $existingMember = Member::query()
            ->where('email', $email)
            ->first();

        if ($existingMember) {
            return [
                'created' => false,
                'skipped' => true,
                'reason' => 'Member already exists.',
                'member_id' => $existingMember->id,
                'email' => $email,
            ];
        }

        $firstName = trim((string) (
            Arr::get($clientInfo, 'firstName')
            ?: Arr::get($clientInfo, 'first_name')
            ?: Arr::get($data, 'customer.firstName')
            ?: Arr::get($data, 'customer.firstname')
            ?: Arr::get($data, 'guest.firstName')
            ?: Arr::get($data, 'guest.firstname')
            ?: Arr::get($data, 'booker.firstName')
            ?: Arr::get($data, 'firstName')
        ));

        $lastName = trim((string) (
            Arr::get($clientInfo, 'lastName')
            ?: Arr::get($clientInfo, 'last_name')
            ?: Arr::get($data, 'customer.lastName')
            ?: Arr::get($data, 'customer.lastname')
            ?: Arr::get($data, 'guest.lastName')
            ?: Arr::get($data, 'guest.lastname')
            ?: Arr::get($data, 'booker.lastName')
            ?: Arr::get($data, 'lastName')
        ));

        $fullName = trim($firstName . ' ' . $lastName);

        if ($fullName === '') {
            $fullName = $email;
        }

        $temporaryPassword = $this->extractBookingNumber($reservationId, $data) ?: $this->makeTemporaryPassword();
        $temporaryPasswordSource = $this->extractBookingNumber($reservationId, $data) ? 'booking_number' : 'random_fallback';

        $member = Member::create([
            'first_name' => $firstName !== '' ? $firstName : null,
            'last_name' => $lastName !== '' ? $lastName : null,
            'name' => $fullName,
            'email' => $email,
            'phone_number' => $this->extractPhone($clientInfo, $data),
            'country' => $this->extractCountry($clientInfo, $data),
            'address' => $this->extractAddress($clientInfo, $data),

            'password' => $temporaryPassword,
            'must_change_password' => true,

            'member_source' => Member::SOURCE_AUTO_JOIN,

            'tier' => Member::TIER_BRONZE,
            'points' => 0,
            'membership_started_at' => now(),
            'membership_expires_at' => now()->addYear(),

            'marketing_consent' => false,
            'email_verified_at' => now(),
        ]);

        $emailNotification = $this->sendWelcomeEmail($member, $reservationId, $data);

        return [
            'created' => true,
            'skipped' => false,
            'reason' => 'Member auto joined from WebHotelier booking.',
            'member_id' => $member->id,
            'email' => $email,
            'member_source' => Member::SOURCE_AUTO_JOIN,
            'must_change_password' => true,
            'temporary_password_source' => $temporaryPasswordSource,
            'welcome_email' => $emailNotification,
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function extractBookingNumber(int|string $reservationId, array $data): ?string
    {
        $bookingNumber = trim((string) (
            Arr::get($data, 'bookingNumber')
            ?: Arr::get($data, 'booking_number')
            ?: Arr::get($data, 'confirmationCode')
            ?: Arr::get($data, 'confirmation_code')
            ?: Arr::get($data, 'id')
            ?: $reservationId
        ));

        return $bookingNumber !== '' ? $bookingNumber : null;
    }

    protected function makeTemporaryPassword(): string
    {
        return Str::upper(Str::random(3)) . random_int(1000, 9999) . Str::lower(Str::random(2));
    }

    /**
     * @param array<string, mixed> $clientInfo
     * @param array<string, mixed> $data
     */
    protected function extractPhone(array $clientInfo, array $data): ?string
    {
        $phone = Arr::get($clientInfo, 'phone')
            ?: Arr::get($clientInfo, 'telephone')
            ?: Arr::get($clientInfo, 'tel')
            ?: Arr::get($data, 'customer.phone')
            ?: Arr::get($data, 'guest.phone')
            ?: Arr::get($data, 'booker.phone')
            ?: Arr::get($data, 'phone');

        return filled($phone) ? (string) $phone : null;
    }

    /**
     * @param array<string, mixed> $clientInfo
     * @param array<string, mixed> $data
     */
    protected function extractCountry(array $clientInfo, array $data): ?string
    {
        $country = Arr::get($clientInfo, 'country')
            ?: Arr::get($clientInfo, 'countryName')
            ?: Arr::get($clientInfo, 'country_name')
            ?: Arr::get($data, 'customer.country')
            ?: Arr::get($data, 'guest.country')
            ?: Arr::get($data, 'booker.country');

        return filled($country) ? (string) $country : null;
    }

    /**
     * @param array<string, mixed> $clientInfo
     * @param array<string, mixed> $data
     */
    protected function extractAddress(array $clientInfo, array $data): ?string
    {
        $address = Arr::get($clientInfo, 'address')
            ?: Arr::get($clientInfo, 'street')
            ?: Arr::get($data, 'customer.address')
            ?: Arr::get($data, 'guest.address')
            ?: Arr::get($data, 'booker.address');

        return filled($address) ? (string) $address : null;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function sendWelcomeEmail(
        Member $member,
        int|string $reservationId,
        array $data
    ): array {
        try {
            // Auto-join welcome email is rendered here, then delivered by the membership relay.
            $result = app(MembershipEmailRelayService::class)->sendView('emails.membership.auto-join-welcome', [
                'member' => $member,
                'bookingNumber' => $this->extractBookingNumber($reservationId, $data),
                'roomName' => Arr::get($data, 'roomStay.roomName'),
                'checkinDate' => Arr::get($data, 'roomStay.from'),
                'checkoutDate' => Arr::get($data, 'roomStay.to'),
                'loginUrl' => route('membership.login'),
                'passwordResetUrl' => route('membership.password.request'),
            ], [
                'to' => $member->email,
                'bcc' => $this->guestBcc(),
                'subject' => 'Welcome to Nandini Inner Circle',
            ]);

            if (! $result['success']) {
                Log::warning('Auto-joined member welcome email could not be sent through relay.', [
                    'member_id' => $member->id,
                    'reservation_id' => (string) $reservationId,
                    'relay_response' => $result,
                ]);

                return [
                    'sent' => false,
                    'error' => $result['error'],
                ];
            }

            $member->forceFill([
                'welcome_email_sent_at' => now(),
            ])->save();

            return [
                'sent' => true,
                'error' => null,
            ];
        } catch (\Throwable $exception) {
            Log::warning('Auto-joined member welcome email could not be sent.', [
                'member_id' => $member->id,
                'reservation_id' => (string) $reservationId,
                'error' => $exception->getMessage(),
            ]);

            return [
                'sent' => false,
                'error' => $exception->getMessage(),
            ];
        }
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

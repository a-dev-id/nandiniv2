<?php

namespace App\Services;

use App\Models\Member;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

class MemberCheckoutNotificationService
{
    private const RESERVATION_EMAIL = 'reservation@nandinibali.com';

    public function __construct(
        private readonly MembershipEmailRelayService $emailRelay,
    ) {}

    /**
     * @return array{date: string, sent: int, failed: int, skipped: int}
     */
    public function sendTodayNotifications(?string $date = null): array
    {
        $checkoutDate = Carbon::parse($date ?: now())->toDateString();
        $summary = [
            'date' => $checkoutDate,
            'sent' => 0,
            'failed' => 0,
            'skipped' => Member::query()
                ->whereDate('booking_check_out', $checkoutDate)
                ->whereDate('checkout_notification_sent_at', $checkoutDate)
                ->count(),
        ];

        Member::query()
            ->whereDate('booking_check_out', $checkoutDate)
            ->where(function ($query) use ($checkoutDate): void {
                $query
                    ->whereNull('checkout_notification_sent_at')
                    ->orWhereDate('checkout_notification_sent_at', '!=', $checkoutDate);
            })
            ->orderBy('id')
            ->chunkById(100, function ($members) use (&$summary, $checkoutDate): void {
                foreach ($members as $member) {
                    $result = $this->sendNotification($member, $checkoutDate);

                    if ($result['success']) {
                        $member->forceFill([
                            'checkout_notification_sent_at' => now(),
                        ])->save();

                        $summary['sent']++;

                        continue;
                    }

                    $summary['failed']++;

                    Log::warning('Member checkout notification email could not be sent through relay.', [
                        'member_id' => $member->id,
                        'email' => $member->email,
                        'checkout_date' => $checkoutDate,
                        'relay_response' => $result,
                    ]);
                }
            });

        return $summary;
    }

    /**
     * @return array{success: bool, status: int|null, response: mixed, error: string|null}
     */
    private function sendNotification(Member $member, string $checkoutDate): array
    {
        try {
            return $this->emailRelay->sendView('emails.membership.member-checkout-today', [
                'member' => $member,
                'checkoutDate' => Carbon::parse($checkoutDate),
            ], [
                'to' => self::RESERVATION_EMAIL,
                'bcc' => $this->guestBcc(),
                'subject' => 'Member Checkout Today: ' . ($member->full_name ?: $member->email),
            ]);
        } catch (Throwable $exception) {
            return [
                'success' => false,
                'status' => null,
                'response' => null,
                'error' => $exception->getMessage(),
            ];
        }
    }

    /**
     * @return array<int, string>
     */
    private function guestBcc(): array
    {
        return collect(explode(',', (string) config('mail.guest_bcc')))
            ->map(fn (string $email): string => trim($email))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}

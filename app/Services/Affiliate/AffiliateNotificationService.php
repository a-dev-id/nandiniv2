<?php

namespace App\Services\Affiliate;

use App\Enums\AffiliatePaymentMethod;
use App\Models\Affiliate;
use App\Models\AffiliateBooking;
use App\Models\AffiliatePayout;
use App\Models\AffiliateProgramSetting;
use App\Services\MembershipEmailRelayService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Throwable;

class AffiliateNotificationService
{
    public function __construct(
        private readonly AffiliateAuditService $audit,
        private readonly AffiliateLinkService $links,
        private readonly MembershipEmailRelayService $relay,
    ) {}

    public function afterCommitRegistration(Affiliate $affiliate): void
    {
        if (AffiliateProgramSetting::current()->registration_confirmation_enabled) {
            $this->afterCommit($affiliate, 'registration_notification_dispatched', [
                'subject' => 'Your Nandini Partner Circle registration',
                'eyebrow' => 'Nandini Partner Circle',
                'heading' => 'Registration Received',
                'paragraphs' => [
                    'Thank you for registering for the Nandini Partner Circle.',
                    'Your affiliate account is currently under review. The review process may take up to 48 hours, and we will notify you when a decision is available.',
                ],
                'actionLabel' => 'Open Affiliate Dashboard',
                'actionUrl' => route('affiliate.dashboard'),
            ], includeBcc: false);
        }

        DB::afterCommit(fn () => $this->sendRegistrationRequestToStaff($affiliate));
    }

    public function afterCommitInvitation(Affiliate $affiliate): void
    {
        if (! AffiliateProgramSetting::current()->internal_invitation_enabled) {
            return;
        }

        $token = Password::broker('affiliates')->createToken($affiliate);
        $approved = $affiliate->isApproved();
        $details = $approved ? [
            'Affiliate code' => $affiliate->affiliate_code,
            'Short link' => $this->links->shortLink($affiliate),
        ] : [];

        $this->afterCommit($affiliate, 'invitation_sent', [
            'subject' => 'Set up your Nandini Partner Circle account',
            'eyebrow' => 'Nandini Partner Circle',
            'heading' => 'Account Invitation',
            'paragraphs' => [
                'Nandini Jungle by Hanging Gardens has created a Partner Circle affiliate account for you.',
                $approved
                    ? 'Your account is approved and will be ready after you set your password.'
                    : 'Your account is pending review. We will notify you when a decision is available.',
            ],
            'details' => $details,
            'actionLabel' => 'Set Your Password',
            'actionUrl' => route('affiliate.password.setup', ['token' => $token, 'email' => $affiliate->email]),
            'footerNote' => 'This secure link expires in 60 minutes and can only be used to set the password for this account.',
        ]);
    }

    public function afterCommitApproval(Affiliate $affiliate): void
    {
        if (! AffiliateProgramSetting::current()->approval_notification_enabled) {
            return;
        }

        $this->afterCommit($affiliate, 'approval_notification_dispatched', [
            'subject' => 'Your Nandini Partner Circle account is approved',
            'eyebrow' => 'Nandini Partner Circle',
            'heading' => 'Account Approved',
            'paragraphs' => [
                'Your Nandini Partner Circle affiliate account has been approved.',
                'Bookings made using your affiliate code or link can now be attributed to your account.',
            ],
            'details' => [
                'Affiliate code' => $affiliate->affiliate_code,
                'Short link' => $this->links->shortLink($affiliate),
            ],
            'actionLabel' => 'Open Affiliate Dashboard',
            'actionUrl' => route('affiliate.dashboard'),
        ]);
    }

    public function afterCommitRejection(Affiliate $affiliate): void
    {
        if (! AffiliateProgramSetting::current()->rejection_notification_enabled) {
            return;
        }

        $paragraphs = ['Thank you for your interest in the Nandini Partner Circle. We are unable to approve your affiliate account at this time.'];

        if (filled($affiliate->rejection_reason)) {
            $paragraphs[] = 'Reason: '.$affiliate->rejection_reason;
        }

        $paragraphs[] = 'If you need assistance, please contact '.config('mail.inquiry_recipient').'.';

        $this->afterCommit($affiliate, 'rejection_notification_dispatched', [
            'subject' => 'Nandini Partner Circle application update',
            'eyebrow' => 'Nandini Partner Circle',
            'heading' => 'Application Update',
            'paragraphs' => $paragraphs,
        ]);
    }

    public function afterCommitPayoutPaid(AffiliatePayout $payout): void
    {
        if (! AffiliateProgramSetting::current()->payout_paid_notification_enabled) {
            return;
        }

        $affiliate = $payout->affiliate;
        $method = AffiliatePaymentMethod::from($payout->payment_method_snapshot)->label();

        $this->afterCommit($affiliate, 'affiliate_payout.paid_notification_dispatched', [
            'subject' => 'Your Nandini Partner Circle commission has been paid',
            'eyebrow' => 'Nandini Partner Circle',
            'heading' => 'Commission Paid',
            'paragraphs' => [
                'Your Nandini Partner Circle commission payment has been completed.',
                'Please review the payment details below. Depending on your bank or payment provider, the funds may take additional time to appear in your account.',
                'If you do not receive the payment after the normal processing time, please contact us and include the payment reference shown below.',
            ],
            'details' => [
                'Payout number' => $payout->payout_number,
                'Amount' => $payout->currency.' '.number_format((float) $payout->net_payout_amount, 2),
                'Payment method' => $method,
                'Payment date' => $payout->paid_at->timezone(config('app.timezone'))->format('d M Y'),
                'Payment reference' => $payout->payment_reference,
            ],
            'actionLabel' => 'Open Affiliate Dashboard',
            'actionUrl' => route('affiliate.dashboard'),
        ], [
            'payout_id' => $payout->id,
            'payout_number' => $payout->payout_number,
        ], $payout);
    }

    public function afterCommitNewBooking(AffiliateBooking $booking): void
    {
        $affiliate = $booking->affiliate;

        if (! $affiliate?->isApproved() || ! in_array($booking->booking_status, [
            \App\Enums\AffiliateBookingStatus::Confirmed,
            \App\Enums\AffiliateBookingStatus::InHouse,
        ], true)) {
            return;
        }

        $estimatedCommission = $booking->estimated_commission_amount !== null && filled($booking->currency)
            ? $booking->currency.' '.number_format((float) $booking->estimated_commission_amount, 2)
            : 'Pending calculation';

        $this->afterCommit($affiliate, 'affiliate_booking.created_notification_dispatched', [
            'subject' => 'A new booking was tracked through your affiliate link',
            'eyebrow' => 'Nandini Partner Circle',
            'heading' => 'New Booking Tracked',
            'paragraphs' => [
                'A new booking has been attributed to your Nandini Partner Circle affiliate account.',
                'The estimated commission is shown below. Final eligibility is confirmed after the guest completes their stay.',
            ],
            'details' => [
                'Room type' => $booking->roomTypesLabel(),
                'Check-in' => $booking->check_in_date->format('d M Y'),
                'Check-out' => $booking->check_out_date->format('d M Y'),
                'Estimated commission' => $estimatedCommission,
            ],
            'actionLabel' => 'Open Affiliate Dashboard',
            'actionUrl' => route('affiliate.dashboard'),
        ], [
            'affiliate_booking_id' => $booking->id,
        ], $booking);
    }

    private function afterCommit(
        Affiliate $affiliate,
        string $event,
        array $email,
        array $metadata = [],
        mixed $subject = null,
        bool $includeBcc = true,
    ): void {
        DB::afterCommit(fn () => $this->send($affiliate, $event, $email, $metadata, $subject, $includeBcc));
    }

    private function send(
        Affiliate $affiliate,
        string $event,
        array $email,
        array $metadata,
        mixed $subject,
        bool $includeBcc,
    ): void
    {
        try {
            $result = $this->relay->sendView('emails.affiliate.notification', [
                'affiliate' => $affiliate,
                ...$email,
            ], [
                'to' => $affiliate->email,
                'bcc' => $includeBcc ? $this->affiliateBcc() : [],
                'subject' => $email['subject'],
            ]);

            if (! $result['success']) {
                Log::warning('Affiliate email could not be sent through relay.', [
                    'affiliate_id' => $affiliate->getKey(),
                    'notification_event' => $event,
                    'relay_response' => $result,
                ]);

                return;
            }

            $this->audit->record($affiliate, $event, metadata: $metadata, subject: $subject);
        } catch (Throwable $exception) {
            Log::error('Affiliate email relay dispatch failed.', [
                'affiliate_id' => $affiliate->getKey(),
                'notification_event' => $event,
                'exception' => $exception::class,
            ]);
        }
    }

    private function sendRegistrationRequestToStaff(Affiliate $affiliate): void
    {
        $recipient = trim((string) config('mail.affiliate_registration_recipient'));
        $cc = trim((string) config('mail.affiliate_registration_cc'));

        if ($recipient === '') {
            Log::warning('Affiliate registration staff notification recipient is not configured.', [
                'affiliate_id' => $affiliate->getKey(),
            ]);

            return;
        }

        $socialProfiles = collect([
            'Instagram' => $affiliate->instagram,
            'Facebook' => $affiliate->facebook,
            'TikTok' => $affiliate->tiktok,
            'X' => $affiliate->x,
            'Threads' => $affiliate->threads,
        ])->filter()->all();

        $email = [
            'subject' => 'New affiliate application: '.$affiliate->name,
            'eyebrow' => 'Nandini Partner Circle',
            'heading' => 'New Affiliate Request',
            'greeting' => 'Reservations Team',
            'paragraphs' => [
                'A new application has been submitted through the Nandini Partner Circle affiliate registration page.',
                'Please review the applicant details and complete the approval decision in the administration portal.',
            ],
            'details' => [
                'Name' => $affiliate->name,
                'Email' => $affiliate->email,
                'Phone / WhatsApp' => $affiliate->phone_whatsapp,
                ...$socialProfiles,
                'Submitted' => $affiliate->created_at?->timezone(config('app.timezone'))->format('d M Y H:i T'),
            ],
            'actionLabel' => 'Review Affiliate Application',
            'actionUrl' => route('filament.admin.resources.affiliates.view', $affiliate),
            'footerNote' => 'This is an internal notification generated by the Nandini affiliate registration system.',
        ];

        try {
            $result = $this->relay->sendView('emails.affiliate.notification', [
                'affiliate' => $affiliate,
                ...$email,
            ], [
                'to' => $recipient,
                'cc' => $cc === '' ? [] : [$cc],
                'reply_to' => $affiliate->email,
                'subject' => $email['subject'],
            ]);

            if (! $result['success']) {
                Log::warning('Affiliate registration staff notification could not be sent through relay.', [
                    'affiliate_id' => $affiliate->getKey(),
                    'relay_response' => $result,
                ]);

                return;
            }

            $this->audit->record($affiliate, 'affiliate_registration.staff_notification_dispatched', metadata: [
                'recipient' => $recipient,
                'cc' => $cc === '' ? [] : [$cc],
            ]);
        } catch (Throwable $exception) {
            Log::error('Affiliate registration staff email relay dispatch failed.', [
                'affiliate_id' => $affiliate->getKey(),
                'exception' => $exception::class,
            ]);
        }
    }

    private function affiliateBcc(): array
    {
        return array_values(array_filter(array_map(
            fn (string $address): string => trim($address),
            explode(',', (string) config('mail.affiliate_notification_bcc')),
        )));
    }
}

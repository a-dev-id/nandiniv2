<?php

namespace App\Notifications;

use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MemberTierDowngradedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $previousTier,
        protected string $newTier,
        protected int $previousPoints,
        protected int $newPoints,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Membership Tier Has Been Updated')
            ->replyTo(config('mail.guest_reply_to'))
            ->bcc($this->guestBcc())
            ->view('emails.membership.tier-downgraded', [
                'member' => $notifiable,
                'previousTierLabel' => Member::getTierLabelForTier($this->previousTier),
                'newTierLabel' => Member::getTierLabelForTier($this->newTier),
                'previousPoints' => $this->previousPoints,
                'newPoints' => $this->newPoints,
                'dashboardUrl' => route('membership.dashboard'),
            ]);
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

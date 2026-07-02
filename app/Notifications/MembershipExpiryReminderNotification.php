<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MembershipExpiryReminderNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Membership Tier Is About to Be Downgraded')
            ->replyTo(config('mail.guest_reply_to'))
            ->bcc($this->guestBcc())
            ->view('emails.membership.expiry-reminder', [
                'member' => $notifiable,
                'dashboardUrl' => route('membership.dashboard'),
            ]);
    }

    /**
     * @return array<int, string>
     */
    private function guestBcc(): array
    {
        $bcc = trim((string) config('mail.guest_bcc'));

        return $bcc === '' ? [] : array_values(array_filter(array_map('trim', explode(',', $bcc))));
    }

}

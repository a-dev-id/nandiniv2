<?php

namespace App\Notifications;

use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class VerifyMemberEmailNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Member $member
    ) {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = URL::temporarySignedRoute(
            'membership.verify.email',
            now()->addHours(24),
            [
                'member' => $this->member->id,
                'hash' => sha1($this->member->email),
            ]
        );

        return (new MailMessage)
            ->subject('Verify Your Nandini Inner Circle Email')
            ->replyTo(config('mail.guest_reply_to'))
            ->bcc($this->guestBcc())
            ->view('emails.membership.verify-email', [
                'member' => $this->member,
                'verificationUrl' => $verificationUrl,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
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

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MemberResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(protected string $token)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = route('membership.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject('Reset Your Nandini Inner Circle Password')
            ->replyTo(config('mail.guest_reply_to'))
            ->cc($this->guestCc())
            ->bcc($this->guestBcc())
            ->view('emails.membership.reset-password', [
                'member' => $notifiable,
                'resetUrl' => $resetUrl,
                'expiresInMinutes' => config('auth.passwords.members.expire', 60),
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

    /**
     * @return array<int, string>
     */
    private function guestCc(): array
    {
        $cc = trim((string) config('mail.guest_cc'));

        return $cc === '' ? [] : array_values(array_filter(array_map('trim', explode(',', $cc))));
    }
}

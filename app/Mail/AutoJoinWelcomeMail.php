<?php

namespace App\Mail;

use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AutoJoinWelcomeMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Member $member,
        public ?string $bookingNumber = null,
        public ?string $roomName = null,
        public ?string $checkinDate = null,
        public ?string $checkoutDate = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Nandini Inner Circle',
            replyTo: $this->guestReplyTo(),
            bcc: $this->guestBcc(),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.membership.auto-join-welcome',
            with: [
                'loginUrl' => route('membership.login'),
                'passwordResetUrl' => route('membership.password.request'),
            ],
        );
    }

    /**
     * @return array<int, string>
     */
    private function guestBcc(): array
    {
        $bcc = trim((string) config('mail.guest_bcc'));

        return $bcc === '' ? [] : array_values(array_filter(array_map('trim', explode(',', $bcc))));
    }

    /**
     * @return array<int, string>
     */
    private function guestReplyTo(): array
    {
        $replyTo = trim((string) config('mail.guest_reply_to'));

        return $replyTo === '' ? [] : [$replyTo];
    }
}

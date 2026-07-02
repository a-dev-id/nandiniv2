<?php

namespace App\Notifications;

use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MemberPointsAddedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected int $pointsAdded,
        protected int $totalPoints,
        protected int $previousPoints,
        protected string $previousTier,
        protected string $newTier,
        protected ?string $description = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Points Have Been Added to Your Account')
            ->replyTo(config('mail.guest_reply_to'))
            ->bcc($this->guestBcc())
            ->view('emails.membership.points-added', [
                'member' => $notifiable,
                'pointsAdded' => $this->pointsAdded,
                'totalPoints' => $this->totalPoints,
                'previousPoints' => $this->previousPoints,
                'previousTierLabel' => Member::getTierLabelForTier($this->previousTier),
                'newTierLabel' => Member::getTierLabelForTier($this->newTier),
                'description' => $this->description,
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

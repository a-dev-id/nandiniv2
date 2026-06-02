<?php

namespace App\Notifications;

use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AutoJoinedMemberNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Member $member,
        protected string $temporaryPassword,
        protected string $reservationId,
        protected ?string $roomName = null,
        protected ?string $checkinDate = null,
        protected ?string $checkoutDate = null,
    ) {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to Nandini Inner Circle')
            ->view('emails.membership.auto-joined', [
                'member' => $this->member,
                'temporaryPassword' => $this->temporaryPassword,
                'reservationId' => $this->reservationId,
                'roomName' => $this->roomName,
                'checkinDate' => $this->checkinDate,
                'checkoutDate' => $this->checkoutDate,
                'loginUrl' => route('membership.login'),
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}

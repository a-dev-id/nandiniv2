<?php

namespace App\Filament\Resources\Members\Pages;

use App\Filament\Resources\Members\MemberResource;
use App\Models\Member;
use App\Services\MembershipEmailRelayService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateMember extends CreateRecord
{
    protected static string $resource = MemberResource::class;

    protected ?string $temporaryPassword = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->temporaryPassword = (string) ($data['password'] ?? '');

        $data['name'] = trim((string) ($data['name'] ?? ''));

        if ($data['name'] === '') {
            $data['name'] = trim((string) ($data['first_name'] ?? '') . ' ' . (string) ($data['last_name'] ?? ''));
        }

        if ($data['name'] === '') {
            $data['name'] = (string) ($data['email'] ?? '');
        }

        $data['email'] = strtolower(trim((string) ($data['email'] ?? '')));
        $data['member_source'] = $data['member_source'] ?? Member::SOURCE_MANUAL_REGISTER;
        $data['tier'] = $data['tier'] ?? Member::TIER_BRONZE;
        $data['points'] = 0;
        $data['must_change_password'] = true;
        $data['membership_started_at'] = $data['membership_started_at'] ?? now();
        $data['membership_expires_at'] = $data['membership_expires_at'] ?? now()->addYear();
        $data['email_verified_at'] = $data['email_verified_at'] ?? now();

        return $data;
    }

    protected function afterCreate(): void
    {
        $result = app(MembershipEmailRelayService::class)->sendView('emails.membership.auto-join-welcome', [
            'member' => $this->record,
            'bookingNumber' => null,
            'roomName' => null,
            'checkinDate' => null,
            'checkoutDate' => null,
            'loginUrl' => route('membership.login'),
            'passwordResetUrl' => route('membership.password.request'),
            'temporaryPassword' => $this->temporaryPassword,
            'manuallyCreated' => true,
        ], [
            'to' => $this->record->email,
            'bcc' => $this->mailRecipients(config('mail.guest_bcc')),
            'subject' => 'Welcome to Nandini Inner Circle',
        ]);

        if ($result['success']) {
            $this->record->forceFill(['welcome_email_sent_at' => now()])->save();

            Notification::make()
                ->title('Member created and welcome email sent')
                ->success()
                ->send();

            return;
        }

        Log::warning('Manually created member welcome email could not be sent through relay.', [
            'member_id' => $this->record->id,
            'email' => $this->record->email,
            'relay_response' => $result,
        ]);

        Notification::make()
            ->title('Member created, but welcome email failed')
            ->body((string) ($result['error'] ?? 'The email relay returned an error.'))
            ->warning()
            ->persistent()
            ->send();
    }

    /**
     * @return array<int, string>
     */
    private function mailRecipients(mixed $value): array
    {
        return collect(explode(',', (string) $value))
            ->map(fn(string $recipient): string => trim($recipient))
            ->filter()
            ->values()
            ->all();
    }
}

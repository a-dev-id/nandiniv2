<?php

namespace App\Filament\Resources\AffiliateBookings\Pages;

use App\Enums\AffiliateBookingStatus;
use App\Filament\Resources\AffiliateBookings\AffiliateBookingResource;
use App\Models\Permission;
use App\Services\Affiliate\Booking\SetManualAffiliateBookingStatusService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ViewRecord;

class ViewAffiliateBooking extends ViewRecord
{
    protected static string $resource = AffiliateBookingResource::class;

    protected function getHeaderActions(): array
    {
        $canManage = fn (): bool => auth()->user()?->hasPermissionTo(Permission::AFFILIATE_BOOKING_MANAGE) ?? false;

        return [
            Action::make('set_manual_outcome')
                ->label('Set Manual Outcome')
                ->color('warning')
                ->visible($canManage)
                ->form([
                    Select::make('status')
                        ->label('Outcome')
                        ->options([
                            AffiliateBookingStatus::Cancelled->value => AffiliateBookingStatus::Cancelled->label(),
                            AffiliateBookingStatus::NoShow->value => AffiliateBookingStatus::NoShow->label(),
                            AffiliateBookingStatus::Refunded->value => AffiliateBookingStatus::Refunded->label(),
                        ])
                        ->required(),
                    Textarea::make('reason')
                        ->label('Internal Reason')
                        ->helperText('Required for audit history. Affiliates see the outcome, not this internal note.')
                        ->required()
                        ->maxLength(2000),
                ])
                ->fillForm(fn (): array => [
                    'status' => $this->record->manual_booking_status?->value,
                    'reason' => $this->record->manual_status_reason,
                ])
                ->action(function (array $data): void {
                    $this->record = app(SetManualAffiliateBookingStatusService::class)->set(
                        $this->record,
                        AffiliateBookingStatus::from($data['status']),
                        $data['reason'],
                        auth()->user(),
                    );
                })
                ->successNotificationTitle('Manual booking outcome saved'),
            Action::make('clear_manual_outcome')
                ->label('Use Synced Outcome')
                ->color('gray')
                ->requiresConfirmation()
                ->modalDescription('Remove the manual outcome and use the latest status received from the booking system.')
                ->visible(fn (): bool => $canManage() && $this->record->manual_booking_status !== null)
                ->action(function (): void {
                    $this->record = app(SetManualAffiliateBookingStatusService::class)->clear($this->record, auth()->user());
                })
                ->successNotificationTitle('Synced booking outcome restored'),
        ];
    }
}

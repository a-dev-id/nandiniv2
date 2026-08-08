<?php

namespace App\Filament\Resources\AffiliatePaymentProfiles\Pages;

use App\Filament\Resources\AffiliatePaymentProfiles\AffiliatePaymentProfileResource;
use App\Models\Permission;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewAffiliatePaymentProfile extends ViewRecord
{
    protected static string $resource = AffiliatePaymentProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('incomplete')->label('Mark Incomplete')->color('warning')->requiresConfirmation()->visible(fn (): bool => auth()->user()->hasPermissionTo(Permission::AFFILIATE_PAYMENT_PROFILE_MANAGE))->action(function (): void {
                app(\App\Services\Affiliate\Finance\AffiliatePaymentProfileService::class)->markIncomplete($this->record, auth()->user());
                Notification::make()->title('Payment profile marked incomplete')->warning()->send();
            }),
        ];
    }
}

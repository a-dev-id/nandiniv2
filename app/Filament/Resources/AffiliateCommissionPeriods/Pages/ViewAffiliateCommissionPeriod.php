<?php

namespace App\Filament\Resources\AffiliateCommissionPeriods\Pages;

use App\Enums\AffiliateCommissionPeriodStatus;
use App\Filament\Resources\AffiliateCommissionPeriods\AffiliateCommissionPeriodResource;
use App\Models\Permission;
use App\Services\Affiliate\Finance\AffiliateCommissionReviewService;
use App\Services\Affiliate\Finance\PrepareAffiliateCommissionPeriodService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewAffiliateCommissionPeriod extends ViewRecord
{
    protected static string $resource = AffiliateCommissionPeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refreshItems')->label('Prepare New Items')->visible(fn (): bool => ! $this->record->isFinalized() && auth()->user()->hasPermissionTo(Permission::AFFILIATE_COMMISSION_VALIDATE))->action(function (): void {
                $summary = app(PrepareAffiliateCommissionPeriodService::class)->prepare($this->record->period_year, $this->record->period_month, auth()->user());
                Notification::make()->title("Created {$summary['created']} new commission items")->success()->send();
            }),
            Action::make('finalize')->label('Finalize Commission Period')->color('success')->requiresConfirmation()->modalDescription('Finalization locks normal review actions. Held items remain carried for later review and approved items become payout-eligible.')->visible(fn (): bool => ! $this->record->isFinalized() && auth()->user()->hasPermissionTo(Permission::AFFILIATE_COMMISSION_VALIDATE))->action(function (): void {
                app(AffiliateCommissionReviewService::class)->finalize($this->record, auth()->user());
                Notification::make()->title('Commission period finalized')->success()->send();
            }),
            Action::make('reopen')->label('Reopen Period')->color('warning')->requiresConfirmation()->form([Textarea::make('reason')->required()->maxLength(2000)])->visible(fn (): bool => $this->record->status === AffiliateCommissionPeriodStatus::Finalized && auth()->user()->hasPermissionTo(Permission::AFFILIATE_COMMISSION_APPROVE))->action(function (array $data): void {
                app(AffiliateCommissionReviewService::class)->reopen($this->record, auth()->user(), $data['reason']);
                Notification::make()->title('Commission period reopened')->warning()->send();
            }),
        ];
    }
}

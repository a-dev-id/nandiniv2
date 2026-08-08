<?php

namespace App\Filament\Resources\AffiliateCommissionItems\Pages;

use App\Enums\AffiliateCommissionItemStatus;
use App\Filament\Resources\AffiliateCommissionItems\AffiliateCommissionItemResource;
use App\Models\Permission;
use App\Services\Affiliate\Finance\AffiliateCommissionReviewService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewAffiliateCommissionItem extends ViewRecord
{
    protected static string $resource = AffiliateCommissionItemResource::class;

    protected function getHeaderActions(): array
    {
        $visible = fn (): bool => ! $this->record->period->isFinalized() && ! $this->record->status->isFinanciallyLocked();

        return [
            Action::make('approve')->label('Approve Commission')->color('success')->visible(fn (): bool => $visible() && $this->record->status !== AffiliateCommissionItemStatus::Excluded && auth()->user()->hasPermissionTo(Permission::AFFILIATE_COMMISSION_APPROVE))->form([
                TextInput::make('approved_commission_amount')->numeric()->required()->default(fn (): string => $this->record->original_commission_amount)->prefix(fn (): string => $this->record->currency),
                Textarea::make('adjustment_reason')->helperText('Required only when the amount differs from the original commission.')->maxLength(2000),
            ])->requiresConfirmation()->action(function (array $data): void {
                app(AffiliateCommissionReviewService::class)->approve($this->record, auth()->user(), $data['approved_commission_amount'], $data['adjustment_reason'] ?? null);
                Notification::make()->title('Commission approved')->success()->send();
            }),
            Action::make('hold')->label('Hold Commission')->color('warning')->visible(fn (): bool => $visible() && $this->record->status !== AffiliateCommissionItemStatus::Excluded && auth()->user()->hasPermissionTo(Permission::AFFILIATE_COMMISSION_VALIDATE))->form([Textarea::make('hold_reason')->required()->maxLength(2000)])->action(function (array $data): void {
                app(AffiliateCommissionReviewService::class)->hold($this->record, auth()->user(), $data['hold_reason']);
                Notification::make()->title('Commission held')->warning()->send();
            }),
            Action::make('exclude')->label('Exclude Commission')->color('danger')->visible(fn (): bool => $visible() && $this->record->status !== AffiliateCommissionItemStatus::Excluded && auth()->user()->hasPermissionTo(Permission::AFFILIATE_COMMISSION_VALIDATE))->form([Textarea::make('exclusion_reason')->required()->maxLength(2000)])->requiresConfirmation()->action(function (array $data): void {
                app(AffiliateCommissionReviewService::class)->exclude($this->record, auth()->user(), $data['exclusion_reason']);
                Notification::make()->title('Commission excluded')->success()->send();
            }),
        ];
    }
}

<?php

namespace App\Filament\Resources\AffiliatePayouts\Pages;

use App\Enums\AffiliatePayoutStatus;
use App\Filament\Resources\AffiliatePayouts\AffiliatePayoutResource;
use App\Models\Permission;
use App\Services\Affiliate\Finance\AffiliatePayoutWorkflowService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewAffiliatePayout extends ViewRecord
{
    protected static string $resource = AffiliatePayoutResource::class;

    protected function getHeaderActions(): array
    {
        $manage = fn (): bool => auth()->user()->hasPermissionTo(Permission::AFFILIATE_PAYOUT_MANAGE);

        return [
            Action::make('adjust')->label('Add Payout Adjustment')->visible(fn (): bool => $manage() && ! $this->record->status->isTerminal())->form([
                TextInput::make('adjustment_amount')->numeric()->required()->default(fn (): string => $this->record->adjustment_amount)->prefix(fn (): string => $this->record->currency),
                Textarea::make('adjustment_reason')->maxLength(2000),
            ])->action(fn (array $data) => app(AffiliatePayoutWorkflowService::class)->adjust($this->record, auth()->user(), $data['adjustment_amount'], $data['adjustment_reason'] ?? null)),
            Action::make('ready')->label('Mark Ready')->color('success')->requiresConfirmation()->visible(fn (): bool => $manage() && in_array($this->record->status, [AffiliatePayoutStatus::Draft, AffiliatePayoutStatus::Failed], true))->action(fn () => app(AffiliatePayoutWorkflowService::class)->markReady($this->record, auth()->user())),
            Action::make('processing')->label('Start Processing')->visible(fn (): bool => $manage() && $this->record->status === AffiliatePayoutStatus::Ready)->action(fn () => app(AffiliatePayoutWorkflowService::class)->startProcessing($this->record, auth()->user())),
            Action::make('paid')->label('Mark Paid')->color('success')->requiresConfirmation()->visible(fn (): bool => $manage() && in_array($this->record->status, [AffiliatePayoutStatus::Ready, AffiliatePayoutStatus::Processing], true))->form([
                DatePicker::make('payment_date')->required()->default(now()), TextInput::make('payment_reference')->required()->maxLength(191),
            ])->action(function (array $data): void {
                app(AffiliatePayoutWorkflowService::class)->markPaid($this->record, auth()->user(), $data['payment_date'], $data['payment_reference']);
                Notification::make()->title('Payout recorded as paid')->success()->send();
            }),
            Action::make('failed')->label('Mark Failed')->color('danger')->visible(fn (): bool => $manage() && in_array($this->record->status, [AffiliatePayoutStatus::Ready, AffiliatePayoutStatus::Processing], true))->form([Textarea::make('failure_reason')->required()->maxLength(2000)])->action(fn (array $data) => app(AffiliatePayoutWorkflowService::class)->markFailed($this->record, auth()->user(), $data['failure_reason'])),
            Action::make('cancel')->label('Cancel Payout')->color('danger')->requiresConfirmation()->visible(fn (): bool => $manage() && ! $this->record->status->isTerminal())->form([Textarea::make('cancellation_reason')->required()->maxLength(2000)])->action(fn (array $data) => app(AffiliatePayoutWorkflowService::class)->cancel($this->record, auth()->user(), $data['cancellation_reason'])),
        ];
    }
}

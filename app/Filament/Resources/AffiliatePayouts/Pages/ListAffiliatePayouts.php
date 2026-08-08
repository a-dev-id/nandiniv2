<?php

namespace App\Filament\Resources\AffiliatePayouts\Pages;

use App\Filament\Resources\AffiliatePayouts\AffiliatePayoutResource;
use App\Models\Permission;
use App\Services\Affiliate\Finance\PrepareAffiliatePayoutsService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListAffiliatePayouts extends ListRecords
{
    protected static string $resource = AffiliatePayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [Action::make('prepare')->label('Prepare Eligible Payouts')->requiresConfirmation()->visible(fn (): bool => auth()->user()->hasPermissionTo(Permission::AFFILIATE_PAYOUT_MANAGE))->action(function (): void {
            $summary = app(PrepareAffiliatePayoutsService::class)->prepare(auth()->user());
            Notification::make()->title("Created {$summary['created']} payout(s)")->body("Carried {$summary['carried']}; missing profile {$summary['missing_profile']}; missing threshold {$summary['missing_threshold']}; account review {$summary['account_review']}.")->success()->send();
        })];
    }
}

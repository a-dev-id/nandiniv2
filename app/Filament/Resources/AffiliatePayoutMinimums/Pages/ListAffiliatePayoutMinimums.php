<?php

namespace App\Filament\Resources\AffiliatePayoutMinimums\Pages;

use App\Filament\Resources\AffiliatePayoutMinimums\AffiliatePayoutMinimumResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAffiliatePayoutMinimums extends ListRecords
{
    protected static string $resource = AffiliatePayoutMinimumResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}

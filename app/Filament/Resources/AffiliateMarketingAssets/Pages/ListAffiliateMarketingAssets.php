<?php

namespace App\Filament\Resources\AffiliateMarketingAssets\Pages;

use App\Filament\Resources\AffiliateMarketingAssets\AffiliateMarketingAssetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAffiliateMarketingAssets extends ListRecords
{
    protected static string $resource = AffiliateMarketingAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}

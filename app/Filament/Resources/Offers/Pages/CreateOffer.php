<?php

namespace App\Filament\Resources\Offers\Pages;

use App\Filament\Resources\Offers\OfferResource;
use App\Models\Offer;
use Filament\Resources\Pages\CreateRecord;

class CreateOffer extends CreateRecord
{
    protected static string $resource = OfferResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['sort_order'] = (Offer::max('sort_order') ?? 0) + 1;

        return $data;
    }
}

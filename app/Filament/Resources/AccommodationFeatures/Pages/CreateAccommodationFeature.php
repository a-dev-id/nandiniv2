<?php

namespace App\Filament\Resources\AccommodationFeatures\Pages;

use App\Filament\Resources\AccommodationFeatures\AccommodationFeatureResource;
use App\Models\AccommodationFeature;
use Filament\Resources\Pages\CreateRecord;

class CreateAccommodationFeature extends CreateRecord
{
    protected static string $resource = AccommodationFeatureResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['sort_order'] = AccommodationFeature::max('sort_order') + 1;

        return $data;
    }
}

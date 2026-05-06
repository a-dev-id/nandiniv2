<?php

namespace App\Filament\Resources\AccommodationFeatures\Pages;

use App\Filament\Resources\AccommodationFeatures\AccommodationFeatureResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAccommodationFeatures extends ListRecords
{
    protected static string $resource = AccommodationFeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

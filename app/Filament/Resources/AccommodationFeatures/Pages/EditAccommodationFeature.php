<?php

namespace App\Filament\Resources\AccommodationFeatures\Pages;

use App\Filament\Resources\AccommodationFeatures\AccommodationFeatureResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAccommodationFeature extends EditRecord
{
    protected static string $resource = AccommodationFeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

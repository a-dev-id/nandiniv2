<?php

namespace App\Filament\Resources\SignatureDishes\Pages;

use App\Filament\Resources\SignatureDishes\SignatureDishResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSignatureDishes extends ListRecords
{
    protected static string $resource = SignatureDishResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}

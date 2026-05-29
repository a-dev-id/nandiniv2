<?php

namespace App\Filament\Resources\Spas\Pages;

use App\Filament\Resources\Spas\SpaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSpas extends ListRecords
{
    protected static string $resource = SpaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

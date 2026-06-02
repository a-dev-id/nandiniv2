<?php

namespace App\Filament\Resources\MiniPopups\Pages;

use App\Filament\Resources\MiniPopups\MiniPopupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMiniPopups extends ListRecords
{
    protected static string $resource = MiniPopupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

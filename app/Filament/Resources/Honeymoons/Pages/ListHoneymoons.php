<?php

namespace App\Filament\Resources\Honeymoons\Pages;

use App\Filament\Resources\Honeymoons\HoneymoonResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHoneymoons extends ListRecords
{
    protected static string $resource = HoneymoonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

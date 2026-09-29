<?php

namespace App\Filament\Resources\FestiveEvents\Pages;

use App\Filament\Resources\FestiveEvents\FestiveEventResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFestiveEvents extends ListRecords
{
    protected static string $resource = FestiveEventResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}

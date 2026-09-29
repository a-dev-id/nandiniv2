<?php

namespace App\Filament\Resources\FestiveEvents\Pages;

use App\Filament\Resources\FestiveEvents\FestiveEventResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFestiveEvent extends EditRecord
{
    protected static string $resource = FestiveEventResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}

<?php

namespace App\Filament\Resources\Spas\Pages;

use App\Filament\Resources\Spas\SpaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSpa extends EditRecord
{
    protected static string $resource = SpaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

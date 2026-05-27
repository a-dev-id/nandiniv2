<?php

namespace App\Filament\Resources\Honeymoons\Pages;

use App\Filament\Resources\Honeymoons\HoneymoonResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHoneymoon extends EditRecord
{
    protected static string $resource = HoneymoonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

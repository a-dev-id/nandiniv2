<?php

namespace App\Filament\Resources\SignatureDishes\Pages;

use App\Filament\Resources\SignatureDishes\SignatureDishResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSignatureDish extends EditRecord
{
    protected static string $resource = SignatureDishResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}

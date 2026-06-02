<?php

namespace App\Filament\Resources\MiniPopups\Pages;

use App\Filament\Resources\MiniPopups\MiniPopupResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMiniPopup extends EditRecord
{
    protected static string $resource = MiniPopupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\MiniPopups\Pages;

use App\Filament\Resources\MiniPopups\MiniPopupResource;
use App\Models\MiniPopup;
use Filament\Resources\Pages\CreateRecord;

class CreateMiniPopup extends CreateRecord
{
    protected static string $resource = MiniPopupResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['sort_order'] = (MiniPopup::max('sort_order') ?? 0) + 1;

        return $data;
    }
}

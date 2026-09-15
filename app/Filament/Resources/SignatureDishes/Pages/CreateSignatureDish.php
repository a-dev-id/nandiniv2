<?php

namespace App\Filament\Resources\SignatureDishes\Pages;

use App\Filament\Resources\SignatureDishes\SignatureDishResource;
use App\Models\SignatureDish;
use Filament\Resources\Pages\CreateRecord;

class CreateSignatureDish extends CreateRecord
{
    protected static string $resource = SignatureDishResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['sort_order'] ??= (SignatureDish::query()->max('sort_order') ?? 0) + 1;

        return $data;
    }
}

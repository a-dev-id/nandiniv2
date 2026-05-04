<?php

namespace App\Filament\Resources\ExperienceCategories\Pages;

use App\Filament\Resources\ExperienceCategories\ExperienceCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditExperienceCategory extends EditRecord
{
    protected static string $resource = ExperienceCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

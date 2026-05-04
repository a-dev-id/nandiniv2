<?php

namespace App\Filament\Resources\ExperienceCategories\Pages;

use App\Filament\Resources\ExperienceCategories\ExperienceCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExperienceCategories extends ListRecords
{
    protected static string $resource = ExperienceCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

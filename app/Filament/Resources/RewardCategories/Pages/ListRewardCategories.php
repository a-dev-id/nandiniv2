<?php

namespace App\Filament\Resources\RewardCategories\Pages;

use App\Filament\Resources\RewardCategories\RewardCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRewardCategories extends ListRecords
{
    protected static string $resource = RewardCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

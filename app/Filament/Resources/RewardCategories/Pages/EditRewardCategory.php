<?php

namespace App\Filament\Resources\RewardCategories\Pages;

use App\Filament\Resources\RewardCategories\RewardCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRewardCategory extends EditRecord
{
    protected static string $resource = RewardCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

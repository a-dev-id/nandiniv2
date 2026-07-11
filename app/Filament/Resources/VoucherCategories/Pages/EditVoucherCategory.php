<?php

namespace App\Filament\Resources\VoucherCategories\Pages;

use App\Filament\Resources\VoucherCategories\VoucherCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVoucherCategory extends EditRecord
{
    protected static string $resource = VoucherCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}

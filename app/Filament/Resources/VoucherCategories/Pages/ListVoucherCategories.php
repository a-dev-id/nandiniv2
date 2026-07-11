<?php

namespace App\Filament\Resources\VoucherCategories\Pages;

use App\Filament\Resources\VoucherCategories\VoucherCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVoucherCategories extends ListRecords
{
    protected static string $resource = VoucherCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}

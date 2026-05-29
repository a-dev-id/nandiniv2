<?php

namespace App\Filament\Resources\BlogNews\Pages;

use App\Filament\Resources\BlogNews\BlogNewsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBlogNews extends EditRecord
{
    protected static string $resource = BlogNewsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

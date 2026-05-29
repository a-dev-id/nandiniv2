<?php

namespace App\Filament\Resources\BlogNews\Pages;

use App\Filament\Resources\BlogNews\BlogNewsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBlogNews extends ListRecords
{
    protected static string $resource = BlogNewsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

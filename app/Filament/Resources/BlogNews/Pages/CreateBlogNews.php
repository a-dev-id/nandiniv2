<?php

namespace App\Filament\Resources\BlogNews\Pages;

use App\Filament\Resources\BlogNews\BlogNewsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBlogNews extends CreateRecord
{
    protected static string $resource = BlogNewsResource::class;
}

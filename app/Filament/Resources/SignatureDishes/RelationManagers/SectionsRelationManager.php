<?php

namespace App\Filament\Resources\SignatureDishes\RelationManagers;

use App\Filament\Resources\BlogNews\RelationManagers\SectionsRelationManager as BlogSectionsRelationManager;

class SectionsRelationManager extends BlogSectionsRelationManager
{
    protected static ?string $title = 'Content Sections';
}

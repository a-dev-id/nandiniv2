<?php

namespace App\Filament\Resources\BlogNews;

use App\Filament\Resources\BlogNews\Pages\CreateBlogNews;
use App\Filament\Resources\BlogNews\Pages\EditBlogNews;
use App\Filament\Resources\BlogNews\Pages\ListBlogNews;
use App\Filament\Resources\BlogNews\Schemas\BlogNewsForm;
use App\Filament\Resources\BlogNews\Tables\BlogNewsTable;
use App\Models\BlogNews;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;
use App\Filament\Resources\BlogNews\RelationManagers\SectionsRelationManager;

class BlogNewsResource extends Resource
{
    protected static ?string $model = BlogNews::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static string|UnitEnum|null $navigationGroup = 'Website Content';

    protected static ?string $navigationLabel = 'Blog & News';

    protected static ?string $modelLabel = 'Blog & News';

    protected static ?string $pluralModelLabel = 'Blog & News';

    protected static ?int $navigationSort = 70;

    public static function form(Schema $schema): Schema
    {
        return BlogNewsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BlogNewsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            SectionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBlogNews::route('/'),
            'create' => CreateBlogNews::route('/create'),
            'edit' => EditBlogNews::route('/{record}/edit'),
        ];
    }
}

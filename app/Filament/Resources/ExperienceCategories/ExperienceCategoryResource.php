<?php

namespace App\Filament\Resources\ExperienceCategories;

use App\Filament\Resources\ExperienceCategories\Pages\CreateExperienceCategory;
use App\Filament\Resources\ExperienceCategories\Pages\EditExperienceCategory;
use App\Filament\Resources\ExperienceCategories\Pages\ListExperienceCategories;
use App\Filament\Resources\ExperienceCategories\Schemas\ExperienceCategoryForm;
use App\Filament\Resources\ExperienceCategories\Tables\ExperienceCategoriesTable;
use App\Models\ExperienceCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ExperienceCategoryResource extends Resource
{
    protected static ?string $model = ExperienceCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?string $modelLabel = 'Experience Category';

    protected static ?string $pluralModelLabel = 'Experience Categories';

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 22;

    public static function form(Schema $schema): Schema
    {
        return ExperienceCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExperienceCategoriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExperienceCategories::route('/'),
            'create' => CreateExperienceCategory::route('/create'),
            'edit' => EditExperienceCategory::route('/{record}/edit'),
        ];
    }
}

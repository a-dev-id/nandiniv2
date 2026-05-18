<?php

namespace App\Filament\Resources\RewardCategories;

use App\Filament\Resources\RewardCategories\Pages\CreateRewardCategory;
use App\Filament\Resources\RewardCategories\Pages\EditRewardCategory;
use App\Filament\Resources\RewardCategories\Pages\ListRewardCategories;
use App\Filament\Resources\RewardCategories\Schemas\RewardCategoryForm;
use App\Filament\Resources\RewardCategories\Tables\RewardCategoriesTable;
use App\Models\RewardCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class RewardCategoryResource extends Resource
{
    protected static ?string $model = RewardCategory::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'Reward Categories';

    protected static ?string $modelLabel = 'Reward Category';

    protected static ?string $pluralModelLabel = 'Reward Categories';

    protected static string | UnitEnum | null $navigationGroup = 'Membership';

    protected static ?int $navigationSort = 29;

    public static function form(Schema $schema): Schema
    {
        return RewardCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RewardCategoriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRewardCategories::route('/'),
            'create' => CreateRewardCategory::route('/create'),
            'edit' => EditRewardCategory::route('/{record}/edit'),
        ];
    }
}

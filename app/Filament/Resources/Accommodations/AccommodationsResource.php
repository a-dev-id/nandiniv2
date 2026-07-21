<?php

namespace App\Filament\Resources\Accommodations;

use App\Filament\Resources\Accommodations\Pages\CreateAccommodations;
use App\Filament\Resources\Accommodations\Pages\EditAccommodations;
use App\Filament\Resources\Accommodations\Pages\ListAccommodations;
use App\Filament\Resources\Accommodations\RelationManagers\ImagesRelationManager;
use App\Filament\Resources\Accommodations\RelationManagers\FeaturesRelationManager;
use App\Filament\Resources\Accommodations\Schemas\AccommodationsForm;
use App\Filament\Resources\Accommodations\Tables\AccommodationsTable;
use App\Models\Accommodation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AccommodationsResource extends Resource
{
    protected static ?string $model = Accommodation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = 'Website Content';

    protected static ?string $navigationLabel = 'Accommodations';

    protected static ?int $navigationSort = 20;

    protected static ?string $modelLabel = 'Accommodation';

    protected static ?string $pluralModelLabel = 'Accommodations';

    public static function form(Schema $schema): Schema
    {
        return AccommodationsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AccommodationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ImagesRelationManager::class,
            FeaturesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAccommodations::route('/'),
            'create' => CreateAccommodations::route('/create'),
            'edit' => EditAccommodations::route('/{record}/edit'),
        ];
    }
}

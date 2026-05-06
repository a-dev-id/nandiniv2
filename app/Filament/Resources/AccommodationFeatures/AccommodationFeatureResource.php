<?php

namespace App\Filament\Resources\AccommodationFeatures;

use App\Filament\Resources\AccommodationFeatures\Pages\CreateAccommodationFeature;
use App\Filament\Resources\AccommodationFeatures\Pages\EditAccommodationFeature;
use App\Filament\Resources\AccommodationFeatures\Pages\ListAccommodationFeatures;
use App\Filament\Resources\AccommodationFeatures\Schemas\AccommodationFeatureForm;
use App\Filament\Resources\AccommodationFeatures\Tables\AccommodationFeaturesTable;
use App\Models\AccommodationFeature;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AccommodationFeatureResource extends Resource
{
    protected static ?string $model = AccommodationFeature::class;

    protected static ?string $recordTitleAttribute = 'label';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Accommodation Features';

    protected static ?string $modelLabel = 'Accommodation Feature';

    protected static ?string $pluralModelLabel = 'Accommodation Features';

    public static function form(Schema $schema): Schema
    {
        return AccommodationFeatureForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AccommodationFeaturesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAccommodationFeatures::route('/'),
            'create' => CreateAccommodationFeature::route('/create'),
            'edit' => EditAccommodationFeature::route('/{record}/edit'),
        ];
    }
}

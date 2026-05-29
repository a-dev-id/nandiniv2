<?php

namespace App\Filament\Resources\Spas;

use App\Filament\Resources\Spas\Pages\CreateSpa;
use App\Filament\Resources\Spas\Pages\EditSpa;
use App\Filament\Resources\Spas\Pages\ListSpas;
use App\Filament\Resources\Spas\Schemas\SpaForm;
use App\Filament\Resources\Spas\Tables\SpasTable;
use App\Models\Spa;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SpaResource extends Resource
{
    protected static ?string $model = Spa::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSun;

    protected static string|UnitEnum|null $navigationGroup = 'General';

    public static function form(Schema $schema): Schema
    {
        return SpaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SpasTable::configure($table);
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
            'index' => ListSpas::route('/'),
            'create' => CreateSpa::route('/create'),
            'edit' => EditSpa::route('/{record}/edit'),
        ];
    }
}

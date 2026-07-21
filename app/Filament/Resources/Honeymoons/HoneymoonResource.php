<?php

namespace App\Filament\Resources\Honeymoons;

use App\Filament\Resources\Honeymoons\Pages\CreateHoneymoon;
use App\Filament\Resources\Honeymoons\Pages\EditHoneymoon;
use App\Filament\Resources\Honeymoons\Pages\ListHoneymoons;
use App\Filament\Resources\Honeymoons\Schemas\HoneymoonForm;
use App\Filament\Resources\Honeymoons\Tables\HoneymoonsTable;
use App\Models\Honeymoon;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class HoneymoonResource extends Resource
{
    protected static ?string $model = Honeymoon::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;

    protected static string|UnitEnum|null $navigationGroup = 'Website Content';

    protected static ?int $navigationSort = 50;

    public static function form(Schema $schema): Schema
    {
        return HoneymoonForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HoneymoonsTable::configure($table);
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
            'index' => ListHoneymoons::route('/'),
            'create' => CreateHoneymoon::route('/create'),
            'edit' => EditHoneymoon::route('/{record}/edit'),
        ];
    }
}

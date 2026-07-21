<?php

namespace App\Filament\Resources\MiniPopups;

use App\Filament\Resources\MiniPopups\Pages\CreateMiniPopup;
use App\Filament\Resources\MiniPopups\Pages\EditMiniPopup;
use App\Filament\Resources\MiniPopups\Pages\ListMiniPopups;
use App\Filament\Resources\MiniPopups\Schemas\MiniPopupForm;
use App\Filament\Resources\MiniPopups\Tables\MiniPopupsTable;
use App\Models\MiniPopup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MiniPopupResource extends Resource
{
    protected static ?string $model = MiniPopup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBellAlert;

    protected static string|UnitEnum|null $navigationGroup = 'Website Content';

    protected static ?string $navigationLabel = 'Mini Popups';

    protected static ?int $navigationSort = 110;

    public static function form(Schema $schema): Schema
    {
        return MiniPopupForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MiniPopupsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMiniPopups::route('/'),
            'create' => CreateMiniPopup::route('/create'),
            'edit' => EditMiniPopup::route('/{record}/edit'),
        ];
    }
}

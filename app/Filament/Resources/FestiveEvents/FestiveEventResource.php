<?php

namespace App\Filament\Resources\FestiveEvents;

use App\Filament\Resources\FestiveEvents\Pages\CreateFestiveEvent;
use App\Filament\Resources\FestiveEvents\Pages\EditFestiveEvent;
use App\Filament\Resources\FestiveEvents\Pages\ListFestiveEvents;
use App\Filament\Resources\FestiveEvents\Schemas\FestiveEventForm;
use App\Filament\Resources\FestiveEvents\Tables\FestiveEventsTable;
use App\Models\FestiveEvent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class FestiveEventResource extends Resource
{
    protected static ?string $model = FestiveEvent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Festive Landing Page';

    protected static ?string $navigationLabel = 'Festive Detail Pages';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return FestiveEventForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FestiveEventsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFestiveEvents::route('/'),
            'create' => CreateFestiveEvent::route('/create'),
            'edit' => EditFestiveEvent::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources\SyncedWebhotelierBookings;

use App\Filament\Resources\SyncedWebhotelierBookings\Pages\ListSyncedWebhotelierBookings;
use App\Filament\Resources\SyncedWebhotelierBookings\Tables\SyncedWebhotelierBookingsTable;
use App\Models\SyncedWebhotelierBooking;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class SyncedWebhotelierBookingResource extends Resource
{
    protected static ?string $model = SyncedWebhotelierBooking::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Synced Bookings';

    protected static ?string $modelLabel = 'Synced Booking';

    protected static ?string $pluralModelLabel = 'Synced Bookings';

    protected static string | UnitEnum | null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return SyncedWebhotelierBookingsTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSyncedWebhotelierBookings::route('/'),
        ];
    }
}

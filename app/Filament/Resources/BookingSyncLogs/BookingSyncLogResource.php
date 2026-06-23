<?php

namespace App\Filament\Resources\BookingSyncLogs;

use App\Filament\Resources\BookingSyncLogs\Pages\ListBookingSyncLogs;
use App\Filament\Resources\BookingSyncLogs\Tables\BookingSyncLogsTable;
use App\Models\BookingSyncLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class BookingSyncLogResource extends Resource
{
    protected static ?string $model = BookingSyncLog::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationLabel = 'Booking Sync Logs';

    protected static ?string $modelLabel = 'Booking Sync Log';

    protected static ?string $pluralModelLabel = 'Booking Sync Logs';

    protected static string | UnitEnum | null $navigationGroup = 'Membership';

    protected static ?int $navigationSort = 50;

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return BookingSyncLogsTable::configure($table);
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
            'index' => ListBookingSyncLogs::route('/'),
        ];
    }
}

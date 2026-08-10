<?php

namespace App\Filament\Resources\AffiliateBookings;

use App\Enums\AffiliateBookingStatus;
use App\Enums\AffiliateCommissionState;
use App\Filament\Resources\AffiliateBookings\Pages\ListAffiliateBookings;
use App\Filament\Resources\AffiliateBookings\Pages\ViewAffiliateBooking;
use App\Filament\Resources\AffiliateBookings\Tables\AffiliateBookingsTable;
use App\Models\AffiliateBooking;
use App\Services\Affiliate\Booking\AffiliateMoneyFormatter;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AffiliateBookingResource extends Resource
{
    protected static ?string $model = AffiliateBooking::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = 'Affiliate Bookings';

    protected static ?string $modelLabel = 'Affiliate Booking';

    protected static string|UnitEnum|null $navigationGroup = 'Affiliate';

    protected static ?int $navigationSort = 40;

    public static function table(Table $table): Table
    {
        return AffiliateBookingsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Booking attribution')->schema([
                TextEntry::make('affiliate.name')->label('Affiliate'),
                TextEntry::make('affiliate_code_snapshot')->label('Affiliate Code'),
                TextEntry::make('external_booking_reference')->label('External Booking Reference')->placeholder('Unavailable'),
                TextEntry::make('source_system')->label('Source System'),
                TextEntry::make('attribution_warning')->label('Attribution Warning')->placeholder('None')->columnSpanFull(),
            ])->columns(2),
            Section::make('Stay and rooms')->schema([
                TextEntry::make('room_types')->label('Room Type(s)')->state(fn (AffiliateBooking $record): string => $record->roomTypesLabel()),
                TextEntry::make('stay_nights')->label('Nights')->numeric(),
                TextEntry::make('check_in_date')->label('Check-in')->date('d M Y'),
                TextEntry::make('check_out_date')->label('Check-out')->date('d M Y'),
                TextEntry::make('booking_status')->label('Booking Status')->badge()->formatStateUsing(fn (AffiliateBookingStatus $state): string => $state->label())->color(fn (AffiliateBookingStatus $state): string => $state->badgeColor()),
                TextEntry::make('manual_booking_status')->label('Manual Outcome')->badge()->placeholder('Uses synced status')->formatStateUsing(fn (AffiliateBookingStatus $state): string => $state->label())->color(fn (AffiliateBookingStatus $state): string => $state->badgeColor()),
                TextEntry::make('manual_status_reason')->label('Internal Manual Reason')->placeholder('None')->columnSpanFull(),
                TextEntry::make('manualStatusSetter.name')->label('Set By')->placeholder('None'),
                TextEntry::make('manual_status_set_at')->label('Set At')->dateTime('d M Y H:i:s')->placeholder('None'),
            ])->columns(2),
            Section::make('Provisional commission')->schema([
                TextEntry::make('room_revenue_amount')->label('Room Revenue')->state(fn (AffiliateBooking $record): string => $record->room_revenue_amount === null ? 'Unavailable' : app(AffiliateMoneyFormatter::class)->format($record->room_revenue_amount, $record->currency)),
                TextEntry::make('currency')->placeholder('Unavailable'),
                TextEntry::make('commission_rate_snapshot')->label('Commission Rate')->suffix('%'),
                TextEntry::make('estimated_commission_amount')->label('Estimated Commission')->state(fn (AffiliateBooking $record): string => $record->estimated_commission_amount === null ? 'Pending calculation' : app(AffiliateMoneyFormatter::class)->format($record->estimated_commission_amount, $record->currency)),
                TextEntry::make('commission_state')->label('Commission State')->badge()->formatStateUsing(fn (AffiliateCommissionState $state, AffiliateBooking $record): string => $record->commissionStatusLabel())->color(fn (AffiliateCommissionState $state): string => $state->badgeColor()),
                TextEntry::make('calculation_unavailable_reason')->label('Calculation Note')->placeholder('None')->columnSpanFull(),
            ])->columns(2),
            Section::make('Synchronization')->schema([
                TextEntry::make('last_synced_at')->label('Last Synchronized')->dateTime('d M Y H:i:s'),
                TextEntry::make('source_updated_at')->label('Source Updated')->dateTime('d M Y H:i:s')->placeholder('Unavailable'),
                TextEntry::make('synchronization_warning')->label('Synchronization Warning')->placeholder('None')->columnSpanFull(),
            ])->columns(2),
        ]);
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
            'index' => ListAffiliateBookings::route('/'),
            'view' => ViewAffiliateBooking::route('/{record}'),
        ];
    }
}

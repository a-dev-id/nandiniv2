<?php

namespace App\Filament\Resources\Affiliates\RelationManagers;

use App\Enums\AffiliateBookingStatus;
use App\Enums\AffiliateCommissionState;
use App\Filament\Resources\AffiliateBookings\AffiliateBookingResource;
use App\Models\AffiliateBooking;
use App\Models\Permission;
use App\Services\Affiliate\Booking\AffiliateMoneyFormatter;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BookingsRelationManager extends RelationManager
{
    protected static bool $isLazy = false;

    protected static string $relationship = 'bookings';

    protected static ?string $title = 'Recent tracked bookings';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth('web')->user()?->hasPermissionTo(Permission::AFFILIATE_BOOKING_VIEW) === true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->with('rooms')
                ->whereIn('affiliate_bookings.id', $this->ownerRecord->bookings()->latest('last_synced_at')->limit(50)->pluck('id')))
            ->defaultSort('last_synced_at', 'desc')
            ->columns([
                TextColumn::make('external_booking_reference')->label('External Reference')->searchable()->placeholder('Unavailable'),
                TextColumn::make('room_types')->label('Room Type(s)')->state(fn (AffiliateBooking $record): string => $record->roomTypesLabel())->wrap(),
                TextColumn::make('check_in_date')->label('Check-in')->date('d M Y'),
                TextColumn::make('check_out_date')->label('Check-out')->date('d M Y'),
                TextColumn::make('stay_nights')->label('Nights')->numeric(),
                TextColumn::make('booking_status')->label('Status')->badge()->formatStateUsing(fn (AffiliateBookingStatus $state): string => $state->label()),
                TextColumn::make('room_revenue_amount')->label('Room Revenue')->state(fn (AffiliateBooking $record): string => $record->room_revenue_amount === null ? 'Unavailable' : app(AffiliateMoneyFormatter::class)->format($record->room_revenue_amount, $record->currency)),
                TextColumn::make('estimated_commission_amount')->label('Estimated Commission')->state(fn (AffiliateBooking $record): string => $record->estimated_commission_amount === null ? 'Pending calculation' : app(AffiliateMoneyFormatter::class)->format($record->estimated_commission_amount, $record->currency)),
                TextColumn::make('commission_state')->label('Commission State')->badge()->formatStateUsing(fn (AffiliateCommissionState $state): string => $state->label()),
                TextColumn::make('last_synced_at')->label('Last Synchronized')->dateTime('d M Y H:i'),
            ])
            ->recordActions([ViewAction::make()->url(fn (AffiliateBooking $record): string => AffiliateBookingResource::getUrl('view', ['record' => $record]))]);
    }
}

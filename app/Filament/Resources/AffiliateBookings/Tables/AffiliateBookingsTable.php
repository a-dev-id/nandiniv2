<?php

namespace App\Filament\Resources\AffiliateBookings\Tables;

use App\Enums\AffiliateBookingStatus;
use App\Enums\AffiliateCommissionState;
use App\Models\AffiliateBooking;
use App\Services\Affiliate\Booking\AffiliateMoneyFormatter;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AffiliateBookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('last_synced_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with('rooms'))
            ->columns([
                TextColumn::make('affiliate.name')->label('Affiliate')->searchable()->sortable(),
                TextColumn::make('affiliate_code_snapshot')->label('Affiliate Code')->searchable()->copyable(),
                TextColumn::make('external_booking_reference')->label('External Reference')->searchable()->placeholder('Unavailable'),
                TextColumn::make('room_types')->label('Room Type(s)')->state(fn (AffiliateBooking $record): string => $record->roomTypesLabel())->wrap(),
                TextColumn::make('check_in_date')->label('Check-in')->date('d M Y')->sortable(),
                TextColumn::make('check_out_date')->label('Check-out')->date('d M Y')->sortable(),
                TextColumn::make('stay_nights')->label('Nights')->numeric()->sortable(),
                TextColumn::make('booking_status')->label('Status')->badge()->formatStateUsing(fn (AffiliateBookingStatus $state): string => $state->label()),
                TextColumn::make('room_revenue_amount')->label('Room Revenue')->state(fn (AffiliateBooking $record): string => $record->room_revenue_amount === null ? 'Unavailable' : app(AffiliateMoneyFormatter::class)->format($record->room_revenue_amount, $record->currency)),
                TextColumn::make('currency')->placeholder('Unavailable'),
                TextColumn::make('commission_rate_snapshot')->label('Rate')->suffix('%'),
                TextColumn::make('estimated_commission_amount')->label('Estimated Commission')->state(fn (AffiliateBooking $record): string => $record->estimated_commission_amount === null ? 'Pending calculation' : app(AffiliateMoneyFormatter::class)->format($record->estimated_commission_amount, $record->currency)),
                TextColumn::make('commission_state')->label('Commission State')->badge()->formatStateUsing(fn (AffiliateCommissionState $state, AffiliateBooking $record): string => $record->commissionStatusLabel()),
                TextColumn::make('source_system')->label('Source')->sortable(),
                TextColumn::make('last_synced_at')->label('Last Synchronized')->dateTime('d M Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('affiliate_id')->label('Affiliate')->relationship('affiliate', 'name')->searchable()->preload(),
                SelectFilter::make('affiliate_code_snapshot')->label('Affiliate Code')->options(fn (): array => AffiliateBooking::query()->distinct()->orderBy('affiliate_code_snapshot')->pluck('affiliate_code_snapshot', 'affiliate_code_snapshot')->all())->searchable(),
                SelectFilter::make('booking_status')->label('Booking Status')->options(collect(AffiliateBookingStatus::cases())->mapWithKeys(fn (AffiliateBookingStatus $status): array => [$status->value => $status->label()])->all()),
                SelectFilter::make('commission_state')->label('Commission State')->options(collect(AffiliateCommissionState::cases())->mapWithKeys(fn (AffiliateCommissionState $state): array => [$state->value => $state->label()])->all()),
                self::dateFilter('check_in_date', 'Check-in'),
                self::dateFilter('check_out_date', 'Check-out'),
                SelectFilter::make('source_system')->label('Source System')->options(fn (): array => AffiliateBooking::query()->whereNotNull('source_system')->distinct()->orderBy('source_system')->pluck('source_system', 'source_system')->all()),
                SelectFilter::make('currency')->options(fn (): array => AffiliateBooking::query()->whereNotNull('currency')->distinct()->orderBy('currency')->pluck('currency', 'currency')->all()),
            ])
            ->recordActions([ViewAction::make()]);
    }

    private static function dateFilter(string $field, string $label): Filter
    {
        return Filter::make($field)
            ->schema([
                DatePicker::make('from')->label($label.' From'),
                DatePicker::make('until')->label($label.' Until'),
            ])
            ->query(fn (Builder $query, array $data): Builder => $query
                ->when($data['from'] ?? null, fn (Builder $query, $date): Builder => $query->whereDate($field, '>=', $date))
                ->when($data['until'] ?? null, fn (Builder $query, $date): Builder => $query->whereDate($field, '<=', $date)));
    }
}

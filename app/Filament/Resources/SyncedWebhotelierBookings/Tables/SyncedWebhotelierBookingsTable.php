<?php

namespace App\Filament\Resources\SyncedWebhotelierBookings\Tables;

use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SyncedWebhotelierBookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('last_synced_at', 'desc')
            ->columns([
                TextColumn::make('booking_number')->label('Booking Number')->searchable()->weight('semibold'),
                TextColumn::make('guest_name')->label('Guest Name')->searchable()->placeholder('-'),
                TextColumn::make('email')->searchable(),
                TextColumn::make('phone')->searchable()->placeholder('-'),
                TextColumn::make('check_in')->label('Check-in')->date('d M Y')->sortable()->placeholder('-'),
                TextColumn::make('check_out')->label('Check-out')->date('d M Y')->sortable()->placeholder('-'),
                TextColumn::make('room_name')->label('Room Name')->searchable()->placeholder('-'),
                TextColumn::make('room_type')->label('Room Type')->placeholder('-'),
                TextColumn::make('status')->badge()->searchable()->placeholder('-'),
                TextColumn::make('booking_total')
                    ->label('Booking Total')
                    ->formatStateUsing(fn($state, $record): string => $state === null ? '-' : trim(($record->currency ?: '') . ' ' . number_format((float) $state, 2)))
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('currency')->placeholder('-'),
                TextColumn::make('member.full_name')->label('Member')->placeholder('-'),
                TextColumn::make('remote_updated_at')->dateTime('d M Y H:i')->sortable()->placeholder('-'),
                TextColumn::make('last_synced_at')->dateTime('d M Y H:i')->sortable()->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(fn(): array => \App\Models\SyncedWebhotelierBooking::query()
                        ->whereNotNull('status')
                        ->distinct()
                        ->orderBy('status')
                        ->pluck('status', 'status')
                        ->all()),

                Filter::make('check_in')
                    ->schema([
                        DatePicker::make('from')->label('Check-in From'),
                        DatePicker::make('until')->label('Check-in Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn(Builder $query, $date): Builder => $query->whereDate('check_in', '>=', $date))
                            ->when($data['until'] ?? null, fn(Builder $query, $date): Builder => $query->whereDate('check_in', '<=', $date));
                    }),
            ]);
    }
}

<?php

namespace App\Filament\Resources\BookingSyncLogs\Tables;

use App\Models\BookingSyncLog;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BookingSyncLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        BookingSyncLog::STATUS_SUCCESS => 'success',
                        BookingSyncLog::STATUS_FAILED => 'danger',
                        BookingSyncLog::STATUS_RUNNING => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(?string $state): string => $state ? ucfirst($state) : '-')
                    ->sortable(),

                TextColumn::make('started_at')->dateTime('d M Y H:i')->sortable(),
                TextColumn::make('finished_at')->dateTime('d M Y H:i')->sortable()->placeholder('-'),
                TextColumn::make('bookings_received')->numeric()->sortable(),
                TextColumn::make('bookings_created')->numeric()->sortable(),
                TextColumn::make('bookings_updated')->numeric()->sortable(),
                TextColumn::make('members_created')->numeric()->sortable(),
                TextColumn::make('members_updated')->numeric()->sortable(),
                TextColumn::make('message')
                    ->searchable()
                    ->limit(80)
                    ->wrap()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->dateTime('d M Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        BookingSyncLog::STATUS_RUNNING => 'Running',
                        BookingSyncLog::STATUS_SUCCESS => 'Success',
                        BookingSyncLog::STATUS_FAILED => 'Failed',
                    ]),

                Filter::make('started_at')
                    ->schema([
                        DatePicker::make('from')->label('Started From'),
                        DatePicker::make('until')->label('Started Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn(Builder $query, $date): Builder => $query->whereDate('started_at', '>=', $date))
                            ->when($data['until'] ?? null, fn(Builder $query, $date): Builder => $query->whereDate('started_at', '<=', $date));
                    }),
            ]);
    }
}

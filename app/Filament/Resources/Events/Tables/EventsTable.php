<?php

namespace App\Filament\Resources\Events\Tables;

use App\Enums\EventStatus;
use App\Enums\EventType;
use App\Models\Event;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('event_start_at', 'desc')
            ->columns([
                ImageColumn::make('image')
                    ->disk('public')
                    ->square(),

                TextColumn::make('title')
                    ->label('Event')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn (Event $record): ?string => $record->subtitle),

                TextColumn::make('event_type')
                    ->label('Type')
                    ->badge()
                    ->getStateUsing(fn (Event $record): string => $record->event_type->label())
                    ->color(fn (string $state): string => match ($state) {
                        'Weekly' => 'info',
                        'Monthly' => 'warning',
                        'Yearly' => 'success',
                        default => 'gray',
                    }),

                ToggleColumn::make('status')
                    ->label('Active')
                    ->getStateUsing(fn (Event $record): bool => $record->status === EventStatus::Published)
                    ->updateStateUsing(function (Event $record, bool $state): bool {
                        $record->update([
                            'status' => $state
                                ? EventStatus::Published->value
                                : EventStatus::Draft->value,
                        ]);

                        return $state;
                    })
                    ->onColor('warning')
                    ->offColor('gray'),

                TextColumn::make('schedule_text')
                    ->label('Schedule')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('event_start_at')
                    ->label('Starts')
                    ->dateTime('d M Y H:i')
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('event_end_at')
                    ->label('Ends')
                    ->dateTime('d M Y H:i')
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(EventStatus::options()),

                SelectFilter::make('event_type')
                    ->label('Event Type')
                    ->options(EventType::options()),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

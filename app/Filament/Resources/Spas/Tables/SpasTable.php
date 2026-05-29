<?php

namespace App\Filament\Resources\Spas\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SpasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->reorderRecordsTriggerAction(
                fn(Action $action, bool $isReordering) => $action
                    ->button()
                    ->label($isReordering ? 'Done sorting' : 'Sort spa packages')
            )
            ->columns([
                ImageColumn::make('card_image')
                    ->label('Image')
                    ->disk('public')
                    ->square(),

                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->limit(45),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('subtitle')
                    ->label('Subtitle')
                    ->searchable()
                    ->limit(45)
                    ->toggleable(),

                TextColumn::make('valid_start_date')
                    ->label('Start Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('valid_end_date')
                    ->label('End Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('button_label')
                    ->label('Button')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('booking_checkin_date')
                    ->label('Check-in')
                    ->date()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('booking_nights')
                    ->label('Nights')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('booking_rooms')
                    ->label('Rooms')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('booking_adults')
                    ->label('Adults')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('booking_rate_code')
                    ->label('Rate Code')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('booking_bkcode')
                    ->label('BK Code')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Order')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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

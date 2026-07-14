<?php

namespace App\Filament\Resources\GuestReviews\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class GuestReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->reorderRecordsTriggerAction(
                fn (Action $action, bool $isReordering) => $action
                    ->button()
                    ->label($isReordering ? 'Done sorting' : 'Sort reviews')
            )
            ->columns([
                TextColumn::make('reviewer_name')
                    ->label('Guest Name')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('review_text')
                    ->label('Review')
                    ->searchable()
                    ->limit(70)
                    ->wrap(),

                TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(fn (int $state): string => $state.' / 5')
                    ->sortable(),

                TextColumn::make('reviewed_at')
                    ->label('Review Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('source')
                    ->label('Source')
                    ->searchable()
                    ->toggleable(),

                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Order')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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

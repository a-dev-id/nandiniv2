<?php

namespace App\Filament\Resources\Offers\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class OffersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->reorderRecordsTriggerAction(
                fn(Action $action, bool $isReordering) => $action
                    ->button()
                    ->label($isReordering ? 'Done sorting' : 'Sort offers')
            )
            ->columns([
                ImageColumn::make('preview_image')
                    ->label('Image')
                    ->disk('public')
                    ->square()
                    ->getStateUsing(fn($record): ?string => $record->card_image
                        ?: $record->hero_image
                        ?: $record->hero_mobile_image),

                TextColumn::make('title')
                    ->label('Offer')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn($record): ?string => $record->subtitle ?: $record->slug),

                TextColumn::make('publish_status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(function ($record): string {
                        if ($record->valid_start_date && $record->valid_start_date->isFuture()) {
                            return 'Scheduled';
                        }

                        if ($record->valid_end_date && $record->valid_end_date->isPast()) {
                            return 'Expired';
                        }

                        if (! $record->is_active) {
                            return 'Inactive';
                        }

                        return 'Live';
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'Live' => 'success',
                        'Scheduled' => 'info',
                        'Expired' => 'danger',
                        'Inactive' => 'gray',
                        default => 'gray',
                    }),

                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->sortable(),

                TextColumn::make('valid_start_date')
                    ->label('Start')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('valid_end_date')
                    ->label('End')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('button_label')
                    ->label('Button')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('button_url')
                    ->label('Button URL')
                    ->searchable()
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('meta_title')
                    ->label('SEO Title')
                    ->searchable()
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),

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
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->placeholder('All offers')
                    ->trueLabel('Active offers')
                    ->falseLabel('Inactive offers'),

                TernaryFilter::make('is_featured')
                    ->label('Featured Status')
                    ->placeholder('All offers')
                    ->trueLabel('Featured offers')
                    ->falseLabel('Not featured'),
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

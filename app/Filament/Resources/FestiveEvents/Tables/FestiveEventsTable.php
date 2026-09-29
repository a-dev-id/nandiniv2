<?php

namespace App\Filament\Resources\FestiveEvents\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class FestiveEventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->reorderRecordsTriggerAction(
                fn (Action $action, bool $isReordering) => $action
                    ->button()
                    ->label($isReordering ? 'Done sorting' : 'Sort detail pages')
            )
            ->columns([
                TextColumn::make('title')->label('Detail Page')->searchable()->sortable()->weight('semibold')
                    ->description(fn ($record): string => '/festive-season/'.$record->slug),
                TextColumn::make('hero_eyebrow')->label('Date')->searchable(),
                ToggleColumn::make('is_active')->label('Active')->sortable(),
                TextColumn::make('sort_order')->label('Order')->numeric()->sortable(),
                TextColumn::make('updated_at')->label('Updated')->since()->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label('Active Status'),
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

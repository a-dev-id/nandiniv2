<?php

namespace App\Filament\Resources\Accommodations\RelationManagers;

use App\Models\AccommodationFeature;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FeaturesRelationManager extends RelationManager
{
    protected static string $relationship = 'features';

    protected static ?string $recordTitleAttribute = 'label';

    protected static ?string $title = 'Attached Features';

    protected static ?string $modelLabel = 'Feature';

    protected static ?string $pluralModelLabel = 'Attached Features';

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('accommodation_features.sort_order')
            ->columns([
                ImageColumn::make('icon_image')
                    ->label('Icon')
                    ->disk('public')
                    ->square()
                    ->size(42),

                TextColumn::make('label')
                    ->label('Feature')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Sort')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Attach Feature')
                    ->icon(Heroicon::OutlinedPlus)
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['label'])
                    ->recordTitle(fn(Model $record): string => $record->label)
                    ->recordSelectOptionsQuery(function (Builder $query): Builder {
                        return $query
                            ->where('is_active', true)
                            ->orderBy('sort_order')
                            ->orderBy('label');
                    }),
            ])
            ->recordActions([
                DetachAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}

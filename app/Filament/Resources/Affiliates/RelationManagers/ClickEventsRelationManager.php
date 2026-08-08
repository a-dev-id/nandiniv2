<?php

namespace App\Filament\Resources\Affiliates\RelationManagers;

use App\Models\Permission;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ClickEventsRelationManager extends RelationManager
{
    protected static bool $isLazy = false;

    protected static string $relationship = 'clickEvents';

    protected static ?string $title = 'Recent click events';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth('web')->user()?->hasPermissionTo(Permission::AFFILIATE_CLICK_VIEW) === true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereIn(
                'affiliate_click_events.id',
                $this->ownerRecord->clickEvents()->latest('clicked_at')->limit(50)->pluck('id'),
            ))
            ->defaultSort('clicked_at', 'desc')
            ->paginationPageOptions([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->columns([
                TextColumn::make('clicked_at')->label('Clicked at')->dateTime('d M Y H:i:s')->sortable(),
                TextColumn::make('country_name')->label('Country')->placeholder('Unknown'),
                TextColumn::make('device_type')->label('Device')->formatStateUsing(fn (string $state): string => ucfirst($state))->badge(),
                TextColumn::make('referrer_domain')->label('Referrer domain')->placeholder('Direct')->wrap(),
                IconColumn::make('is_unique')->label('Unique')->boolean(),
                IconColumn::make('is_bot')->label('Bot')->boolean(),
                TextColumn::make('bot_name')->label('Bot name')->placeholder('-'),
            ]);
    }
}

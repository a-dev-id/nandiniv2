<?php

namespace App\Filament\Resources\DiningHomepage;

use App\Filament\Resources\DiningHomepage\Pages\EditDiningHomepage;
use App\Filament\Resources\DiningHomepage\Pages\ListDiningHomepage;
use App\Filament\Resources\Pages\RelationManagers\SectionsRelationManager;
use App\Filament\Resources\Pages\Schemas\PageForm;
use App\Models\Page;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class DiningHomepageResource extends Resource
{
    protected static ?string $slug = 'dining-homepage';

    protected static ?string $model = Page::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static string|UnitEnum|null $navigationGroup = 'Dining Landing Page';

    protected static ?string $navigationLabel = 'Dining Homepage';

    protected static ?string $modelLabel = 'Dining Homepage';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereKey(4);
    }

    public static function form(Schema $schema): Schema
    {
        return PageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('page_name')->label('Page')->weight('semibold'),
            TextColumn::make('title'),
            IconColumn::make('is_active')->label('Published')->boolean(),
        ])->recordActions([EditAction::make()]);
    }

    public static function getRelations(): array
    {
        return [SectionsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDiningHomepage::route('/'),
            'edit' => EditDiningHomepage::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources\DiningReviews;

use App\Filament\Resources\DiningReviews\Pages\CreateDiningReview;
use App\Filament\Resources\DiningReviews\Pages\EditDiningReview;
use App\Filament\Resources\DiningReviews\Pages\ListDiningReviews;
use App\Filament\Resources\GuestReviews\Schemas\GuestReviewForm;
use App\Filament\Resources\GuestReviews\Tables\GuestReviewsTable;
use App\Models\GuestReview;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class DiningReviewResource extends Resource
{
    protected static ?string $model = GuestReview::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static string|UnitEnum|null $navigationGroup = 'Dining Landing Page';

    protected static ?string $navigationLabel = 'Dining Reviews';

    protected static ?int $navigationSort = 100;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->forDining();
    }

    public static function form(Schema $schema): Schema
    {
        return GuestReviewForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GuestReviewsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDiningReviews::route('/'),
            'create' => CreateDiningReview::route('/create'),
            'edit' => EditDiningReview::route('/{record}/edit'),
        ];
    }
}

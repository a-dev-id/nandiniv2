<?php

namespace App\Filament\Resources\GuestReviews;

use App\Filament\Resources\GuestReviews\Pages\CreateGuestReview;
use App\Filament\Resources\GuestReviews\Pages\EditGuestReview;
use App\Filament\Resources\GuestReviews\Pages\ListGuestReviews;
use App\Filament\Resources\GuestReviews\Schemas\GuestReviewForm;
use App\Filament\Resources\GuestReviews\Tables\GuestReviewsTable;
use App\Models\GuestReview;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class GuestReviewResource extends Resource
{
    protected static ?string $model = GuestReview::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static string|UnitEnum|null $navigationGroup = 'Website Content';

    protected static ?string $navigationLabel = 'Guest Reviews';

    protected static ?string $modelLabel = 'Guest Review';

    protected static ?string $pluralModelLabel = 'Guest Reviews';

    protected static ?int $navigationSort = 90;

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
            'index' => ListGuestReviews::route('/'),
            'create' => CreateGuestReview::route('/create'),
            'edit' => EditGuestReview::route('/{record}/edit'),
        ];
    }
}

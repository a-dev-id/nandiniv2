<?php

namespace App\Filament\Resources\AffiliateExchangeRates;

use App\Enums\AffiliatePreferredCurrency;
use App\Filament\Resources\AffiliateExchangeRates\Pages\CreateAffiliateExchangeRate;
use App\Filament\Resources\AffiliateExchangeRates\Pages\EditAffiliateExchangeRate;
use App\Filament\Resources\AffiliateExchangeRates\Pages\ListAffiliateExchangeRates;
use App\Models\AffiliateExchangeRate;
use App\Models\Permission;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class AffiliateExchangeRateResource extends Resource
{
    protected static ?string $model = AffiliateExchangeRate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static ?string $navigationLabel = 'Exchange Rates';

    protected static string|UnitEnum|null $navigationGroup = 'Affiliate';

    protected static ?int $navigationSort = 45;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermissionTo(Permission::AFFILIATE_PAYOUT_MANAGE) === true;
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit($record): bool
    {
        return static::canViewAny();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Hidden::make('base_currency')->default('IDR'),
            Select::make('quote_currency')
                ->label('Payout Currency')
                ->options(collect(AffiliatePreferredCurrency::cases())->reject(fn (AffiliatePreferredCurrency $currency): bool => $currency === AffiliatePreferredCurrency::IDR)->mapWithKeys(fn (AffiliatePreferredCurrency $currency): array => [$currency->value => $currency->value])->all())
                ->required()
                ->unique(ignoreRecord: true),
            TextInput::make('base_units_per_quote')
                ->label('IDR per 1 payout currency unit')
                ->helperText('Example: enter 16478.10 when 1 USD equals IDR 16,478.10.')
                ->numeric()
                ->minValue(0.000001)
                ->required(),
            DateTimePicker::make('effective_at')->label('Effective From')->default(now()),
            Toggle::make('is_active')->label('Active')->default(true)->onColor('warning'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('quote_currency')->label('Payout Currency')->badge()->sortable(),
            TextColumn::make('base_units_per_quote')->label('IDR per 1 unit')->numeric(decimalPlaces: 6)->sortable(),
            IconColumn::make('is_active')->label('Active')->boolean(),
            TextColumn::make('effective_at')->label('Effective From')->dateTime('d M Y H:i')->placeholder('Immediately'),
            TextColumn::make('updater.name')->label('Updated By')->placeholder('System'),
            TextColumn::make('updated_at')->label('Updated')->dateTime('d M Y H:i')->sortable(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAffiliateExchangeRates::route('/'),
            'create' => CreateAffiliateExchangeRate::route('/create'),
            'edit' => EditAffiliateExchangeRate::route('/{record}/edit'),
        ];
    }
}

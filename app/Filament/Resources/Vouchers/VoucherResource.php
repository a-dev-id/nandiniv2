<?php

namespace App\Filament\Resources\Vouchers;

use App\Filament\Resources\Vouchers\Pages\CreateVoucher;
use App\Filament\Resources\Vouchers\Pages\EditVoucher;
use App\Filament\Resources\Vouchers\Pages\ListVouchers;
use App\Filament\Resources\Vouchers\Schemas\VoucherForm;
use App\Models\Voucher;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class VoucherResource extends Resource
{
    protected static ?string $model = Voucher::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGift;

    protected static string|UnitEnum|null $navigationGroup = 'Vouchers';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return VoucherForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->searchable()->sortable()->description(fn($record) => $record->sku),
            TextColumn::make('category.name')->label('Category')->sortable(),
            TextColumn::make('voucher_type')->badge(),
            TextColumn::make('selling_price')
                ->label('Price')
                ->formatStateUsing(fn ($state, Voucher $record): string => app(\App\Services\Voucher\MoneyFormatter::class)->format($record->discounted_price, $record->currency) . app(\App\Services\Voucher\MoneyFormatter::class)->priceTypeSuffix($record->price_type))
                ->description(fn (Voucher $record): ?string => $record->has_discount ? $record->discount_percentage . '% off ' . app(\App\Services\Voucher\MoneyFormatter::class)->format($record->selling_price, $record->currency) : null)
                ->sortable(),
            IconColumn::make('is_featured')->boolean(),
            ToggleColumn::make('is_active'),
        ])->recordActions([EditAction::make()])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVouchers::route('/'),
            'create' => CreateVoucher::route('/create'),
            'edit' => EditVoucher::route('/{record}/edit'),
        ];
    }
}

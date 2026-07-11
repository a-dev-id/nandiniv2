<?php

namespace App\Filament\Resources\Vouchers;

use App\Filament\Resources\Vouchers\Pages\CreateVoucher;
use App\Filament\Resources\Vouchers\Pages\EditVoucher;
use App\Filament\Resources\Vouchers\Pages\ListVouchers;
use App\Models\Voucher;
use App\Models\VoucherCategory;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class VoucherResource extends Resource
{
    protected static ?string $model = Voucher::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGift;

    protected static string|UnitEnum|null $navigationGroup = 'Vouchers';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(191)->live(onBlur: true)->afterStateUpdated(fn($state, $set) => $set('slug', Str::slug($state ?? ''))),
            TextInput::make('slug')->required()->unique(ignoreRecord: true)->maxLength(191),
            Select::make('voucher_category_id')->label('Category')->options(fn() => VoucherCategory::query()->ordered()->pluck('name', 'id'))->searchable(),
            TextInput::make('sku')->unique(ignoreRecord: true)->maxLength(191),
            Select::make('voucher_type')->options(array_combine(Voucher::TYPES, Voucher::TYPES))->required()->default('custom'),
            TextInput::make('selling_price')->numeric()->required()->minValue(0),
            TextInput::make('face_value')->numeric()->minValue(0),
            TextInput::make('currency')->required()->default('IDR')->maxLength(3),
            Select::make('price_type')->options([
                'plus_plus' => '++',
                'net' => 'Nett',
                'inclusive' => 'Inclusive',
            ])->nullable(),
            Select::make('unit_type')->options([
                'per_person' => 'Per Person',
                'per_couple' => 'Per Couple',
                'per_booking' => 'Per Booking',
            ])->nullable(),
            Select::make('validity_type')->options(array_combine(Voucher::VALIDITY_TYPES, Voucher::VALIDITY_TYPES))->required()->default('days_after_issue'),
            TextInput::make('validity_days')->numeric()->minValue(1),
            DatePicker::make('fixed_valid_from')->native(false),
            DatePicker::make('fixed_valid_until')->native(false),
            TextInput::make('minimum_quantity')->numeric()->default(1)->minValue(1),
            TextInput::make('maximum_quantity')->numeric()->minValue(1),
            TextInput::make('purchase_limit_per_order')->numeric()->minValue(1),
            Textarea::make('excerpt')->columnSpanFull(),
            RichEditor::make('description')->columnSpanFull(),
            RichEditor::make('inclusions')->columnSpanFull(),
            RichEditor::make('terms_conditions')->columnSpanFull(),
            TextInput::make('image')->maxLength(255),
            TextInput::make('card_image')->maxLength(255),
            TextInput::make('image_alt')->maxLength(255),
            TextInput::make('meta_title')->maxLength(255),
            Textarea::make('meta_description')->columnSpanFull(),
            TextInput::make('sort_order')->numeric()->default(0),
            Toggle::make('allow_partial_redemption')->default(false),
            Toggle::make('is_featured')->default(false),
            Toggle::make('is_active')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->searchable()->sortable()->description(fn($record) => $record->sku),
            TextColumn::make('category.name')->label('Category')->sortable(),
            TextColumn::make('voucher_type')->badge(),
            TextColumn::make('selling_price')->formatStateUsing(fn($state, Voucher $record): string => app(\App\Services\Voucher\MoneyFormatter::class)->format($state, $record->currency) . app(\App\Services\Voucher\MoneyFormatter::class)->priceTypeSuffix($record->price_type))->sortable(),
            IconColumn::make('is_featured')->boolean(),
            IconColumn::make('is_active')->boolean(),
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

<?php

namespace App\Filament\Resources\VoucherCategories;

use App\Filament\Resources\VoucherCategories\Pages\CreateVoucherCategory;
use App\Filament\Resources\VoucherCategories\Pages\EditVoucherCategory;
use App\Filament\Resources\VoucherCategories\Pages\ListVoucherCategories;
use App\Models\VoucherCategory;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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

class VoucherCategoryResource extends Resource
{
    protected static ?string $model = VoucherCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolder;

    protected static string|UnitEnum|null $navigationGroup = 'Vouchers';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(191)->live(onBlur: true)->afterStateUpdated(fn($state, $set) => $set('slug', Str::slug($state ?? ''))),
            TextInput::make('slug')->required()->unique(ignoreRecord: true)->maxLength(191),
            Textarea::make('description')->columnSpanFull(),
            TextInput::make('image')->maxLength(255),
            TextInput::make('image_alt')->maxLength(255),
            TextInput::make('sort_order')->numeric()->default(0),
            Toggle::make('is_active')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('slug')->searchable(),
            IconColumn::make('is_active')->boolean(),
            TextColumn::make('sort_order')->sortable(),
        ])->recordActions([EditAction::make()])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVoucherCategories::route('/'),
            'create' => CreateVoucherCategory::route('/create'),
            'edit' => EditVoucherCategory::route('/{record}/edit'),
        ];
    }
}

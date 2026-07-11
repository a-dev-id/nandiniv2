<?php

namespace App\Filament\Resources\Vouchers\Schemas;

use App\Models\Voucher;
use App\Models\VoucherCategory;
use App\Support\FilamentWebpUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class VoucherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                Section::make('Voucher Content')
                    ->columnSpan(['default' => 12, 'lg' => 8])
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(191)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                        TextInput::make('slug')->required()->unique(ignoreRecord: true)->maxLength(191),
                        Select::make('voucher_category_id')
                            ->label('Category')
                            ->options(fn () => VoucherCategory::query()->ordered()->pluck('name', 'id'))
                            ->searchable()
                            ->preload(),
                        TextInput::make('sku')->label('SKU')->unique(ignoreRecord: true)->maxLength(191),
                        Textarea::make('excerpt')->rows(3)->columnSpanFull(),
                        RichEditor::make('description')->toolbarButtons(self::editorToolbar())->columnSpanFull(),
                        RichEditor::make('terms_conditions')->label('Terms and Conditions')->toolbarButtons(self::editorToolbar())->columnSpanFull(),

                        Section::make('Pricing')
                            ->columnSpanFull()
                            ->columns(2)
                            ->schema([
                                Select::make('voucher_type')->options(array_combine(Voucher::TYPES, Voucher::TYPES))->required()->default('custom'),
                                TextInput::make('selling_price')->label('Original Price')->numeric()->required()->minValue(0)->prefix('IDR'),
                                TextInput::make('discount_percentage')
                                    ->label('Discount')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(99)
                                    ->default(0)
                                    ->suffix('%')
                                    ->helperText('The storefront and checkout automatically use the discounted price.'),
                                TextInput::make('currency')->required()->default('IDR')->maxLength(3),
                                Select::make('price_type')->options(['plus_plus' => '++', 'net' => 'Nett', 'inclusive' => 'Inclusive'])->nullable(),
                                Select::make('unit_type')->options(['per_person' => 'Per Person', 'per_couple' => 'Per Couple', 'per_booking' => 'Per Booking'])->nullable(),
                            ]),

                        Section::make('Validity and Purchase Limits')
                            ->columnSpanFull()
                            ->columns(2)
                            ->schema([
                                TextInput::make('validity_days')->label('Validity Days')->numeric()->minValue(1)->default(365),
                                TextInput::make('minimum_quantity')->numeric()->default(1)->minValue(1),
                                TextInput::make('maximum_quantity')->numeric()->minValue(1),
                                TextInput::make('purchase_limit_per_order')->numeric()->minValue(1),
                            ]),

                        Section::make('SEO')
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('meta_title')->label('Meta Title')->maxLength(70),
                                Textarea::make('meta_description')->label('Meta Description')->rows(3)->maxLength(180),
                            ]),

                        Section::make('Settings')
                            ->columnSpanFull()
                            ->columns(3)
                            ->schema([
                                Toggle::make('is_featured')->default(false),
                                Toggle::make('is_active')->default(true),
                                Hidden::make('validity_type')->default('days_after_issue'),
                                Hidden::make('allow_partial_redemption')->default(false),
                                Hidden::make('sort_order')->default(0),
                            ]),
                    ]),

                Grid::make()
                    ->columnSpan(['default' => 12, 'lg' => 4])
                    ->schema([
                        Section::make('Voucher Images')
                            ->columnSpanFull()
                            ->schema([
                                self::imageUpload('image', 'Main Image', 'vouchers/main', 1200, 900),
                                self::imageUpload('card_image', 'Card Image', 'vouchers/cards', 1200, 900),
                                TextInput::make('image_alt')->label('Image Alt Text')->maxLength(255),
                            ]),
                    ]),
            ]);
    }

    private static function imageUpload(string $field, string $label, string $directory, int $width, int $height): FileUpload
    {
        return FileUpload::make($field)
            ->label($label)
            ->disk('public')
            ->directory($directory)
            ->visibility('public')
            ->image()
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->imagePreviewHeight('180')
            ->panelAspectRatio('4:3')
            ->panelLayout('integrated')
            ->openable()
            ->downloadable()
            ->saveUploadedFileUsing(fn (TemporaryUploadedFile $file): string => FilamentWebpUpload::store(
                file: $file,
                directory: $directory,
                targetWidth: $width,
                targetHeight: $height,
            ));
    }

    private static function editorToolbar(): array
    {
        return [
            ['bold', 'italic', 'underline', 'strike', 'link'],
            ['h2', 'h3'],
            ['bulletList', 'orderedList'],
            ['blockquote'],
            ['undo', 'redo'],
        ];
    }
}

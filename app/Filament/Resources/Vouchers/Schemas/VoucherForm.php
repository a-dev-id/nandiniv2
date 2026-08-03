<?php

namespace App\Filament\Resources\Vouchers\Schemas;

use App\Models\Voucher;
use App\Models\VoucherCategory;
use App\Support\FilamentWebpUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
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
                                TextInput::make('selling_price')
                                    ->label('Base / Original Price')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->prefix('IDR')
                                    ->helperText('This is the price of the standard room option.'),
                                TextInput::make('discount_percentage')
                                    ->label('Discount')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(99)
                                    ->default(0)
                                    ->dehydrateStateUsing(fn (mixed $state): int => max(0, (int) ($state ?? 0)))
                                    ->suffix('%')
                                    ->helperText('Enter 0 or leave empty to remove the discount.'),
                                TextInput::make('currency')->required()->default('IDR')->maxLength(3),
                                Select::make('price_type')->options(['plus_plus' => '++', 'net' => 'Net', 'inclusive' => 'Inclusive'])->nullable(),
                                Select::make('unit_type')->options(['per_person' => 'Per Person', 'per_couple' => 'Per Couple', 'per_booking' => 'Per Booking'])->nullable(),
                                Repeater::make('price_options')
                                    ->label('Room Price Options')
                                    ->columnSpanFull()
                                    ->columns(2)
                                    ->addActionLabel('Add room option')
                                    ->reorderable()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): string => $state['label'] ?? 'Room Option')
                                    ->afterStateHydrated(function (Repeater $component, mixed $state): void {
                                        self::hydrateRepeaterItems(
                                            $component,
                                            $state,
                                            fn (mixed $item): array => array_merge(
                                                ['key' => (string) Str::uuid(), 'label' => null, 'additional_price' => 0],
                                                is_array($item) ? $item : [],
                                            ),
                                        );
                                    })
                                    ->schema([
                                        Hidden::make('key')->default(fn (): string => (string) Str::uuid()),
                                        TextInput::make('label')
                                            ->label('Room Name')
                                            ->required()
                                            ->maxLength(191)
                                            ->live(onBlur: true)
                                            ->placeholder('Jungle View Villa'),
                                        TextInput::make('additional_price')
                                            ->label('Additional Price')
                                            ->numeric()
                                            ->required()
                                            ->default(0)
                                            ->minValue(0)
                                            ->prefix('IDR')
                                            ->helperText('Use 0 for the base room; enter only the upgrade amount for other rooms.'),
                                    ])
                                    ->helperText('Optional. When added, customers choose one room from a dropdown before adding the voucher to their cart.'),
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
                                Repeater::make('gallery_images')
                                    ->label('Gallery Images')
                                    ->addActionLabel('Add image')
                                    ->reorderable()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): string => $state['image_alt'] ?? 'Gallery Image')
                                    ->afterStateHydrated(function (Repeater $component, mixed $state): void {
                                        self::hydrateRepeaterItems(
                                            $component,
                                            $state,
                                            fn (mixed $item): array => is_array($item)
                                                ? $item
                                                : ['image' => $item, 'image_alt' => null],
                                        );
                                    })
                                    ->schema([
                                        self::imageUpload('image', 'Image', 'vouchers/gallery', 1200, 900)
                                            ->required(),
                                        TextInput::make('image_alt')
                                            ->label('Image Alt Text')
                                            ->maxLength(255)
                                            ->helperText('Briefly describe this image for accessibility.'),
                                    ])
                                    ->helperText('Optional. Add and reorder images for the voucher detail slider.'),
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

    private static function hydrateRepeaterItems(Repeater $component, mixed $state, callable $normalize): void
    {
        $items = [];

        foreach (is_array($state) ? $state : [] as $item) {
            $itemKey = $component->generateUuid();
            $item = $normalize($item);

            if ($itemKey === null) {
                $items[] = $item;
            } else {
                $items[$itemKey] = $item;
            }
        }

        $component->rawState($items);
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

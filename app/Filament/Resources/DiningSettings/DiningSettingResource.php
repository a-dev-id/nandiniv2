<?php

namespace App\Filament\Resources\DiningSettings;

use App\Filament\Resources\DiningSettings\Pages\EditDiningSetting;
use App\Filament\Resources\DiningSettings\Pages\ListDiningSettings;
use App\Models\DiningSetting;
use App\Support\FilamentWebpUpload;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use UnitEnum;

class DiningSettingResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = DiningSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Dining Subdomain';

    protected static ?string $navigationLabel = 'Dining Settings';

    protected static ?string $modelLabel = 'Dining Settings';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Section::make('Reservations & Menus')->columns(2)->schema([
                TextInput::make('reservation_whatsapp')->tel()->maxLength(50),
                TextInput::make('reservation_email')->email()->maxLength(255),
                TextInput::make('reservation_url')->url()->columnSpanFull(),
                TextInput::make('food_menu_url')->url(),
                TextInput::make('beverage_menu_url')->url(),
            ])->columnSpanFull(),
            Section::make('Default Practical Information')->columns(2)->schema([
                TextInput::make('opening_hours')->maxLength(255),
                TextInput::make('cuisine')->maxLength(255),
                TextInput::make('location')->maxLength(255)->columnSpanFull(),
                TextInput::make('dress_code')->maxLength(255),
                Textarea::make('outside_guest_information')->rows(3)->columnSpanFull(),
            ])->columnSpanFull(),
            Section::make('Our Philosophy')
                ->description('Content for the Our Philosophy / Chef Plating section on the Dining landing page.')
                ->columns(2)
                ->schema([
                    Section::make('Content')->schema([
                        TextInput::make('philosophy_eyebrow')
                            ->label('Eyebrow / Label')
                            ->maxLength(255),
                        Textarea::make('philosophy_heading')
                            ->label('Main Heading')
                            ->rows(3)
                            ->helperText('Line breaks are preserved on the website.'),
                        Textarea::make('philosophy_description')
                            ->label('Description')
                            ->rows(5),
                        Textarea::make('philosophy_accent_text')
                            ->label('Handwritten / Accent Text')
                            ->rows(4)
                            ->helperText('Line breaks are preserved on the website.'),
                    ]),
                    Section::make('Media')->schema([
                        FileUpload::make('philosophy_image')
                            ->label('Main Image')
                            ->disk('public')
                            ->directory('dining/philosophy')
                            ->visibility('public')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->imagePreviewHeight('220')
                            ->panelAspectRatio('4:3')
                            ->panelLayout('integrated')
                            ->openable()
                            ->downloadable()
                            ->saveUploadedFileUsing(
                                fn (TemporaryUploadedFile $file): string => FilamentWebpUpload::store(
                                    file: $file,
                                    directory: 'dining/philosophy',
                                    targetWidth: 1200,
                                    targetHeight: 900,
                                )
                            ),
                        TextInput::make('philosophy_image_alt')
                            ->label('Image Alt Text')
                            ->maxLength(255),
                    ]),
                ])
                ->columnSpanFull(),
            Section::make('Why Dine at Nandini')
                ->description('Content and selling points for the Why Dine at Nandini section.')
                ->schema([
                    Section::make('Section Content')->columns(2)->schema([
                        TextInput::make('why_dine_eyebrow')
                            ->label('Eyebrow / Small Label')
                            ->maxLength(255),
                        Textarea::make('why_dine_heading')
                            ->label('Main Heading')
                            ->rows(3)
                            ->helperText('Line breaks are preserved on the website.'),
                    ]),
                    Section::make('Benefits / Selling Points')->schema([
                        Repeater::make('why_dine_items')
                            ->label('Why Dine Items')
                            ->columns(3)
                            ->minItems(1)
                            ->maxItems(8)
                            ->reorderable()
                            ->collapsible()
                            ->cloneable()
                            ->itemLabel(fn (array $state): string => str_replace(["\r", "\n"], ' ', $state['title'] ?? 'Benefit item'))
                            ->addActionLabel('Add benefit item')
                            ->schema([
                                Select::make('icon')
                                    ->label('Icon')
                                    ->options(self::iconOptions())
                                    ->native(false)
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Textarea::make('title')
                                    ->label('Title')
                                    ->rows(2)
                                    ->required()
                                    ->helperText('Line breaks are preserved on the website.'),
                                Textarea::make('description')
                                    ->label('Description')
                                    ->rows(3)
                                    ->required()
                                    ->helperText('Line breaks are preserved on the website.'),
                            ]),
                    ]),
                ])
                ->columnSpanFull(),
            Section::make('Restaurant Information Bar')
                ->description('The ordered information cards directly below the Dining hero.')
                ->schema([
                    Repeater::make('information_bar_items')
                        ->columns(2)->minItems(1)->maxItems(8)->reorderable()->collapsible()
                        ->itemLabel(fn (array $state): string => $state['label'] ?? 'Information item')
                        ->addActionLabel('Add information item')
                        ->schema(self::informationItemFields()),
                ])->columnSpanFull(),
            Section::make('Private Dining')
                ->description('Special occasions content, image, enquiry action and ordered occasion tags.')
                ->columns(2)
                ->schema([
                    Section::make('Content')->schema([
                        TextInput::make('private_dining_eyebrow')->label('Eyebrow')->maxLength(255),
                        Textarea::make('private_dining_heading')->label('Heading')->rows(2),
                        Textarea::make('private_dining_description')->label('Description')->rows(5),
                        TextInput::make('private_dining_cta_label')->label('CTA Label')->maxLength(255),
                        self::linkInput('private_dining_cta_url', 'CTA URL'),
                    ]),
                    Section::make('Media')->schema([
                        self::imageUpload('private_dining_image', 'dining/private-dining', 1200, 900),
                        TextInput::make('private_dining_image_alt')->label('Image Alt Text')->maxLength(255),
                    ]),
                    Section::make('Occasion Tags')->schema([
                        Repeater::make('private_dining_tags')
                            ->columns(2)->minItems(1)->maxItems(12)->reorderable()->collapsible()
                            ->itemLabel(fn (array $state): string => $state['label'] ?? 'Occasion')
                            ->addActionLabel('Add occasion')
                            ->schema([
                                TextInput::make('label')->required()->maxLength(255),
                                self::linkInput('url', 'Optional URL'),
                            ]),
                    ])->columnSpanFull(),
                ])->columnSpanFull(),
            Section::make('Plan Your Visit')
                ->description('Practical information and menu actions shown beside the FAQ.')
                ->schema([
                    Section::make('Section Content')->columns(2)->schema([
                        TextInput::make('visit_eyebrow')->label('Eyebrow')->maxLength(255),
                        Textarea::make('visit_heading')->label('Heading')->rows(2),
                    ]),
                    Section::make('Information Items')->schema([
                        Repeater::make('visit_information_items')
                            ->columns(2)->minItems(1)->maxItems(12)->reorderable()->collapsible()
                            ->itemLabel(fn (array $state): string => $state['label'] ?? 'Visit information')
                            ->addActionLabel('Add information item')
                            ->schema(self::informationItemFields(true)),
                    ]),
                    Section::make('Buttons')->columns(2)->schema([
                        TextInput::make('visit_food_menu_label')->label('Food Menu Button Label')->maxLength(255),
                        self::linkInput('visit_food_menu_url', 'Food Menu URL'),
                        TextInput::make('visit_premium_menu_label')->label('Premium Menu Button Label')->maxLength(255),
                        self::linkInput('visit_premium_menu_url', 'Premium Menu URL'),
                        TextInput::make('visit_beverage_menu_label')->label('Beverage Button Label')->maxLength(255),
                        self::linkInput('visit_beverage_menu_url', 'Beverage Menu URL'),
                    ]),
                ])->columnSpanFull(),
            Section::make('Frequently Asked Questions')
                ->schema([
                    Section::make('Section Content')->columns(2)->schema([
                        TextInput::make('faq_eyebrow')->label('Eyebrow')->maxLength(255),
                        Textarea::make('faq_heading')->label('Heading')->rows(2),
                    ]),
                    Repeater::make('faq_items')
                        ->label('FAQ Items')
                        ->minItems(1)->maxItems(30)->reorderable()->collapsible()
                        ->itemLabel(fn (array $state): string => $state['question'] ?? 'FAQ')
                        ->addActionLabel('Add question')
                        ->schema([
                            TextInput::make('question')->required()->maxLength(500),
                            Textarea::make('answer')->required()->rows(4),
                        ]),
                ])->columnSpanFull(),
            Section::make('Reservation CTA')
                ->description('The final background-image reservation panel above the footer.')
                ->columns(2)
                ->schema([
                    Section::make('Content')->schema([
                        Textarea::make('reservation_cta_heading')->label('Heading')->rows(2),
                        Textarea::make('reservation_cta_description')->label('Description')->rows(4)
                            ->helperText('Line breaks are preserved on the website.'),
                        TextInput::make('reservation_cta_label')->label('CTA Label')->maxLength(255),
                        self::linkInput('reservation_cta_url', 'CTA URL'),
                    ]),
                    Section::make('Background Image')->schema([
                        self::imageUpload('reservation_cta_background_image', 'dining/reservation-cta', 1920, 900, '16:9'),
                        TextInput::make('reservation_cta_background_image_alt')
                            ->label('Accessibility Description')->maxLength(255),
                    ]),
                ])->columnSpanFull(),
            Section::make('SEO Defaults')->schema([
                TextInput::make('meta_title')->maxLength(70),
                Textarea::make('meta_description')->maxLength(180)->rows(3),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('location'),
            TextColumn::make('opening_hours'),
            TextColumn::make('reservation_email'),
        ])->recordActions([EditAction::make()]);
    }

    public static function canCreate(): bool
    {
        return ! DiningSetting::query()->exists();
    }

    public static function canViewAny(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDiningSettings::route('/'),
            'edit' => EditDiningSetting::route('/{record}/edit'),
        ];
    }

    public static function iconOptions(): array
    {
        return [
            'leaves' => 'Leaves / Jungle', 'bowl' => 'Dining Bowl', 'dining' => 'Cutlery',
            'wine' => 'Wine Glass', 'heart' => 'Heart', 'clock' => 'Clock',
            'location' => 'Location', 'whatsapp' => 'WhatsApp', 'email' => 'Email',
            'guests' => 'People / Guests', 'star' => 'Star', 'sparkles' => 'Sparkles',
            'flame' => 'Flame', 'coffee' => 'Cup / Hot Drink',
        ];
    }

    public static function informationItemFields(bool $multiline = false): array
    {
        return [
            Select::make('icon')->options(self::iconOptions())->native(false)->searchable()->preload()->required(),
            TextInput::make('label')->required()->maxLength(255),
            ($multiline ? Textarea::make('value')->rows(3) : TextInput::make('value'))->required()->maxLength(1000),
            self::linkInput('link', 'Optional Link / Action'),
        ];
    }

    public static function linkInput(string $name, string $label): TextInput
    {
        return TextInput::make($name)->label($label)->maxLength(2048)
            ->rule('nullable')
            ->rule('regex:/^(https?:\/\/|mailto:|tel:|\/|#).+/i')
            ->helperText('Use an http(s), mailto:, tel:, root-relative, or # anchor URL.');
    }

    public static function imageUpload(string $name, string $directory, int $width, int $height, string $ratio = '4:3'): FileUpload
    {
        return FileUpload::make($name)->label('Image')->disk('public')->directory($directory)
            ->visibility('public')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->imagePreviewHeight('220')->panelAspectRatio($ratio)->panelLayout('integrated')->openable()->downloadable()
            ->saveUploadedFileUsing(fn (TemporaryUploadedFile $file): string => FilamentWebpUpload::store(
                file: $file, directory: $directory, targetWidth: $width, targetHeight: $height,
            ));
    }

}

<?php

namespace App\Filament\Resources\Pages\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class SectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';

    protected static ?string $title = 'Page Sections';

    private const MEDIA_SECTION_KEYS = [
        'image_overlay_section',
        'split_media_section',
        'split_media_reverse',
        'three_images_section',
        'two_images_section',
        'two_images_reverse',
    ];

    private const ITEM_SECTION_KEYS = [
        'how_it_works_section',
        'member_benefits_section',
        'membership_tier_section',
        'membership_use_points_section',
        'membership_faq_section',
        'dining_information_section',
        'spa_information_section',
    ];

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                Section::make('Section Content')
                    ->columnSpan([
                        'default' => 12,
                        'lg' => 8,
                    ])
                    ->columns(2)
                    ->schema([
                        Select::make('section_key')
                            ->label('Section Type')
                            ->required()
                            ->native(false)
                            ->live()
                            ->options([
                                'intro_text_section' => 'Intro / Text Section',
                                'how_it_works_section' => 'How It Works Section',
                                'member_benefits_section' => 'Member Benefits Table',
                                'membership_tier_section' => 'Membership Tier Section',
                                'membership_use_points_section' => 'Use Your Points Section',
                                'membership_faq_section' => 'Membership FAQ Section',
                                'dining_information_section' => 'Dining Information Section',
                                'spa_information_section' => 'Spa Information Section',
                                'image_overlay_section' => 'Image Overlay Section',
                                'split_media_section' => 'Split Media Section',
                                'split_media_reverse' => 'Split Media Reverse',
                                'three_images_section' => 'Three Images Section',
                                'two_images_section' => 'Two Images Section',
                                'two_images_reverse' => 'Two Images Reverse',
                            ])
                            ->default('intro_text_section')
                            ->afterStateUpdated(function (?string $state, Set $set, Get $get): void {
                                if (! in_array($state, ['dining_information_section', 'spa_information_section'], true)) {
                                    return;
                                }

                                if ($state === 'dining_information_section') {
                                    if (blank($get('description'))) {
                                        $set('description', '<p><strong>Cuisine:</strong><br>Western and Indonesian</p><p><strong>Opening times:</strong><br>Breakfast: 07:00 am to 10:30 am<br>Lunch: 12:00 pm to 03:00 pm<br>Dinner: 06:30 pm to 10:30 pm</p>');
                                    }

                                    if (blank($get('excerpt'))) {
                                        $set('excerpt', '<p><strong>Contact Us:</strong><br>+62 812-3687-1170<br>(Whatsapp Enabled)</p><p><strong>Email Us:</strong><br>reservation@nandinibali.com</p>');
                                    }

                                    if (blank($get('items'))) {
                                        $set('items', [
                                            [
                                                'label' => 'Reserve Now',
                                                'url' => '#',
                                            ],
                                            [
                                                'label' => 'View Menu',
                                                'url' => '#',
                                            ],
                                            [
                                                'label' => 'Premium Menu',
                                                'url' => '#',
                                            ],
                                            [
                                                'label' => 'Beverage List',
                                                'url' => '#',
                                            ],
                                        ]);
                                    }

                                    return;
                                }

                                if ($state === 'spa_information_section') {
                                    if (blank($get('description'))) {
                                        $set('description', '<p><strong>Spa:</strong><br>Traditional Balinese wellness and spa treatments</p><p><strong>Opening times:</strong><br>Daily: 09:00 am to 09:00 pm</p>');
                                    }

                                    if (blank($get('excerpt'))) {
                                        $set('excerpt', '<p><strong>Contact Us:</strong><br>+62 812-3687-1170<br>(Whatsapp Enabled)</p><p><strong>Email Us:</strong><br>reservation@nandinibali.com</p>');
                                    }

                                    if (blank($get('items'))) {
                                        $set('items', [
                                            [
                                                'label' => 'Reserve Now',
                                                'url' => '#',
                                            ],
                                            [
                                                'label' => 'View Spa Menu',
                                                'url' => '#',
                                            ],
                                            [
                                                'label' => 'Treatment List',
                                                'url' => '#',
                                            ],
                                            [
                                                'label' => 'Spa Packages',
                                                'url' => '#',
                                            ],
                                        ]);
                                    }
                                }
                            }),

                        TextInput::make('title')
                            ->label('Title')
                            ->placeholder('Section title')
                            ->maxLength(255),

                        TextInput::make('subtitle')
                            ->label('Subtitle')
                            ->placeholder('Section subtitle')
                            ->maxLength(255),

                        RichEditor::make('excerpt')
                            ->label(fn(Get $get): string => match ($get('section_key')) {
                                'member_benefits_section' => 'Tier Recognition Text',
                                'dining_information_section', 'spa_information_section' => 'Reservations',
                                default => 'Excerpt',
                            })
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline', 'strike', 'link'],
                                ['bulletList', 'orderedList'],
                                ['undo', 'redo'],
                            ])
                            ->visible(fn(Get $get): bool => in_array($get('section_key'), self::MEDIA_SECTION_KEYS, true)
                                || in_array($get('section_key'), [
                                    'member_benefits_section',
                                    'dining_information_section',
                                    'spa_information_section',
                                ], true))
                            ->columnSpanFull(),

                        RichEditor::make('description')
                            ->label(fn(Get $get): string => match ($get('section_key')) {
                                'dining_information_section' => 'Restaurant Information',
                                'spa_information_section' => 'Spa Information',
                                default => 'Description',
                            })
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline', 'strike', 'link'],
                                ['h1', 'h2', 'h3'],
                                ['bulletList', 'orderedList'],
                                ['blockquote'],
                                ['undo', 'redo'],
                            ])
                            ->columnSpanFull(),

                        Repeater::make('items')
                            ->label(fn(Get $get): string => match ($get('section_key')) {
                                'member_benefits_section' => 'Member Benefit Rows',
                                'membership_tier_section' => 'Membership Tier Rows',
                                'membership_use_points_section' => 'Use Your Points Cards',
                                'membership_faq_section' => 'FAQ Rows',
                                'dining_information_section', 'spa_information_section' => 'Additional Information Buttons',
                                default => 'How It Works Items',
                            })
                            ->visible(fn(Get $get): bool => in_array($get('section_key'), self::ITEM_SECTION_KEYS, true))
                            ->columnSpanFull()
                            ->columns(4)
                            ->defaultItems(fn(Get $get): int => match ($get('section_key')) {
                                'member_benefits_section' => 3,
                                'membership_tier_section' => 4,
                                'membership_use_points_section' => 3,
                                'membership_faq_section' => 5,
                                'dining_information_section', 'spa_information_section' => 4,
                                default => 4,
                            })
                            ->minItems(1)
                            ->maxItems(fn(Get $get): int => in_array($get('section_key'), [
                                'dining_information_section',
                                'spa_information_section',
                            ], true) ? 10 : 50)
                            ->reorderable()
                            ->collapsible()
                            ->cloneable()
                            ->itemLabel(function (array $state, Get $get): string {
                                if ($get('section_key') === 'member_benefits_section') {
                                    return $state['benefit'] ?? 'Benefit Row';
                                }

                                if ($get('section_key') === 'membership_tier_section') {
                                    $tierName = $state['tier_name'] ?? 'Tier';
                                    $circleName = $state['circle_name'] ?? 'Circle';

                                    return trim($tierName . ' - ' . $circleName, ' -');
                                }

                                if ($get('section_key') === 'membership_use_points_section') {
                                    return $state['title'] ?? 'Use Your Points Card';
                                }

                                if ($get('section_key') === 'membership_faq_section') {
                                    return $state['question'] ?? 'FAQ Row';
                                }

                                if (in_array($get('section_key'), ['dining_information_section', 'spa_information_section'], true)) {
                                    return $state['label'] ?? 'Button';
                                }

                                return $state['title'] ?? 'How It Works Item';
                            })
                            ->addActionLabel(fn(Get $get): string => match ($get('section_key')) {
                                'member_benefits_section' => 'Add benefit row',
                                'membership_tier_section' => 'Add tier row',
                                'membership_use_points_section' => 'Add card',
                                'membership_faq_section' => 'Add FAQ',
                                'dining_information_section', 'spa_information_section' => 'Add button',
                                default => 'Add item',
                            })
                            ->schema([
                                TextInput::make('label')
                                    ->label('Button Label')
                                    ->default('Reserve Now')
                                    ->maxLength(255)
                                    ->required(fn(Get $get): bool => in_array($get('../../section_key'), [
                                        'dining_information_section',
                                        'spa_information_section',
                                    ], true))
                                    ->visible(fn(Get $get): bool => in_array($get('../../section_key'), [
                                        'dining_information_section',
                                        'spa_information_section',
                                    ], true)),

                                TextInput::make('url')
                                    ->label('Button Link')
                                    ->default('#')
                                    ->maxLength(255)
                                    ->required(fn(Get $get): bool => in_array($get('../../section_key'), [
                                        'dining_information_section',
                                        'spa_information_section',
                                    ], true))
                                    ->visible(fn(Get $get): bool => in_array($get('../../section_key'), [
                                        'dining_information_section',
                                        'spa_information_section',
                                    ], true)),

                                FileUpload::make('image')
                                    ->label('Card Image')
                                    ->disk('public')
                                    ->directory('membership/use-points')
                                    ->visibility('public')
                                    ->image()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->imagePreviewHeight('160')
                                    ->panelAspectRatio('16:10')
                                    ->panelLayout('integrated')
                                    ->openable()
                                    ->downloadable()
                                    ->saveUploadedFileUsing(
                                        fn(TemporaryUploadedFile $file): string => self::storeAsWebp(
                                            file: $file,
                                            directory: 'membership/use-points',
                                            targetWidth: 1200,
                                            targetHeight: 750,
                                        )
                                    )
                                    ->visible(fn(Get $get): bool => $get('../../section_key') === 'membership_use_points_section')
                                    ->columnSpanFull(),

                                Select::make('icon')
                                    ->label('Icon')
                                    ->native(false)
                                    ->options([
                                        'home' => 'Home / Hotel Stay',
                                        'cup' => 'Cup / Food & Beverages',
                                        'heart' => 'Heart / Spa & Wellness',
                                        'user' => 'User',
                                        'book' => 'Book',
                                        'arrow-up' => 'Arrow Up',
                                        'gift' => 'Gift',
                                        'star' => 'Star',
                                        'sparkles' => 'Sparkles',
                                    ])
                                    ->default('user')
                                    ->required(fn(Get $get): bool => $get('../../section_key') === 'how_it_works_section')
                                    ->visible(fn(Get $get): bool => $get('../../section_key') === 'how_it_works_section'),

                                TextInput::make('title')
                                    ->label('Title')
                                    ->placeholder(fn(Get $get): string => $get('../../section_key') === 'membership_use_points_section'
                                        ? 'Riverside Sanctuary Spa'
                                        : 'JOIN FOR FREE')
                                    ->maxLength(255)
                                    ->required(fn(Get $get): bool => in_array($get('../../section_key'), [
                                        'how_it_works_section',
                                        'membership_use_points_section',
                                    ], true))
                                    ->visible(fn(Get $get): bool => in_array($get('../../section_key'), [
                                        'how_it_works_section',
                                        'membership_use_points_section',
                                    ], true))
                                    ->columnSpan(fn(Get $get): int => $get('../../section_key') === 'membership_use_points_section' ? 2 : 1),

                                TextInput::make('points_label')
                                    ->label('Points Label')
                                    ->placeholder('528 Points')
                                    ->maxLength(255)
                                    ->visible(fn(Get $get): bool => $get('../../section_key') === 'membership_use_points_section'),

                                TextInput::make('button_label')
                                    ->label('Button Label')
                                    ->placeholder('Redeem')
                                    ->maxLength(255)
                                    ->visible(fn(Get $get): bool => $get('../../section_key') === 'membership_use_points_section'),

                                TextInput::make('button_url')
                                    ->label('Button URL')
                                    ->placeholder('#')
                                    ->maxLength(255)
                                    ->visible(fn(Get $get): bool => $get('../../section_key') === 'membership_use_points_section'),

                                Textarea::make('description')
                                    ->label(fn(Get $get): string => match ($get('../../section_key')) {
                                        'membership_tier_section' => 'Tier Description',
                                        'membership_use_points_section' => 'Card Description',
                                        default => 'Description',
                                    })
                                    ->placeholder(fn(Get $get): string => match ($get('../../section_key')) {
                                        'membership_tier_section' => 'Describe this membership tier.',
                                        'membership_use_points_section' => 'Indulge in the ultimate riverside retreat by the sacred Ayung River.',
                                        default => 'Join the program by signing up through our loyalty website.',
                                    })
                                    ->rows(3)
                                    ->columnSpanFull()
                                    ->visible(fn(Get $get): bool => in_array($get('../../section_key'), [
                                        'how_it_works_section',
                                        'membership_tier_section',
                                        'membership_use_points_section',
                                    ], true)),

                                TextInput::make('question')
                                    ->label('Question')
                                    ->placeholder('How do I register?')
                                    ->maxLength(255)
                                    ->required(fn(Get $get): bool => $get('../../section_key') === 'membership_faq_section')
                                    ->visible(fn(Get $get): bool => $get('../../section_key') === 'membership_faq_section')
                                    ->columnSpanFull(),

                                Textarea::make('answer')
                                    ->label('Answer')
                                    ->placeholder('Write the answer for this FAQ.')
                                    ->rows(4)
                                    ->required(fn(Get $get): bool => $get('../../section_key') === 'membership_faq_section')
                                    ->visible(fn(Get $get): bool => $get('../../section_key') === 'membership_faq_section')
                                    ->columnSpanFull(),

                                Select::make('card_design')
                                    ->label('Card Design')
                                    ->native(false)
                                    ->options([
                                        'bronze' => 'Bronze Card',
                                        'silver' => 'Silver Card',
                                        'gold' => 'Gold Card',
                                        'platinum' => 'Platinum Card',
                                    ])
                                    ->default('bronze')
                                    ->required(fn(Get $get): bool => $get('../../section_key') === 'membership_tier_section')
                                    ->visible(fn(Get $get): bool => $get('../../section_key') === 'membership_tier_section'),

                                TextInput::make('tier_name')
                                    ->label('Tier Name')
                                    ->placeholder('Bronze')
                                    ->maxLength(255)
                                    ->required(fn(Get $get): bool => $get('../../section_key') === 'membership_tier_section')
                                    ->visible(fn(Get $get): bool => $get('../../section_key') === 'membership_tier_section'),

                                TextInput::make('circle_name')
                                    ->label('Circle Name')
                                    ->placeholder('Dana')
                                    ->maxLength(255)
                                    ->required(fn(Get $get): bool => $get('../../section_key') === 'membership_tier_section')
                                    ->visible(fn(Get $get): bool => $get('../../section_key') === 'membership_tier_section'),

                                TextInput::make('circle_meaning')
                                    ->label('Circle Meaning')
                                    ->placeholder('Generosity')
                                    ->maxLength(255)
                                    ->visible(fn(Get $get): bool => $get('../../section_key') === 'membership_tier_section'),

                                TextInput::make('benefit')
                                    ->label('Benefit')
                                    ->placeholder('Extra savings on rooms')
                                    ->maxLength(255)
                                    ->required(fn(Get $get): bool => $get('../../section_key') === 'member_benefits_section')
                                    ->visible(fn(Get $get): bool => $get('../../section_key') === 'member_benefits_section')
                                    ->columnSpanFull(),

                                TextInput::make('bronze')
                                    ->label('Bronze')
                                    ->placeholder('✓ / - / 5%')
                                    ->visible(fn(Get $get): bool => $get('../../section_key') === 'member_benefits_section'),

                                TextInput::make('silver')
                                    ->label('Silver')
                                    ->placeholder('✓ / - / 5%')
                                    ->visible(fn(Get $get): bool => $get('../../section_key') === 'member_benefits_section'),

                                TextInput::make('gold')
                                    ->label('Gold')
                                    ->placeholder('✓ / - / 10%')
                                    ->visible(fn(Get $get): bool => $get('../../section_key') === 'member_benefits_section'),

                                TextInput::make('platinum')
                                    ->label('Platinum')
                                    ->placeholder('✓ / - / 15%')
                                    ->visible(fn(Get $get): bool => $get('../../section_key') === 'member_benefits_section'),
                            ]),

                        Repeater::make('images')
                            ->label('Section Images')
                            ->relationship('images')
                            ->orderColumn('sort_order')
                            ->addActionLabel('Add image')
                            ->collapsible()
                            ->cloneable()
                            ->columnSpanFull()
                            ->columns(2)
                            ->itemLabel(fn(array $state): string => $state['image_alt'] ?? $state['caption'] ?? 'Section Image')
                            ->visible(fn(Get $get): bool => in_array($get('section_key'), self::MEDIA_SECTION_KEYS, true))
                            ->schema([
                                FileUpload::make('image')
                                    ->label('Desktop Image')
                                    ->disk('public')
                                    ->directory('pages/sections')
                                    ->visibility('public')
                                    ->image()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->imagePreviewHeight('140')
                                    ->panelAspectRatio('16:9')
                                    ->panelLayout('integrated')
                                    ->openable()
                                    ->downloadable()
                                    ->saveUploadedFileUsing(
                                        fn(TemporaryUploadedFile $file): string => self::storeAsWebp(
                                            file: $file,
                                            directory: 'pages/sections',
                                            targetWidth: 1600,
                                            targetHeight: 900,
                                        )
                                    ),

                                TextInput::make('image_alt')
                                    ->label('Desktop Alt Text')
                                    ->placeholder('Describe the desktop image for SEO and accessibility')
                                    ->maxLength(255),

                                FileUpload::make('mobile_image')
                                    ->label('Mobile Image')
                                    ->disk('public')
                                    ->directory('pages/sections/mobile')
                                    ->visibility('public')
                                    ->image()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->imagePreviewHeight('140')
                                    ->panelAspectRatio('4:3')
                                    ->panelLayout('integrated')
                                    ->openable()
                                    ->downloadable()
                                    ->saveUploadedFileUsing(
                                        fn(TemporaryUploadedFile $file): string => self::storeAsWebp(
                                            file: $file,
                                            directory: 'pages/sections/mobile',
                                            targetWidth: 1200,
                                            targetHeight: 900,
                                        )
                                    ),

                                TextInput::make('mobile_image_alt')
                                    ->label('Mobile Alt Text')
                                    ->placeholder('Describe the mobile image')
                                    ->maxLength(255),

                                TextInput::make('caption')
                                    ->label('Caption')
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                Hidden::make('is_active')
                                    ->default(true),

                                Hidden::make('sort_order')
                                    ->default(0),
                            ]),
                    ]),

                Grid::make()
                    ->columnSpan([
                        'default' => 12,
                        'lg' => 4,
                    ])
                    ->schema([
                        Section::make('Button')
                            ->columnSpanFull()
                            ->visible(fn(Get $get): bool => in_array($get('section_key'), self::MEDIA_SECTION_KEYS, true)
                                || $get('section_key') === 'member_benefits_section')
                            ->schema([
                                TextInput::make('button_label')
                                    ->label('Button Label')
                                    ->maxLength(255),

                                Select::make('button_link_type')
                                    ->label('Button Link Type')
                                    ->native(false)
                                    ->live()
                                    ->options([
                                        'manual' => 'Manual Link',
                                        'route' => 'Route',
                                    ])
                                    ->default('manual')
                                    ->required(),

                                TextInput::make('button_url')
                                    ->label('Manual Button URL')
                                    ->placeholder('Example: /offers or https://example.com')
                                    ->maxLength(255)
                                    ->visible(fn(Get $get): bool => $get('button_link_type') === 'manual'),

                                TextInput::make('button_route')
                                    ->label('Button Route')
                                    ->placeholder('Example: offers.index')
                                    ->maxLength(255)
                                    ->visible(fn(Get $get): bool => $get('button_link_type') === 'route'),
                            ]),

                        Section::make('Layout')
                            ->columnSpanFull()
                            ->visible(fn(Get $get): bool => in_array($get('section_key'), [
                                'intro_text_section',
                                'image_overlay_section',
                                'how_it_works_section',
                                'membership_tier_section',
                                'membership_use_points_section',
                                'membership_faq_section',
                            ], true))
                            ->schema([
                                Select::make('text_align')
                                    ->label('Text Align')
                                    ->native(false)
                                    ->visible(fn(Get $get): bool => in_array($get('section_key'), [
                                        'intro_text_section',
                                        'image_overlay_section',
                                        'membership_tier_section',
                                        'membership_use_points_section',
                                        'membership_faq_section',
                                    ], true))
                                    ->options([
                                        'left' => 'Left',
                                        'center' => 'Center',
                                        'right' => 'Right',
                                    ])
                                    ->default('center')
                                    ->required(),

                                Select::make('background_color')
                                    ->label('Background Color')
                                    ->native(false)
                                    ->visible(fn(Get $get): bool => in_array($get('section_key'), [
                                        'intro_text_section',
                                        'how_it_works_section',
                                        'membership_tier_section',
                                        'membership_use_points_section',
                                        'membership_faq_section',
                                    ], true))
                                    ->options([
                                        'white' => 'White',
                                        'soft_gray' => 'Soft Gray',
                                        'warm_ivory' => 'Warm Ivory',
                                        'light_gold' => 'Light Gold',
                                        'dark_navy' => 'Dark Navy',
                                    ])
                                    ->default('white'),
                            ]),

                        Section::make('Settings')
                            ->columnSpanFull()
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true)
                                    ->required(),

                                Hidden::make('sort_order')
                                    ->default(0),
                            ]),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->reorderRecordsTriggerAction(
                fn(Action $action, bool $isReordering) => $action
                    ->button()
                    ->label($isReordering ? 'Done sorting' : 'Sort sections')
            )
            ->columns([
                ImageColumn::make('first_image')
                    ->label('Image')
                    ->disk('public')
                    ->square()
                    ->getStateUsing(fn($record): ?string => $record->images()
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->first()?->image),

                TextColumn::make('title')
                    ->label('Section')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->limit(45)
                    ->placeholder('Untitled section')
                    ->description(fn($record): ?string => Str::limit($record->subtitle ?? '', 50)),

                TextColumn::make('section_key')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'intro_text_section' => 'Intro Text',
                        'how_it_works_section' => 'How It Works',
                        'member_benefits_section' => 'Member Benefits',
                        'membership_tier_section' => 'Membership Tiers',
                        'membership_use_points_section' => 'Use Your Points',
                        'membership_faq_section' => 'Membership FAQ',
                        'dining_information_section' => 'Dining Information',
                        'spa_information_section' => 'Spa Information',
                        'image_overlay_section' => 'Image Overlay',
                        'split_media_section' => 'Split Media',
                        'split_media_reverse' => 'Split Media Reverse',
                        'three_images_section' => 'Three Images',
                        'two_images_section' => 'Two Images',
                        'two_images_reverse' => 'Two Images Reverse',
                        default => $state ? Str::headline($state) : '-',
                    })
                    ->color(fn(?string $state): string => match ($state) {
                        'intro_text_section' => 'gray',
                        'how_it_works_section' => 'primary',
                        'member_benefits_section' => 'primary',
                        'membership_tier_section' => 'warning',
                        'membership_use_points_section' => 'success',
                        'membership_faq_section' => 'info',
                        'dining_information_section' => 'success',
                        'spa_information_section' => 'warning',
                        'image_overlay_section' => 'info',
                        'split_media_section' => 'success',
                        'split_media_reverse' => 'warning',
                        'three_images_section' => 'primary',
                        'two_images_section' => 'success',
                        'two_images_reverse' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('text_align')
                    ->label('Align')
                    ->badge()
                    ->formatStateUsing(fn(?string $state): string => $state ? Str::headline($state) : '-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('background_color')
                    ->label('Background')
                    ->badge()
                    ->formatStateUsing(fn(?string $state): string => $state ? Str::headline(str_replace('_', ' ', $state)) : '-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('excerpt')
                    ->label('Excerpt')
                    ->limit(60)
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Order')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('New section'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected static function storeAsWebp(
        TemporaryUploadedFile $file,
        string $directory,
        int $targetWidth,
        int $targetHeight,
    ): string {
        $sourcePath = $file->getRealPath();

        $imageInfo = getimagesize($sourcePath);

        if (! $imageInfo) {
            return $file->store($directory, 'public');
        }

        [$originalWidth, $originalHeight] = $imageInfo;
        $mime = $imageInfo['mime'] ?? null;

        $sourceImage = match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($sourcePath),
            'image/png' => imagecreatefrompng($sourcePath),
            'image/webp' => imagecreatefromwebp($sourcePath),
            default => null,
        };

        if (! $sourceImage) {
            return $file->store($directory, 'public');
        }

        if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
            $exif = @exif_read_data($sourcePath);

            if (! empty($exif['Orientation'])) {
                $sourceImage = match ((int) $exif['Orientation']) {
                    3 => imagerotate($sourceImage, 180, 0),
                    6 => imagerotate($sourceImage, -90, 0),
                    8 => imagerotate($sourceImage, 90, 0),
                    default => $sourceImage,
                };

                $originalWidth = imagesx($sourceImage);
                $originalHeight = imagesy($sourceImage);
            }
        }

        $sourceRatio = $originalWidth / $originalHeight;
        $targetRatio = $targetWidth / $targetHeight;

        if ($sourceRatio > $targetRatio) {
            $cropHeight = $originalHeight;
            $cropWidth = (int) round($originalHeight * $targetRatio);
            $cropX = (int) round(($originalWidth - $cropWidth) / 2);
            $cropY = 0;
        } else {
            $cropWidth = $originalWidth;
            $cropHeight = (int) round($originalWidth / $targetRatio);
            $cropX = 0;
            $cropY = (int) round(($originalHeight - $cropHeight) / 2);
        }

        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);

        imagecopyresampled(
            $targetImage,
            $sourceImage,
            0,
            0,
            $cropX,
            $cropY,
            $targetWidth,
            $targetHeight,
            $cropWidth,
            $cropHeight
        );

        $filename = $directory . '/' . Str::uuid() . '.webp';
        $storagePath = Storage::disk('public')->path($filename);

        Storage::disk('public')->makeDirectory($directory);

        imagewebp($targetImage, $storagePath, 85);

        imagedestroy($sourceImage);
        imagedestroy($targetImage);

        return $filename;
    }
}

<?php

namespace App\Filament\Resources\Experiences\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ExperienceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                Section::make('Experience Content')
                    ->columnSpan([
                        'default' => 12,
                        'lg' => 8,
                    ])
                    ->columns(2)
                    ->schema([
                        Select::make('experience_category_id')
                            ->label('Category')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Category Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, ?string $state): void {
                                        $set('slug', Str::slug($state ?? ''));
                                    }),

                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique('experience_categories', 'slug'),

                                TextInput::make('subtitle')
                                    ->label('Subtitle')
                                    ->maxLength(255),

                                Textarea::make('excerpt')
                                    ->label('Excerpt')
                                    ->rows(3)
                                    ->columnSpanFull(),

                                RichEditor::make('description')
                                    ->label('Description')
                                    ->toolbarButtons([
                                        ['bold', 'italic', 'underline', 'strike', 'link'],
                                        ['h2', 'h3'],
                                        ['bulletList', 'orderedList'],
                                        ['blockquote'],
                                        ['undo', 'redo'],
                                    ])
                                    ->columnSpanFull(),

                                FileUpload::make('image')
                                    ->label('Category Image')
                                    ->disk('public')
                                    ->directory('experience-categories')
                                    ->visibility('public')
                                    ->image()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->imagePreviewHeight('160')
                                    ->panelAspectRatio('3:2')
                                    ->panelLayout('integrated')
                                    ->openable()
                                    ->downloadable()
                                    ->saveUploadedFileUsing(
                                        fn(TemporaryUploadedFile $file): string => self::storeAsWebp(
                                            file: $file,
                                            directory: 'experience-categories',
                                            targetWidth: 1200,
                                            targetHeight: 800,
                                        )
                                    ),

                                TextInput::make('image_alt')
                                    ->label('Category Image Alt Text')
                                    ->placeholder('Example: Holy river blessing experience in Ubud')
                                    ->maxLength(255),

                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true),

                                Hidden::make('sort_order')
                                    ->default(0),
                            ]),

                        TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, ?string $state): void {
                                $set('slug', Str::slug($state ?? ''));
                            }),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        TextInput::make('subtitle')
                            ->label('Subtitle')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('excerpt')
                            ->label('Excerpt')
                            ->rows(3)
                            ->columnSpanFull(),

                        RichEditor::make('description')
                            ->label('Description')
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline', 'strike', 'link'],
                                ['h2', 'h3'],
                                ['bulletList', 'orderedList'],
                                ['blockquote'],
                                ['undo', 'redo'],
                            ])
                            ->columnSpanFull(),

                        RichEditor::make('inclusions')
                            ->label('Inclusions')
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline', 'strike', 'link'],
                                ['h2', 'h3'],
                                ['bulletList', 'orderedList'],
                                ['blockquote'],
                                ['undo', 'redo'],
                            ])
                            ->columnSpanFull(),

                        TextInput::make('duration')
                            ->label('Duration')
                            ->placeholder('Example: 60 minutes')
                            ->maxLength(255),

                        TextInput::make('location')
                            ->label('Location')
                            ->placeholder('Example: Ayung River')
                            ->maxLength(255),

                        Section::make('Prices')
                            ->columnSpanFull()
                            ->schema([
                                Repeater::make('prices')
                                    ->label('Experience Prices')
                                    ->relationship('prices')
                                    ->orderColumn('sort_order')
                                    ->addActionLabel('Add price')
                                    ->collapsible()
                                    ->cloneable()
                                    ->columns(12)
                                    ->itemLabel(function (array $state): string {
                                        $label = $state['label'] ?? 'Price';
                                        $price = $state['price'] ?? null;
                                        $unit = $state['unit_type'] ?? null;

                                        if ($price) {
                                            return $label . ' - IDR ' . number_format((float) $price, 0) . ' / ' . str_replace('_', ' ', $unit ?? '');
                                        }

                                        return $label;
                                    })
                                    ->schema([
                                        TextInput::make('label')
                                            ->label('Label')
                                            ->placeholder('Example: Couple Package')
                                            ->maxLength(255)
                                            ->columnSpan(3),

                                        TextInput::make('price')
                                            ->label('Price')
                                            ->numeric()
                                            ->prefix('IDR')
                                            ->required()
                                            ->default(0)
                                            ->columnSpan(3),

                                        Select::make('price_type')
                                            ->label('Price Type')
                                            ->options([
                                                'plus_plus' => '++',
                                                'net' => 'Net',
                                                'inclusive' => 'Inclusive',
                                            ])
                                            ->default('plus_plus')
                                            ->required()
                                            ->columnSpan(2),

                                        Select::make('unit_type')
                                            ->label('Unit Type')
                                            ->options([
                                                'per_person' => 'Per Person',
                                                'per_couple' => 'Per Couple',
                                                'per_car' => 'Per Car',
                                                'per_booking' => 'Per Booking',
                                            ])
                                            ->default('per_person')
                                            ->required()
                                            ->columnSpan(2),

                                        TextInput::make('currency')
                                            ->label('Currency')
                                            ->default('IDR')
                                            ->maxLength(10)
                                            ->columnSpan(2),

                                        TextInput::make('min_qty')
                                            ->label('Min Qty')
                                            ->numeric()
                                            ->columnSpan(2),

                                        TextInput::make('max_qty')
                                            ->label('Max Qty')
                                            ->numeric()
                                            ->columnSpan(2),

                                        Textarea::make('notes')
                                            ->label('Notes')
                                            ->rows(2)
                                            ->columnSpan(6),

                                        Toggle::make('is_active')
                                            ->label('Active')
                                            ->default(true)
                                            ->columnSpan(2),

                                        Hidden::make('sort_order')
                                            ->default(0),
                                    ]),
                            ]),

                        Section::make('SEO')
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('meta_title')
                                    ->label('Meta Title')
                                    ->live(debounce: 500)
                                    ->maxLength(70)
                                    ->helperText(function (?string $state): string {
                                        $text = trim($state ?? '');
                                        $characters = mb_strlen($text);

                                        return "{$characters}/60 characters recommended";
                                    }),

                                Textarea::make('meta_description')
                                    ->label('Meta Description')
                                    ->rows(3)
                                    ->live(debounce: 500)
                                    ->maxLength(180)
                                    ->helperText(function (?string $state): string {
                                        $text = trim($state ?? '');
                                        $characters = mb_strlen($text);

                                        return "{$characters}/160 characters recommended";
                                    }),
                            ]),
                    ]),

                Grid::make()
                    ->columnSpan([
                        'default' => 12,
                        'lg' => 4,
                    ])
                    ->schema([
                        Section::make('Main Image')
                            ->description('Used for the experience detail/header image.')
                            ->columnSpanFull()
                            ->schema([
                                FileUpload::make('image')
                                    ->label('Main Image')
                                    ->disk('public')
                                    ->directory('experiences/main')
                                    ->visibility('public')
                                    ->image()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->imagePreviewHeight('180')
                                    ->panelAspectRatio('16:9')
                                    ->panelLayout('integrated')
                                    ->openable()
                                    ->downloadable()
                                    ->saveUploadedFileUsing(
                                        fn(TemporaryUploadedFile $file): string => self::storeAsWebp(
                                            file: $file,
                                            directory: 'experiences/main',
                                            targetWidth: 1600,
                                            targetHeight: 900,
                                        )
                                    ),

                                TextInput::make('image_alt')
                                    ->label('Main Image Alt Text')
                                    ->placeholder('Example: Balinese holy river blessing at Ayung River')
                                    ->maxLength(255),
                            ]),

                        Section::make('Card Image')
                            ->description('Used for carousel and listing cards.')
                            ->columnSpanFull()
                            ->schema([
                                FileUpload::make('card_image')
                                    ->label('Card Image')
                                    ->disk('public')
                                    ->directory('experiences/cards')
                                    ->visibility('public')
                                    ->image()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->imagePreviewHeight('180')
                                    ->panelAspectRatio('3:2')
                                    ->panelLayout('integrated')
                                    ->openable()
                                    ->downloadable()
                                    ->saveUploadedFileUsing(
                                        fn(TemporaryUploadedFile $file): string => self::storeAsWebp(
                                            file: $file,
                                            directory: 'experiences/cards',
                                            targetWidth: 1200,
                                            targetHeight: 800,
                                        )
                                    ),

                                TextInput::make('card_image_alt')
                                    ->label('Card Image Alt Text')
                                    ->placeholder('Example: Holy river wellness experience in Ubud')
                                    ->maxLength(255),
                            ]),

                        Section::make('Settings')
                            ->columnSpanFull()
                            ->schema([
                                Toggle::make('is_featured')
                                    ->label('Featured')
                                    ->default(false),

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

    private static function storeAsWebp(
        TemporaryUploadedFile $file,
        string $directory,
        int $targetWidth,
        int $targetHeight,
    ): string {
        $disk = Storage::disk('public');

        $disk->makeDirectory($directory);

        $sourcePath = $file->getRealPath();
        $mimeType = $file->getMimeType();

        $sourceImage = match ($mimeType) {
            'image/jpeg', 'image/jpg' => imagecreatefromjpeg($sourcePath),
            'image/png' => imagecreatefrompng($sourcePath),
            'image/webp' => imagecreatefromwebp($sourcePath),
            default => null,
        };

        if (! $sourceImage) {
            return $file->store($directory, 'public');
        }

        if (in_array($mimeType, ['image/jpeg', 'image/jpg'], true) && function_exists('exif_read_data')) {
            $sourceImage = self::fixImageOrientation($sourceImage, $sourcePath);
        }

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);

        $sourceRatio = $sourceWidth / $sourceHeight;
        $targetRatio = $targetWidth / $targetHeight;

        if ($sourceRatio > $targetRatio) {
            $cropWidth = (int) round($sourceHeight * $targetRatio);
            $cropHeight = $sourceHeight;
        } else {
            $cropWidth = $sourceWidth;
            $cropHeight = (int) round($sourceWidth / $targetRatio);
        }

        $cropX = (int) round(($sourceWidth - $cropWidth) / 2);
        $cropY = (int) round(($sourceHeight - $cropHeight) / 2);

        $finalImage = imagecreatetruecolor($targetWidth, $targetHeight);

        imagealphablending($finalImage, false);
        imagesavealpha($finalImage, true);

        imagecopyresampled(
            $finalImage,
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

        $path = $directory . '/' . Str::uuid() . '.webp';
        $fullPath = $disk->path($path);

        imagewebp($finalImage, $fullPath, 82);

        imagedestroy($sourceImage);
        imagedestroy($finalImage);

        return $path;
    }

    private static function fixImageOrientation($image, string $sourcePath)
    {
        $exif = @exif_read_data($sourcePath);

        if (! isset($exif['Orientation'])) {
            return $image;
        }

        return match ((int) $exif['Orientation']) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => $image,
        };
    }
}

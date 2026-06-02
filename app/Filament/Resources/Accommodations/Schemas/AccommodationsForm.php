<?php

namespace App\Filament\Resources\Accommodations\Schemas;

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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class AccommodationsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                Section::make('Accommodation Content')
                    ->columnSpan([
                        'default' => 12,
                        'lg' => 8,
                    ])
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Accommodation Name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, ?string $state): void {
                                $set('slug', Str::slug($state ?? ''));
                            }),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        TextInput::make('subtitle')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('excerpt')
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

                        Section::make('Accommodation Details')
                            ->columnSpanFull()
                            ->columns(2)
                            ->schema([
                                Select::make('accommodation_type')
                                    ->label('Accommodation Type')
                                    ->options([
                                        'villa' => 'Jungle Villa',
                                        'suite' => 'Royal Suite',
                                    ])
                                    ->default('villa')
                                    ->required()
                                    ->native(false)
                                    ->helperText('This controls the frontend URL prefix.')
                                    ->columnSpanFull(),

                                TextInput::make('villa_code')
                                    ->label('Villa Code')
                                    ->placeholder('Example: PVV')
                                    ->maxLength(20)
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Booking Button')
                            ->columnSpanFull()
                            ->columns(2)
                            ->schema([
                                TextInput::make('button_label')
                                    ->label('Button Label')
                                    ->placeholder('Book Now')
                                    ->maxLength(255),

                                TextInput::make('button_url')
                                    ->label('Button URL')
                                    ->url()
                                    ->maxLength(255),

                                TextInput::make('booking_url_override')
                                    ->label('Booking URL Override')
                                    ->url()
                                    ->maxLength(255)
                                    ->columnSpanFull()
                                    ->helperText('Use this if this accommodation has a specific booking engine URL.'),
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

                Grid::make()
                    ->columnSpan([
                        'default' => 12,
                        'lg' => 4,
                    ])
                    ->schema([
                        Section::make('Hero Images')
                            ->columnSpanFull()
                            ->schema([
                                FileUpload::make('hero_image')
                                    ->label('Desktop')
                                    ->disk('public')
                                    ->directory('accommodations/hero')
                                    ->visibility('public')
                                    ->image()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->imagePreviewHeight('160')
                                    ->panelAspectRatio('16:9')
                                    ->panelLayout('integrated')
                                    ->openable()
                                    ->downloadable()
                                    ->saveUploadedFileUsing(
                                        fn(TemporaryUploadedFile $file): string => self::storeAsWebp(
                                            file: $file,
                                            directory: 'accommodations/hero',
                                            targetWidth: 1600,
                                            targetHeight: 900,
                                        )
                                    ),

                                TextInput::make('hero_image_alt')
                                    ->label('Desktop Alt Text')
                                    ->placeholder('Example: Panoramic jungle view villa bedroom')
                                    ->maxLength(255),

                                FileUpload::make('hero_mobile_image')
                                    ->label('Mobile')
                                    ->disk('public')
                                    ->directory('accommodations/hero-mobile')
                                    ->visibility('public')
                                    ->image()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->imagePreviewHeight('160')
                                    ->panelAspectRatio('4:3')
                                    ->panelLayout('integrated')
                                    ->openable()
                                    ->downloadable()
                                    ->saveUploadedFileUsing(
                                        fn(TemporaryUploadedFile $file): string => self::storeAsWebp(
                                            file: $file,
                                            directory: 'accommodations/hero-mobile',
                                            targetWidth: 1200,
                                            targetHeight: 900,
                                        )
                                    ),

                                TextInput::make('hero_mobile_image_alt')
                                    ->label('Mobile Alt Text')
                                    ->placeholder('Example: Nandini Jungle accommodation mobile hero image')
                                    ->maxLength(255),
                            ]),

                        Section::make('Card Image')
                            ->columnSpanFull()
                            ->schema([
                                FileUpload::make('card_image')
                                    ->label('Card')
                                    ->disk('public')
                                    ->directory('accommodations/cards')
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
                                            directory: 'accommodations/cards',
                                            targetWidth: 1200,
                                            targetHeight: 800,
                                        )
                                    ),

                                TextInput::make('card_image_alt')
                                    ->label('Card Alt Text')
                                    ->placeholder('Example: Jungle villa bedroom with balcony')
                                    ->maxLength(255),
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

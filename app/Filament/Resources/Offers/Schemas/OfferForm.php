<?php

namespace App\Filament\Resources\Offers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
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

class OfferForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                Grid::make()
                    ->columnSpan([
                        'default' => 12,
                        'lg' => 8,
                    ])
                    ->schema([
                        Section::make('Offer Content')
                            ->columnSpanFull()
                            ->columns(2)
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, ?string $state): void {
                                        $set('slug', Str::slug($state ?? ''));
                                    }),

                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(191)
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

                                DatePicker::make('valid_start_date')
                                    ->label('Valid Start Date')
                                    ->native(false)
                                    ->helperText('Offer will start showing from this date.'),

                                DatePicker::make('valid_end_date')
                                    ->label('Valid End Date')
                                    ->native(false)
                                    ->helperText('Offer will stop showing after this date.'),

                                TextInput::make('button_label')
                                    ->label('Button Label')
                                    ->placeholder('Example: Book Now')
                                    ->maxLength(255),

                                TextInput::make('button_url')
                                    ->label('Button URL / Route Name')
                                    ->placeholder('Example: offers.index or https://...')
                                    ->maxLength(255),
                            ]),

                        Section::make('Booking Engine')
                            ->columnSpanFull()
                            ->columns(2)
                            ->schema([
                                DatePicker::make('booking_checkin_date')
                                    ->label('Check-in Date')
                                    ->native(false)
                                    ->helperText('Used for the booking engine URL.'),

                                TextInput::make('booking_nights')
                                    ->label('Nights')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(30)
                                    ->placeholder('Example: 2'),

                                TextInput::make('booking_rooms')
                                    ->label('Rooms')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(10)
                                    ->placeholder('Example: 1'),

                                TextInput::make('booking_adults')
                                    ->label('Adults')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(20)
                                    ->placeholder('Example: 2'),

                                TextInput::make('booking_rate_code')
                                    ->label('Rate Code')
                                    ->placeholder('Example: 942373')
                                    ->maxLength(100),

                                TextInput::make('booking_bkcode')
                                    ->label('BK Code')
                                    ->placeholder('Example: promo code / booking code')
                                    ->maxLength(100),

                                Textarea::make('booking_url_override')
                                    ->label('Booking URL Override')
                                    ->placeholder('Paste full booking engine URL only if this offer needs a special URL.')
                                    ->rows(3)
                                    ->columnSpanFull()
                                    ->helperText('If filled, this URL will be used instead of the generated booking URL.'),
                            ]),

                        Section::make('SEO')
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('meta_title')
                                    ->label('Meta Title')
                                    ->maxLength(255),

                                Textarea::make('meta_description')
                                    ->label('Meta Description')
                                    ->rows(3),
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
                                    ->label('Desktop Hero Image')
                                    ->disk('public')
                                    ->directory('offers/hero')
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
                                            directory: 'offers/hero',
                                            targetWidth: 1600,
                                            targetHeight: 900,
                                        )
                                    ),

                                TextInput::make('hero_image_alt')
                                    ->label('Desktop Hero Alt Text')
                                    ->placeholder('Describe the desktop hero image')
                                    ->maxLength(255),

                                FileUpload::make('hero_mobile_image')
                                    ->label('Mobile Hero Image')
                                    ->disk('public')
                                    ->directory('offers/hero-mobile')
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
                                            directory: 'offers/hero-mobile',
                                            targetWidth: 1200,
                                            targetHeight: 900,
                                        )
                                    ),

                                TextInput::make('hero_mobile_image_alt')
                                    ->label('Mobile Hero Alt Text')
                                    ->placeholder('Describe the mobile hero image')
                                    ->maxLength(255),
                            ]),

                        Section::make('Card Image')
                            ->columnSpanFull()
                            ->schema([
                                FileUpload::make('card_image')
                                    ->label('Offer Card Image')
                                    ->disk('public')
                                    ->directory('offers/cards')
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
                                            directory: 'offers/cards',
                                            targetWidth: 1200,
                                            targetHeight: 800,
                                        )
                                    ),

                                TextInput::make('card_image_alt')
                                    ->label('Card Image Alt Text')
                                    ->placeholder('Describe the offer card image')
                                    ->maxLength(255),
                            ]),

                        Section::make('Settings')
                            ->columnSpanFull()
                            ->schema([
                                Toggle::make('is_featured')
                                    ->label('Featured')
                                    ->default(false)
                                    ->required(),

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

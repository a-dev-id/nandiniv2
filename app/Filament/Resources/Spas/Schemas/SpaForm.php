<?php

namespace App\Filament\Resources\Spas\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
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

class SpaForm
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
                        Section::make('Spa Content')
                            ->columnSpanFull()
                            ->columns(2)
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(191)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, ?string $state): void {
                                        $set('slug', Str::slug($state ?? ''));
                                    }),

                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(191)
                                    ->unique(ignoreRecord: true),

                                TextInput::make('subtitle')
                                    ->maxLength(191)
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
                                    ->helperText('Spa package will start showing from this date.'),

                                DatePicker::make('valid_end_date')
                                    ->label('Valid End Date')
                                    ->native(false)
                                    ->helperText('Spa package will stop showing after this date.'),

                                TextInput::make('button_label')
                                    ->label('Button Label')
                                    ->placeholder('Example: Book Now')
                                    ->maxLength(191),

                                TextInput::make('button_url')
                                    ->label('Button URL / Route Name')
                                    ->placeholder('Example: spa.index or https://...')
                                    ->maxLength(191),
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
                                    ->placeholder('Paste full booking engine URL only if this spa package needs a special URL.')
                                    ->rows(3)
                                    ->columnSpanFull()
                                    ->helperText('If filled, this URL will be used instead of the generated booking URL.'),
                            ]),

                        Section::make('SEO')
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('meta_title')
                                    ->label('Meta Title')
                                    ->maxLength(191),

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
                                    ->directory('spas/hero')
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
                                            directory: 'spas/hero',
                                            targetWidth: 1600,
                                            targetHeight: 900,
                                        )
                                    ),

                                TextInput::make('hero_image_alt')
                                    ->label('Desktop Hero Alt Text')
                                    ->placeholder('Describe the desktop hero image')
                                    ->maxLength(191),

                                FileUpload::make('hero_mobile_image')
                                    ->label('Mobile Hero Image')
                                    ->disk('public')
                                    ->directory('spas/hero-mobile')
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
                                            directory: 'spas/hero-mobile',
                                            targetWidth: 1200,
                                            targetHeight: 900,
                                        )
                                    ),

                                TextInput::make('hero_mobile_image_alt')
                                    ->label('Mobile Hero Alt Text')
                                    ->placeholder('Describe the mobile hero image')
                                    ->maxLength(191),
                            ]),

                        Section::make('Card Image')
                            ->columnSpanFull()
                            ->schema([
                                FileUpload::make('card_image')
                                    ->label('Spa Card Image')
                                    ->disk('public')
                                    ->directory('spas/cards')
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
                                            directory: 'spas/cards',
                                            targetWidth: 1200,
                                            targetHeight: 800,
                                        )
                                    ),

                                TextInput::make('card_image_alt')
                                    ->label('Card Image Alt Text')
                                    ->placeholder('Describe the spa card image')
                                    ->maxLength(191),
                            ]),

                        Section::make('Status')
                            ->columnSpanFull()
                            ->schema([
                                Toggle::make('is_featured')
                                    ->label('Featured')
                                    ->default(false),

                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true),

                                TextInput::make('sort_order')
                                    ->label('Sort Order')
                                    ->numeric()
                                    ->default(0)
                                    ->required(),
                            ]),
                    ]),
            ]);
    }

    protected static function storeAsWebp(
        TemporaryUploadedFile $file,
        string $directory,
        int $targetWidth,
        int $targetHeight
    ): string {
        if (! function_exists('imagecreatetruecolor')) {
            return $file->store($directory, 'public');
        }

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

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);

        $targetRatio = $targetWidth / $targetHeight;
        $sourceRatio = $sourceWidth / $sourceHeight;

        if ($sourceRatio > $targetRatio) {
            $cropHeight = $sourceHeight;
            $cropWidth = (int) round($sourceHeight * $targetRatio);
            $cropX = (int) round(($sourceWidth - $cropWidth) / 2);
            $cropY = 0;
        } else {
            $cropWidth = $sourceWidth;
            $cropHeight = (int) round($sourceWidth / $targetRatio);
            $cropX = 0;
            $cropY = (int) round(($sourceHeight - $cropHeight) / 2);
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

        Storage::disk('public')->makeDirectory($directory);

        $path = trim($directory, '/') . '/' . Str::uuid() . '.webp';
        $fullPath = Storage::disk('public')->path($path);

        imagewebp($targetImage, $fullPath, 85);

        imagedestroy($sourceImage);
        imagedestroy($targetImage);

        return $path;
    }
}

<?php

namespace App\Filament\Resources\Pages\Schemas;

use App\Models\Page;
use App\Support\FilamentWebpUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                Section::make('Page Content')
                    ->columnSpan([
                        'default' => 12,
                        'lg' => 8,
                    ])
                    ->columns(2)
                    ->schema([
                        TextInput::make('page_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Page Name')
                            ->columnSpanFull()
                            ->helperText('Unique name for internal reference, e.g., "Home", "About Us".'),

                        Select::make('site')
                            ->label('Website')
                            ->options([
                                Page::SITE_MAIN => 'Main Website',
                                Page::SITE_SPA => 'Spa Website',
                            ])
                            ->default(Page::SITE_MAIN)
                            ->required(),

                        TextInput::make('title')
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
                                    ->directory('pages/hero')
                                    ->visibility('public')
                                    ->image()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->imagePreviewHeight('160')
                                    ->panelAspectRatio('16:9')
                                    ->panelLayout('integrated')
                                    ->openable()
                                    ->downloadable()
                                    ->saveUploadedFileUsing(
                                        fn(TemporaryUploadedFile $file, Get $get): string => FilamentWebpUpload::store(
                                            file: $file,
                                            directory: 'pages/hero',
                                            targetWidth: 1600,
                                            targetHeight: 900,
                                            fileName: $get('hero_image_file_name'),
                                        )
                                    ),

                                TextInput::make('hero_image_file_name')
                                    ->label('Desktop File Name')
                                    ->placeholder('example-desktop-hero')
                                    ->helperText('Optional. Saved as .webp; leave blank for automatic name.')
                                    ->maxLength(120)
                                    ->dehydrated(false),

                                TextInput::make('hero_image_alt')
                                    ->label('Desktop Alt Text')
                                    ->placeholder('Example: Jungle resort villa surrounded by tropical forest')
                                    ->maxLength(255),

                                FileUpload::make('hero_mobile_image')
                                    ->label('Mobile')
                                    ->disk('public')
                                    ->directory('pages/hero-mobile')
                                    ->visibility('public')
                                    ->image()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->imagePreviewHeight('160')
                                    ->panelAspectRatio('4:3')
                                    ->panelLayout('integrated')
                                    ->openable()
                                    ->downloadable()
                                    ->saveUploadedFileUsing(
                                        fn(TemporaryUploadedFile $file, Get $get): string => FilamentWebpUpload::store(
                                            file: $file,
                                            directory: 'pages/hero-mobile',
                                            targetWidth: 1200,
                                            targetHeight: 900,
                                            fileName: $get('hero_mobile_image_file_name'),
                                        )
                                    ),

                                TextInput::make('hero_mobile_image_file_name')
                                    ->label('Mobile File Name')
                                    ->placeholder('example-mobile-hero')
                                    ->helperText('Optional. Saved as .webp; leave blank for automatic name.')
                                    ->maxLength(120)
                                    ->dehydrated(false),

                                TextInput::make('hero_mobile_image_alt')
                                    ->label('Mobile Alt Text')
                                    ->placeholder('Example: Nandini Jungle resort view on mobile hero image')
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

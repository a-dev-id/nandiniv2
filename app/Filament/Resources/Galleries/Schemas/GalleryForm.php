<?php

namespace App\Filament\Resources\Galleries\Schemas;

use App\Support\FilamentWebpUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
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

class GalleryForm
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
                        Section::make('Gallery Content')
                            ->columnSpanFull()
                            ->columns(2)
                            ->schema([
                                TextInput::make('title')
                                    ->label('Title')
                                    ->required()
                                    ->maxLength(191)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, ?string $state): void {
                                        $set('slug', Str::slug($state ?? ''));
                                    }),

                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->maxLength(191)
                                    ->unique(ignoreRecord: true),

                                TextInput::make('category')
                                    ->label('Category')
                                    ->placeholder('Example: Resort, Dining, Spa, Wedding')
                                    ->maxLength(191)
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
                            ]),

                        Section::make('Button')
                            ->columnSpanFull()
                            ->columns(2)
                            ->schema([
                                TextInput::make('button_label')
                                    ->label('Button Label')
                                    ->placeholder('Example: View Gallery')
                                    ->maxLength(191),

                                TextInput::make('button_url')
                                    ->label('Button URL')
                                    ->placeholder('Example: https://...')
                                    ->maxLength(255),
                            ]),

                        Section::make('SEO')
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('meta_title')
                                    ->label('Meta Title')
                                    ->maxLength(191),

                                Textarea::make('meta_description')
                                    ->label('Meta Description')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Grid::make()
                    ->columnSpan([
                        'default' => 12,
                        'lg' => 4,
                    ])
                    ->schema([
                        Section::make('Gallery Images')
                            ->columnSpanFull()
                            ->schema([
                                FileUpload::make('image')
                                    ->label('Desktop Image')
                                    ->disk('public')
                                    ->directory('gallery/images')
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
                                            directory: 'gallery/images',
                                            targetWidth: 1600,
                                            targetHeight: 900,
                                            fileName: $get('image_file_name'),
                                        )
                                    ),

                                TextInput::make('image_file_name')
                                    ->label('Desktop Image File Name')
                                    ->placeholder('example-gallery-image')
                                    ->helperText('Optional. Saved as .webp; leave blank for automatic name.')
                                    ->maxLength(120)
                                    ->dehydrated(false),

                                TextInput::make('image_alt')
                                    ->label('Desktop Image Alt Text')
                                    ->placeholder('Describe the desktop image')
                                    ->maxLength(191),

                                FileUpload::make('mobile_image')
                                    ->label('Mobile Image')
                                    ->disk('public')
                                    ->directory('gallery/mobile')
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
                                            directory: 'gallery/mobile',
                                            targetWidth: 1200,
                                            targetHeight: 900,
                                            fileName: $get('mobile_image_file_name'),
                                        )
                                    ),

                                TextInput::make('mobile_image_file_name')
                                    ->label('Mobile Image File Name')
                                    ->placeholder('example-gallery-mobile')
                                    ->helperText('Optional. Saved as .webp; leave blank for automatic name.')
                                    ->maxLength(120)
                                    ->dehydrated(false),

                                TextInput::make('mobile_image_alt')
                                    ->label('Mobile Image Alt Text')
                                    ->placeholder('Describe the mobile image')
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

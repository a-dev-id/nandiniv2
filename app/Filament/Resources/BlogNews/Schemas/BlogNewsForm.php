<?php

namespace App\Filament\Resources\BlogNews\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
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

class BlogNewsForm
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
                        Section::make('Blog / News Content')
                            ->columnSpanFull()
                            ->columns(2)
                            ->schema([
                                Select::make('type')
                                    ->label('Type')
                                    ->required()
                                    ->native(false)
                                    ->options([
                                        'blog' => 'Blog',
                                        'news' => 'News',
                                    ])
                                    ->default('blog'),

                                DatePicker::make('published_at')
                                    ->label('Published Date')
                                    ->native(false)
                                    ->default(today()),

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

                                TextInput::make('subtitle')
                                    ->label('Subtitle')
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

                                TextInput::make('author_name')
                                    ->label('Author Name')
                                    ->placeholder('Example: Nandini Jungle by Hanging Gardens')
                                    ->maxLength(191),

                                TextInput::make('button_label')
                                    ->label('Button Label')
                                    ->placeholder('Example: Read More')
                                    ->maxLength(191),

                                TextInput::make('button_url')
                                    ->label('Button URL')
                                    ->placeholder('Example: https://...')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
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
                        Section::make('Hero Images')
                            ->columnSpanFull()
                            ->schema([
                                FileUpload::make('hero_image')
                                    ->label('Desktop Hero Image')
                                    ->disk('public')
                                    ->directory('blog-news/hero')
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
                                            directory: 'blog-news/hero',
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
                                    ->directory('blog-news/hero-mobile')
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
                                            directory: 'blog-news/hero-mobile',
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
                                    ->label('Card Image')
                                    ->disk('public')
                                    ->directory('blog-news/cards')
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
                                            directory: 'blog-news/cards',
                                            targetWidth: 1200,
                                            targetHeight: 800,
                                        )
                                    ),

                                TextInput::make('card_image_alt')
                                    ->label('Card Image Alt Text')
                                    ->placeholder('Describe the card image')
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

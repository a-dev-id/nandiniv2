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
                                'image_overlay_section' => 'Image Overlay Section',
                                'split_media_section' => 'Split Media Section',
                                'split_media_reverse' => 'Split Media Reverse',
                                'three_images_section' => 'Three Images Section',
                                'two_images_section' => 'Two Images Section',
                                'two_images_reverse' => 'Two Images Reverse',
                            ])
                            ->default('intro_text_section'),

                        TextInput::make('title')
                            ->label('Title')
                            ->placeholder('Section title')
                            ->maxLength(255),

                        TextInput::make('subtitle')
                            ->label('Subtitle')
                            ->maxLength(255)
                            ->visible(fn(Get $get): bool => in_array($get('section_key'), self::MEDIA_SECTION_KEYS, true))
                            ->columnSpanFull(),

                        Textarea::make('excerpt')
                            ->label('Excerpt')
                            ->rows(3)
                            ->visible(fn(Get $get): bool => in_array($get('section_key'), self::MEDIA_SECTION_KEYS, true))
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
                            ->visible(fn(Get $get): bool => in_array($get('section_key'), self::MEDIA_SECTION_KEYS, true))
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
                            ->visible(fn(Get $get): bool => $get('section_key') === 'image_overlay_section')
                            ->schema([
                                Select::make('text_align')
                                    ->label('Text Align')
                                    ->native(false)
                                    ->options([
                                        'left' => 'Left',
                                        'center' => 'Center',
                                        'right' => 'Right',
                                    ])
                                    ->default('center')
                                    ->required(),
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

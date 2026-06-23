<?php

namespace App\Filament\Resources\AccommodationFeatures\Schemas;

use App\Support\FilamentWebpUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class AccommodationFeatureForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Feature Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('label')
                            ->label('Feature Label')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Example: King size bed')
                            ->columnSpanFull(),

                        FileUpload::make('icon_image')
                            ->label('Icon Image')
                            ->disk('public')
                            ->directory('accommodations/features/icons')
                            ->visibility('public')
                            ->image()
                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                                'image/svg+xml',
                            ])
                            ->imagePreviewHeight('80')
                            ->panelAspectRatio('1:1')
                            ->panelLayout('integrated')
                            ->openable()
                            ->downloadable()
                            ->maxSize(1024)
                            ->saveUploadedFileUsing(
                                fn(TemporaryUploadedFile $file, Get $get): string => FilamentWebpUpload::storeOriginal(
                                    file: $file,
                                    directory: 'accommodations/features/icons',
                                    fileName: $get('icon_image_file_name'),
                                )
                            )
                            ->helperText('Recommended: SVG, PNG, or WebP icon.')
                            ->columnSpanFull(),

                        TextInput::make('icon_image_file_name')
                            ->label('Icon File Name')
                            ->placeholder('example-feature-icon')
                            ->helperText('Optional. Leave blank for automatic name.')
                            ->maxLength(120)
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                        Hidden::make('sort_order')
                            ->default(0),
                    ]),
            ]);
    }
}

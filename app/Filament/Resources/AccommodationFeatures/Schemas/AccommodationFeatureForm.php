<?php

namespace App\Filament\Resources\AccommodationFeatures\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

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
                            ->helperText('Recommended: SVG, PNG, or WebP icon.')
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

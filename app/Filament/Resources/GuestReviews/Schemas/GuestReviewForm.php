<?php

namespace App\Filament\Resources\GuestReviews\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GuestReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                Section::make('Review Content')
                    ->columnSpan([
                        'default' => 12,
                        'lg' => 8,
                    ])
                    ->columns(2)
                    ->schema([
                        TextInput::make('reviewer_name')
                            ->label('Guest Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('source')
                            ->label('Review Source')
                            ->placeholder('Example: Tripadvisor or Google')
                            ->maxLength(255),

                        Textarea::make('excerpt')
                            ->label('Excerpt')
                            ->required()
                            ->rows(5)
                            ->maxLength(1000)
                            ->helperText('Short review text shown in the homepage slider.')
                            ->columnSpanFull(),

                        RichEditor::make('review_text')
                            ->label('Review')
                            ->required()
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline', 'strike', 'link'],
                                ['h2', 'h3'],
                                ['bulletList', 'orderedList'],
                                ['blockquote'],
                                ['undo', 'redo'],
                            ])
                            ->helperText('Full review shown on the Guest Reviews page.')
                            ->columnSpanFull(),
                    ]),

                Grid::make()
                    ->columnSpan([
                        'default' => 12,
                        'lg' => 4,
                    ])
                    ->schema([
                        Section::make('Review Details')
                            ->columnSpanFull()
                            ->schema([
                                Select::make('rating')
                                    ->label('Rating')
                                    ->options([
                                        5 => '5 Stars',
                                        4 => '4 Stars',
                                        3 => '3 Stars',
                                        2 => '2 Stars',
                                        1 => '1 Star',
                                    ])
                                    ->default(5)
                                    ->required()
                                    ->native(false),

                                DatePicker::make('reviewed_at')
                                    ->label('Review Date')
                                    ->native(false),
                            ]),

                        Section::make('Settings')
                            ->columnSpanFull()
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true)
                                    ->required(),

                                Toggle::make('is_featured')
                                    ->label('Featured')
                                    ->helperText('Only featured reviews are shown on the homepage.')
                                    ->default(false),
                            ]),
                    ]),
            ]);
    }
}

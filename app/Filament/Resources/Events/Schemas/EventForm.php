<?php

namespace App\Filament\Resources\Events\Schemas;

use App\Enums\EventStatus;
use App\Enums\EventType;
use App\Support\FilamentWebpUpload;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class EventForm
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
                        Section::make('Event Content')
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('subtitle')
                                    ->maxLength(255),

                                Textarea::make('excerpt')
                                    ->rows(3)
                                    ->maxLength(1000)
                                    ->helperText("Optional summary shown when the event is featured as today's event."),

                                RichEditor::make('description')
                                    ->toolbarButtons([
                                        ['bold', 'italic', 'underline', 'strike', 'link'],
                                        ['h2', 'h3'],
                                        ['bulletList', 'orderedList'],
                                        ['blockquote'],
                                        ['undo', 'redo'],
                                    ]),
                            ]),

                        Section::make('Schedule')
                            ->columnSpanFull()
                            ->columns(2)
                            ->schema([
                                DateTimePicker::make('event_start_at')
                                    ->label('Event Start')
                                    ->native(false)
                                    ->seconds(false)
                                    ->beforeOrEqual('event_end_at')
                                    ->helperText('Optional. Regular events remain visible without a start date.'),

                                DateTimePicker::make('event_end_at')
                                    ->label('Event End')
                                    ->native(false)
                                    ->seconds(false)
                                    ->afterOrEqual('event_start_at')
                                    ->helperText('Optional. Leave blank when the event has no fixed end date.'),

                                Select::make('event_type')
                                    ->label('Event Type')
                                    ->options(EventType::options())
                                    ->default(EventType::Regular->value)
                                    ->required()
                                    ->native(false)
                                    ->helperText('Regular events always appear in the Regular Events section.'),

                                TextInput::make('schedule_text')
                                    ->label('Schedule Text')
                                    ->placeholder('Start from 7:00 PM - 9:00 PM')
                                    ->maxLength(255)
                                    ->helperText('Optional custom schedule shown on the event card. Times entered in 24-hour format are displayed with AM/PM.'),

                                Toggle::make('status')
                                    ->label('Published')
                                    ->default(false)
                                    ->inline(false)
                                    ->formatStateUsing(fn (EventStatus|string|null $state): bool => match (true) {
                                        $state instanceof EventStatus => $state === EventStatus::Published,
                                        default => $state === EventStatus::Published->value,
                                    })
                                    ->dehydrateStateUsing(fn (bool $state): string => $state
                                        ? EventStatus::Published->value
                                        : EventStatus::Draft->value)
                                    ->helperText('Turn on to display this event on the public Events page.'),
                            ]),
                    ]),

                Grid::make()
                    ->columnSpan([
                        'default' => 12,
                        'lg' => 4,
                    ])
                    ->schema([
                        Section::make('Event Image')
                            ->columnSpanFull()
                            ->schema([
                                FileUpload::make('image')
                                    ->required()
                                    ->disk('public')
                                    ->directory('events')
                                    ->visibility('public')
                                    ->image()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->imagePreviewHeight('360')
                                    ->panelAspectRatio('12:17')
                                    ->panelLayout('integrated')
                                    ->helperText('Upload a portrait event flyer. The image is saved at 600 × 850 pixels.')
                                    ->openable()
                                    ->downloadable()
                                    ->saveUploadedFileUsing(
                                        fn (TemporaryUploadedFile $file, Get $get): string => FilamentWebpUpload::store(
                                            file: $file,
                                            directory: 'events',
                                            targetWidth: 600,
                                            targetHeight: 850,
                                            fileName: $get('image_name'),
                                        )
                                    ),

                                TextInput::make('image_name')
                                    ->label('Image Name')
                                    ->placeholder('example-event-image')
                                    ->helperText('Optional. The uploaded image is saved as WebP; leave blank for an automatic name.')
                                    ->maxLength(120)
                                    ->afterStateHydrated(function (TextInput $component, Get $get, ?string $state): void {
                                        if (filled($state) || blank($get('image'))) {
                                            return;
                                        }

                                        $component->state(pathinfo((string) $get('image'), PATHINFO_FILENAME));
                                    }),

                                TextInput::make('alt_text')
                                    ->label('Alt Text')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Describe the image for accessibility and search engines.'),
                            ]),
                    ]),
            ]);
    }
}

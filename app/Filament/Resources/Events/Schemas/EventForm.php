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
                                    ->helperText('The manually entered date and time for this event occurrence.'),

                                DateTimePicker::make('event_end_at')
                                    ->label('Event End')
                                    ->native(false)
                                    ->seconds(false)
                                    ->afterOrEqual('event_start_at')
                                    ->helperText('After this date and time, the event is automatically hidden from the public website.'),

                                Select::make('event_type')
                                    ->label('Event Type')
                                    ->options(EventType::options())
                                    ->default(EventType::Regular->value)
                                    ->required()
                                    ->native(false)
                                    ->helperText('One-time events never repeat. The event schedule cron advances active weekly, monthly, and yearly events to their next occurrence.'),

                                TextInput::make('schedule_text')
                                    ->label('Schedule Text')
                                    ->placeholder('Start from 7:00 PM - 9:00 PM')
                                    ->maxLength(255)
                                    ->helperText('Optional custom schedule shown on the event card. Times entered in 24-hour format are displayed with AM/PM.'),

                                Toggle::make('is_dish_of_month')
                                    ->label('Dish of the Month')
                                    ->default(false)
                                    ->live()
                                    ->inline(false)
                                    ->onColor('warning')
                                    ->helperText('Feature this record between Today’s Event and Upcoming Events. Enabling it replaces the previous Dish of the Month.'),

                                Toggle::make('status')
                                    ->label('Active')
                                    ->default(false)
                                    ->inline(false)
                                    ->formatStateUsing(fn (EventStatus|string|null $state): bool => match (true) {
                                        $state instanceof EventStatus => $state === EventStatus::Published,
                                        default => $state === EventStatus::Published->value,
                                    })
                                    ->dehydrateStateUsing(fn (bool $state): string => $state
                                        ? EventStatus::Published->value
                                        : EventStatus::Draft->value)
                                    ->onColor('warning')
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
                                    ->panelAspectRatio(fn (Get $get): string => $get('is_dish_of_month') ? '16:9' : '12:17')
                                    ->panelLayout('integrated')
                                    ->imageEditor()
                                    ->imageEditorAspectRatioOptions(fn (Get $get): array => [
                                        $get('is_dish_of_month') ? '16:9' : '12:17',
                                        null,
                                    ])
                                    ->helperText(fn (Get $get): string => $get('is_dish_of_month')
                                        ? 'Upload a horizontal Dish of the Month image. Recommended size: 2048 × 1152 pixels (16:9).'
                                        : 'Upload a portrait event flyer. The image is saved at 600 × 850 pixels.')
                                    ->openable()
                                    ->downloadable()
                                    ->saveUploadedFileUsing(
                                        function (TemporaryUploadedFile $file, Get $get): string {
                                            $isDishOfTheMonth = (bool) $get('is_dish_of_month');

                                            return FilamentWebpUpload::store(
                                                file: $file,
                                                directory: 'events',
                                                targetWidth: $isDishOfTheMonth ? 2048 : 600,
                                                targetHeight: $isDishOfTheMonth ? 1152 : 850,
                                                fileName: $get('image_name'),
                                            );
                                        }
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

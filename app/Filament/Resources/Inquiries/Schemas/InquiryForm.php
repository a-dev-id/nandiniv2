<?php

namespace App\Filament\Resources\Inquiries\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InquiryForm
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
                        Section::make('Guest Details')
                            ->columns(2)
                            ->schema([
                                TextInput::make('guest_name')
                                    ->label('Guest Name')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->afterStateHydrated(fn(TextInput $component, $record): mixed => $component->state($record?->full_name)),

                                TextInput::make('email')
                                    ->email()
                                    ->disabled(),

                                TextInput::make('country')
                                    ->disabled(),

                                TextInput::make('phone_display')
                                    ->label('Phone/WA')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->afterStateHydrated(fn(TextInput $component, $record): mixed => $component->state($record?->phone_wa)),
                            ]),

                        Section::make('Booking Request')
                            ->columns(2)
                            ->schema([
                                TextInput::make('inquiry_title')
                                    ->label('Inquiry Title')
                                    ->disabled()
                                    ->columnSpanFull(),

                                Textarea::make('inquiry_image')
                                    ->label('Inquiry Image')
                                    ->rows(2)
                                    ->disabled()
                                    ->columnSpanFull(),

                                DatePicker::make('reserve_date')
                                    ->label('Reserve Date')
                                    ->native(false)
                                    ->disabled(),

                                TimePicker::make('reserve_time')
                                    ->label('Reserve Time')
                                    ->native(false)
                                    ->seconds(false)
                                    ->disabled(),
                            ]),

                        Section::make('Inquiry Note')
                            ->schema([
                                Textarea::make('note')
                                    ->rows(5)
                                    ->disabled()
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Grid::make()
                    ->columnSpan([
                        'default' => 12,
                        'lg' => 4,
                    ])
                    ->schema([
                        Section::make('Status')
                            ->schema([
                                Toggle::make('is_read')
                                    ->label('Read'),

                                DateTimePicker::make('submitted_at')
                                    ->native(false)
                                    ->disabled(),

                                DateTimePicker::make('email_sent_at')
                                    ->label('Email Sent At')
                                    ->native(false)
                                    ->disabled(),
                            ]),

                        Section::make('Source')
                            ->schema([
                                Textarea::make('source_url')
                                    ->rows(3)
                                    ->disabled(),

                                TextInput::make('ip_address')
                                    ->label('IP Address')
                                    ->disabled(),

                                Textarea::make('user_agent')
                                    ->rows(3)
                                    ->disabled(),
                            ]),

                        Section::make('Email Error')
                            ->schema([
                                Textarea::make('email_error')
                                    ->rows(4)
                                    ->disabled(),
                            ])
                            ->visible(fn($record): bool => filled($record?->email_error)),
                    ]),
            ]);
    }
}

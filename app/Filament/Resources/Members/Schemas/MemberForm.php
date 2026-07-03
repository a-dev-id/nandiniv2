<?php

namespace App\Filament\Resources\Members\Schemas;

use App\Models\Member;
use App\Support\FilamentWebpUpload;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class MemberForm
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
                        Section::make('Member Details')
                            ->columns(2)
                            ->schema([
                                TextInput::make('first_name')
                                    ->maxLength(100),

                                TextInput::make('last_name')
                                    ->maxLength(100),

                                TextInput::make('name')
                                    ->label('Display Name')
                                    ->maxLength(150)
                                    ->columnSpanFull(),

                                TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),

                                TextInput::make('password')
                                    ->label('Temporary Password')
                                    ->password()
                                    ->revealable()
                                    ->required(fn(string $operation): bool => $operation === 'create')
                                    ->visible(fn(string $operation): bool => $operation === 'create')
                                    ->maxLength(255)
                                    ->helperText('The member must change this password after first login.'),

                                TextInput::make('phone_number')
                                    ->label('Phone / WhatsApp')
                                    ->maxLength(50),

                                DatePicker::make('date_of_birth')
                                    ->native(false)
                                    ->maxDate(now()->subDay()),

                                TextInput::make('country')
                                    ->maxLength(100),

                                Textarea::make('address')
                                    ->rows(3)
                                    ->maxLength(1000)
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Stay Dates')
                            ->columns(2)
                            ->schema([
                                DatePicker::make('booking_check_in')
                                    ->label('Check In')
                                    ->native(false),

                                DatePicker::make('booking_check_out')
                                    ->label('Check Out')
                                    ->native(false)
                                    ->helperText('Used for the daily checkout notification. Update this date if the guest extends their stay.'),
                            ]),

                        Section::make('Membership')
                            ->columns(2)
                            ->schema([
                                Select::make('tier')
                                    ->options([
                                        Member::TIER_BRONZE => 'Dana',
                                        Member::TIER_SILVER => 'Upaya',
                                        Member::TIER_GOLD => 'Dhyana',
                                        Member::TIER_PLATINUM => 'Jnana',
                                    ])
                                    ->default(Member::TIER_BRONZE)
                                    ->required(),

                                TextInput::make('points')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->helperText('Use the table action to add or deduct points so transaction history stays accurate.'),

                                DateTimePicker::make('membership_started_at')
                                    ->default(now())
                                    ->native(false),

                                DateTimePicker::make('membership_expires_at')
                                    ->default(now()->addYear())
                                    ->native(false),

                                Toggle::make('must_change_password')
                                    ->label('Must Change Password')
                                    ->default(true),

                                Toggle::make('marketing_consent')
                                    ->label('Marketing Consent'),

                                DateTimePicker::make('email_verified_at')
                                    ->label('Email Verified At')
                                    ->default(now())
                                    ->native(false),
                            ]),
                    ]),

                Grid::make()
                    ->columnSpan([
                        'default' => 12,
                        'lg' => 4,
                    ])
                    ->schema([
                        Section::make('Profile Photo')
                            ->schema([
                                FileUpload::make('profile_photo')
                                    ->disk('public')
                                    ->directory('members/profile-photos')
                                    ->visibility('public')
                                    ->image()
                                    ->imagePreviewHeight('180')
                                    ->openable()
                                    ->downloadable()
                                    ->saveUploadedFileUsing(
                                        fn(TemporaryUploadedFile $file, Get $get): string => FilamentWebpUpload::storeOriginal(
                                            file: $file,
                                            directory: 'members/profile-photos',
                                            fileName: $get('profile_photo_file_name'),
                                        )
                                    ),

                                TextInput::make('profile_photo_file_name')
                                    ->label('Profile Photo File Name')
                                    ->placeholder('example-profile-photo')
                                    ->helperText('Optional. Leave blank for automatic name.')
                                    ->maxLength(120)
                                    ->dehydrated(false),
                            ]),

                        Section::make('Source')
                            ->schema([
                                Select::make('member_source')
                                    ->options([
                                        Member::SOURCE_AUTO_JOIN => 'Auto Joined From Booking',
                                        Member::SOURCE_MANUAL_REGISTER => 'Manual Register',
                                    ])
                                    ->default(Member::SOURCE_MANUAL_REGISTER),

                                DateTimePicker::make('created_at')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->native(false),

                                DateTimePicker::make('updated_at')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->native(false),

                                DateTimePicker::make('last_login_at')
                                    ->label('Last Login At')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->native(false),

                                DateTimePicker::make('checkout_notification_sent_at')
                                    ->label('Checkout Notice Sent At')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->native(false),
                            ]),
                    ]),
            ]);
    }
}

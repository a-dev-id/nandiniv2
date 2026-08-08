<?php

namespace App\Filament\Resources\Affiliates\Schemas;

use App\Models\Affiliate;
use App\Rules\SocialProfile;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rule;

class AffiliateForm
{
    public static function configure(Schema $schema): Schema
    {
        $socials = ['instagram', 'facebook', 'tiktok', 'x', 'threads'];

        return $schema->components([
            Section::make('Account and contact')
                ->description('The affiliate will use this email to set a password and sign in. No password is created or displayed here.')
                ->schema([
                    TextInput::make('name')->required()->maxLength(255),
                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->rules(fn (?Affiliate $record): array => [Rule::unique('affiliates', 'email')->ignore($record?->getKey())]),
                    TextInput::make('phone_whatsapp')
                        ->label('Phone / WhatsApp')
                        ->required()
                        ->maxLength(50)
                        ->regex('/^[+0-9\s()\-]+$/'),
                ])->columns(2),

            Section::make('Social profiles')
                ->description('At least one profile is required. Enter an @username or a full profile URL.')
                ->schema(collect($socials)->map(function (string $field) use ($socials): TextInput {
                    $otherFields = array_values(array_diff($socials, [$field]));

                    return TextInput::make($field)
                        ->label($field === 'x' ? 'X' : ucfirst($field))
                        ->maxLength(255)
                        ->requiredWithoutAll($otherFields)
                        ->rules([new SocialProfile]);
                })->all())
                ->columns(2),
        ]);
    }
}

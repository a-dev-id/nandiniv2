<?php

namespace App\Filament\Resources\MemberRewardRedemptions\Schemas;

use App\Models\MemberRewardRedemption;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MemberRewardRedemptionForm
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
                        Section::make('Redemption')
                            ->columns(2)
                            ->schema([
                        TextInput::make('member_display')
                            ->label('Member')
                            ->disabled()
                            ->dehydrated(false)
                            ->afterStateHydrated(function (TextInput $component, $record): void {
                                $member = $record?->member;

                                $component->state($member
                                    ? trim($member->full_name . ' <' . $member->email . '>')
                                    : '-');
                            }),

                                TextInput::make('reward_name')
                                    ->disabled(),

                                TextInput::make('redemption_code')
                                    ->disabled(),

                                TextInput::make('points_used')
                                    ->numeric()
                                    ->disabled(),

                                Select::make('status')
                                    ->options([
                                        MemberRewardRedemption::STATUS_PENDING => 'Pending',
                                        MemberRewardRedemption::STATUS_USED => 'Used / Accepted',
                                        MemberRewardRedemption::STATUS_CANCELLED => 'Cancelled',
                                        MemberRewardRedemption::STATUS_EXPIRED => 'Expired',
                                    ])
                                    ->required(),
                            ]),

                        Section::make('Request Details')
                            ->schema([
                                Textarea::make('notes')
                                    ->rows(10)
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Grid::make()
                    ->columnSpan([
                        'default' => 12,
                        'lg' => 4,
                    ])
                    ->schema([
                        Section::make('Timeline')
                            ->schema([
                                DateTimePicker::make('used_at')
                                    ->label('Used / Accepted At')
                                    ->native(false),

                                DateTimePicker::make('cancelled_at')
                                    ->native(false),

                                DateTimePicker::make('expires_at')
                                    ->native(false),

                                DateTimePicker::make('created_at')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->native(false),
                            ]),
                    ]),
            ]);
    }
}

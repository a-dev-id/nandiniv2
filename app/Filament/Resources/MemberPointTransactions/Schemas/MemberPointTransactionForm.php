<?php

namespace App\Filament\Resources\MemberPointTransactions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MemberPointTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Point Transaction')
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

                        TextInput::make('type')
                            ->disabled(),

                        TextInput::make('points')
                            ->numeric()
                            ->disabled(),

                        TextInput::make('reference_type')
                            ->disabled(),

                        TextInput::make('reference_id')
                            ->disabled(),

                        DateTimePicker::make('created_at')
                            ->native(false)
                            ->disabled(),

                        Textarea::make('description')
                            ->rows(5)
                            ->disabled()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

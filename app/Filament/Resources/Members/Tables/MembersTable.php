<?php

namespace App\Filament\Resources\Members\Tables;

use App\Models\Member;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class MembersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('profile_photo')
                    ->label('Photo')
                    ->disk('public')
                    ->circular(),

                TextColumn::make('full_name')
                    ->label('Member')
                    ->searchable(['first_name', 'last_name', 'name'])
                    ->description(fn(Member $record): string => $record->email)
                    ->weight('semibold'),

                TextColumn::make('phone_number')
                    ->label('Phone/WA')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('country')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('tier_label')
                    ->label('Tier')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Jnana' => 'success',
                        'Dhyana' => 'warning',
                        'Upaya' => 'info',
                        default => 'gray',
                    })
                    ->sortable(['tier']),

                TextColumn::make('points')
                    ->label('Points')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('membership_expires_at')
                    ->label('Expires')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->label('Joined')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('tier')
                    ->options([
                        Member::TIER_BRONZE => 'Dana',
                        Member::TIER_SILVER => 'Upaya',
                        Member::TIER_GOLD => 'Dhyana',
                        Member::TIER_PLATINUM => 'Jnana',
                    ]),

                SelectFilter::make('member_source')
                    ->label('Source')
                    ->options([
                        Member::SOURCE_AUTO_JOIN => 'Auto Joined From Booking',
                        Member::SOURCE_MANUAL_REGISTER => 'Manual Register',
                    ]),
            ])
            ->recordActions([
                Action::make('addPoints')
                    ->label('Total Consume')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        TextInput::make('total_consumption')
                            ->label('Total Consume')
                            ->numeric()
                            ->minValue(1)
                            ->prefix('IDR')
                            ->helperText('Points are calculated as: total consume / 1.21 x 5% / 1,000.')
                            ->required(),

                        Textarea::make('description')
                            ->label('Reason')
                            ->default('Points for stay on dd/mm/yyyy')
                            ->rows(3)
                            ->required()
                            ->maxLength(500),
                    ])
                    ->action(function (Member $record, array $data): void {
                        $totalConsumption = (float) $data['total_consumption'];
                        $points = Member::calculatePointsFromConsumption($totalConsumption);

                        if ($points < 1) {
                            Notification::make()
                                ->title('No points added')
                                ->body('The total consume amount is too low to generate points.')
                                ->warning()
                                ->send();

                            return;
                        }

                        $record->earnPoints(
                            points: $points,
                            description: $data['description'] ?? 'Admin consumption points',
                            referenceType: 'booking_consumption',
                        );

                        Notification::make()
                            ->title(number_format($points, 0) . ' points added')
                            ->success()
                            ->send();
                    }),

                Action::make('deductPoints')
                    ->label('Deduct Points')
                    ->icon('heroicon-o-minus-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        TextInput::make('points')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(fn(Member $record): int => max((int) $record->points, 1))
                            ->required(),

                        Textarea::make('description')
                            ->label('Reason')
                            ->default('Admin point deduction')
                            ->rows(3)
                            ->required()
                            ->maxLength(500),
                    ])
                    ->action(function (Member $record, array $data): void {
                        $record->redeemPoints(
                            points: (int) $data['points'],
                            description: $data['description'] ?? 'Admin point deduction',
                            referenceType: 'admin_adjustment',
                        );

                        Notification::make()
                            ->title('Points deducted')
                            ->success()
                            ->send();
                    }),

                Action::make('recalculatePoints')
                    ->label('Recalculate')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->action(function (Member $record): void {
                        DB::transaction(fn() => $record->refreshPointsFromTransactions());

                        Notification::make()
                            ->title('Points recalculated')
                            ->success()
                            ->send();
                    }),

                EditAction::make(),
            ]);
    }
}

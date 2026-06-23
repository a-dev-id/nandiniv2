<?php

namespace App\Filament\Resources\MemberPointTransactions\Tables;

use App\Models\MemberPointTransaction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MemberPointTransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('member.full_name')
                    ->label('Member')
                    ->searchable(['members.first_name', 'members.last_name', 'members.name', 'members.email'])
                    ->description(fn(MemberPointTransaction $record): string => $record->member?->email ?? '-')
                    ->weight('semibold'),

                TextColumn::make('type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'earn' => 'success',
                        'redeem' => 'danger',
                        'adjustment' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('points')
                    ->label('Points')
                    ->formatStateUsing(fn(int|string|null $state): string => ((int) $state > 0 ? '+' : '') . number_format((int) $state))
                    ->color(fn(int|string|null $state): string => (int) $state >= 0 ? 'success' : 'danger')
                    ->sortable(),

                TextColumn::make('description')
                    ->searchable()
                    ->limit(55)
                    ->placeholder('-'),

                TextColumn::make('reference_type')
                    ->label('Reference')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('-'),

                TextColumn::make('reference_id')
                    ->label('Reference ID')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'earn' => 'Earn',
                        'redeem' => 'Redeem',
                        'adjustment' => 'Adjustment',
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('View'),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Inquiries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class InquiriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('submitted_at', 'desc')
            ->columns([
                IconColumn::make('is_read')
                    ->label('Read')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('full_name')
                    ->label('Guest')
                    ->searchable(['title', 'first_name', 'last_name'])
                    ->sortable(['first_name', 'last_name'])
                    ->weight('semibold')
                    ->description(fn($record): string => $record->email),

                TextColumn::make('country')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone_wa')
                    ->label('Phone/WA')
                    ->searchable(['phone_code', 'phone']),

                TextColumn::make('inquiry_title')
                    ->label('Inquiry')
                    ->searchable()
                    ->limit(40)
                    ->placeholder('-'),

                TextColumn::make('reserve_date')
                    ->label('Reserve Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('reserve_time')
                    ->label('Time')
                    ->sortable(),

                TextColumn::make('note')
                    ->limit(60)
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('email_status')
                    ->label('Email')
                    ->badge()
                    ->getStateUsing(fn($record): string => $record->email_sent_at ? 'Sent' : ($record->email_error ? 'Failed' : 'Pending'))
                    ->color(fn(string $state): string => match ($state) {
                        'Sent' => 'success',
                        'Failed' => 'danger',
                        default => 'gray',
                    }),

                ToggleColumn::make('is_read')
                    ->label('Read Toggle')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('submitted_at')
                    ->label('Submitted')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('source_url')
                    ->label('Source')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_read')
                    ->label('Read Status')
                    ->placeholder('All inquiries')
                    ->trueLabel('Read')
                    ->falseLabel('Unread'),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('View'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

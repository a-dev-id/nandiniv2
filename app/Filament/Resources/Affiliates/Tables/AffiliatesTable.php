<?php

namespace App\Filament\Resources\Affiliates\Tables;

use App\Enums\AffiliateRegistrationSource;
use App\Enums\AffiliateStatus;
use App\Filament\Resources\Affiliates\AffiliateResource;
use App\Models\Affiliate;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AffiliatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->weight('semibold'),

                TextColumn::make('email')
                    ->searchable(),

                TextColumn::make('phone_whatsapp')
                    ->label('Phone / WhatsApp')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('affiliate_code')
                    ->label('Affiliate Code')
                    ->searchable()
                    ->copyable()
                    ->weight('semibold'),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (AffiliateStatus $state): string => $state->label())
                    ->color(fn (AffiliateStatus $state): string => match ($state) {
                        AffiliateStatus::Approved => 'success',
                        AffiliateStatus::Pending => 'warning',
                        AffiliateStatus::Rejected => 'danger',
                        AffiliateStatus::Suspended => 'gray',
                    }),

                TextColumn::make('registration_source')
                    ->label('Registration Source')
                    ->formatStateUsing(fn (AffiliateRegistrationSource $state): string => $state->label()),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('approved_at')
                    ->label('Approved')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-')
                    ->sortable(),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->placeholder('Self registration'),

                TextColumn::make('approver.name')
                    ->label('Approved By')
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(collect(AffiliateStatus::cases())->mapWithKeys(
                        fn (AffiliateStatus $status): array => [$status->value => $status->label()]
                    )->all()),

                SelectFilter::make('registration_source')
                    ->label('Registration Source')
                    ->options(collect(AffiliateRegistrationSource::cases())->mapWithKeys(
                        fn (AffiliateRegistrationSource $source): array => [$source->value => $source->label()]
                    )->all()),
            ])
            ->recordActions([
                AffiliateResource::approveAction(),
                AffiliateResource::rejectAction(),
                ViewAction::make(),
                EditAction::make()->visible(fn (Affiliate $record): bool => $record->isPending()),
            ]);
    }
}

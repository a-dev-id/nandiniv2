<?php

namespace App\Filament\Resources\SyncedWebhotelierBookings\Tables;

use App\Models\Member;
use App\Models\SyncedWebhotelierBooking;
use App\Services\MemberStayDateBackfillService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SyncedWebhotelierBookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('last_synced_at', 'desc')
            ->columns([
                TextColumn::make('booking_number')->label('Booking Number')->searchable()->weight('semibold'),
                TextColumn::make('guest_name')->label('Guest Name')->searchable()->placeholder('-'),
                TextColumn::make('email')->searchable(),
                TextColumn::make('phone')->searchable()->placeholder('-'),
                TextColumn::make('check_in')->label('Check-in')->date('d M Y')->sortable()->placeholder('-'),
                TextColumn::make('check_out')->label('Check-out')->date('d M Y')->sortable()->placeholder('-'),
                TextColumn::make('room_name')->label('Room Name')->searchable()->placeholder('-'),
                TextColumn::make('room_type')->label('Room Type')->placeholder('-'),
                TextColumn::make('status')->badge()->searchable()->placeholder('-'),
                TextColumn::make('booking_total')
                    ->label('Booking Total')
                    ->formatStateUsing(fn($state, $record): string => $state === null ? '-' : trim(($record->currency ?: '') . ' ' . number_format((float) $state, 2)))
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('currency')->placeholder('-'),
                TextColumn::make('member.full_name')->label('Member')->placeholder('-'),
                TextColumn::make('member_assigned_manually')
                    ->label('Assignment')
                    ->badge()
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Manual' : 'Auto')
                    ->color(fn(bool $state): string => $state ? 'warning' : 'gray'),
                TextColumn::make('remote_updated_at')->dateTime('d M Y H:i')->sortable()->placeholder('-'),
                TextColumn::make('last_synced_at')->dateTime('d M Y H:i')->sortable()->placeholder('-'),
            ])
            ->recordActions([
                Action::make('moveToMember')
                    ->label('Move to Member')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('warning')
                    ->modalHeading('Move booking to member')
                    ->modalDescription('This manual assignment will be kept during future booking syncs.')
                    ->fillForm(fn(SyncedWebhotelierBooking $record): array => [
                        'member_id' => $record->member_id,
                    ])
                    ->form([
                        Select::make('member_id')
                            ->label('Member')
                            ->searchable()
                            ->required()
                            ->getSearchResultsUsing(fn(string $search): array => Member::query()
                                ->where(function (Builder $query) use ($search): void {
                                    $query
                                        ->where('first_name', 'like', "%{$search}%")
                                        ->orWhere('last_name', 'like', "%{$search}%")
                                        ->orWhere('name', 'like', "%{$search}%")
                                        ->orWhere('email', 'like', "%{$search}%");
                                })
                                ->orderBy('first_name')
                                ->orderBy('last_name')
                                ->limit(50)
                                ->get()
                                ->mapWithKeys(fn(Member $member): array => [
                                    $member->getKey() => self::memberOptionLabel($member),
                                ])
                                ->all())
                            ->getOptionLabelUsing(fn($value): ?string => ($member = Member::find($value))
                                ? self::memberOptionLabel($member)
                                : null),
                    ])
                    ->requiresConfirmation()
                    ->action(function (SyncedWebhotelierBooking $record, array $data): void {
                        $member = Member::findOrFail($data['member_id']);

                        $record->forceFill([
                            'member_id' => $member->getKey(),
                            'member_assigned_manually' => true,
                        ])->save();

                        app(MemberStayDateBackfillService::class)->fillMissingDatesForMember($member);

                        Notification::make()
                            ->title('Booking moved to member')
                            ->body($record->booking_number . ' is now assigned to ' . self::memberOptionLabel($member) . '.')
                            ->success()
                            ->send();
                    }),

                Action::make('restoreAutoAssignment')
                    ->label('Use Auto Match')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->visible(fn(SyncedWebhotelierBooking $record): bool => $record->member_assigned_manually)
                    ->action(function (SyncedWebhotelierBooking $record): void {
                        $record->forceFill([
                            'member_assigned_manually' => false,
                        ])->save();

                        Notification::make()
                            ->title('Auto member matching restored')
                            ->body('The next booking sync can match this booking by email again.')
                            ->success()
                            ->send();
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(fn(): array => \App\Models\SyncedWebhotelierBooking::query()
                        ->whereNotNull('status')
                        ->distinct()
                        ->orderBy('status')
                        ->pluck('status', 'status')
                        ->all()),

                Filter::make('check_in')
                    ->schema([
                        DatePicker::make('from')->label('Check-in From'),
                        DatePicker::make('until')->label('Check-in Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn(Builder $query, $date): Builder => $query->whereDate('check_in', '>=', $date))
                            ->when($data['until'] ?? null, fn(Builder $query, $date): Builder => $query->whereDate('check_in', '<=', $date));
                    }),
            ]);
    }

    private static function memberOptionLabel(Member $member): string
    {
        $name = trim($member->full_name ?: (string) $member->name);
        $email = (string) $member->email;

        return $name !== ''
            ? "{$name} <{$email}>"
            : $email;
    }
}

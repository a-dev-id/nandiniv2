<?php

namespace App\Filament\Resources\MemberRewardRedemptions\Tables;

use App\Models\MemberRewardRedemption;
use App\Services\MembershipEmailRelayService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Throwable;

class MemberRewardRedemptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('redemption_code')
                    ->label('Code')
                    ->searchable()
                    ->copyable()
                    ->weight('semibold'),

                TextColumn::make('member.full_name')
                    ->label('Member')
                    ->searchable(
                        query: fn(Builder $query, string $search): Builder => $query->whereHas(
                            'member',
                            fn(Builder $query): Builder => $query
                                ->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                        ),
                    )
                    ->description(fn(MemberRewardRedemption $record): string => $record->member?->email ?? '-'),

                TextColumn::make('reward_name')
                    ->label('Reward')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('points_used')
                    ->label('Points')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('status_label')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Used' => 'success',
                        'Cancelled' => 'danger',
                        'Expired' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('notes')
                    ->label('Request')
                    ->limit(60)
                    ->toggleable(),

                TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->label('Requested')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        MemberRewardRedemption::STATUS_PENDING => 'Pending',
                        MemberRewardRedemption::STATUS_USED => 'Used / Accepted',
                        MemberRewardRedemption::STATUS_CANCELLED => 'Cancelled',
                        MemberRewardRedemption::STATUS_EXPIRED => 'Expired',
                    ]),
            ])
            ->recordActions([
                Action::make('markUsed')
                    ->label('Accept / Used')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn(MemberRewardRedemption $record): bool => $record->status === MemberRewardRedemption::STATUS_PENDING)
                    ->form([
                        Textarea::make('notes')
                            ->label('Staff Notes')
                            ->rows(3)
                            ->maxLength(1000),
                    ])
                    ->action(function (MemberRewardRedemption $record, array $data): void {
                        $notes = self::appendStaffNote($record->notes, 'Accepted / used', $data['notes'] ?? null);

                        $record->markAsUsed($notes);
                        $record->refresh();

                        self::sendRedemptionSuccessEmail($record);

                        Notification::make()
                            ->title('Redemption accepted')
                            ->success()
                            ->send();
                    }),

                Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn(MemberRewardRedemption $record): bool => $record->status === MemberRewardRedemption::STATUS_PENDING)
                    ->form([
                        Textarea::make('notes')
                            ->label('Cancellation Notes')
                            ->rows(3)
                            ->maxLength(1000),
                    ])
                    ->action(function (MemberRewardRedemption $record, array $data): void {
                        $notes = self::appendStaffNote($record->notes, 'Cancelled', $data['notes'] ?? null);

                        $record->markAsCancelled($notes);

                        Notification::make()
                            ->title('Redemption cancelled')
                            ->success()
                            ->send();
                    }),

                Action::make('expire')
                    ->label('Expire')
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn(MemberRewardRedemption $record): bool => $record->status === MemberRewardRedemption::STATUS_PENDING)
                    ->action(function (MemberRewardRedemption $record): void {
                        $record->markAsExpired();

                        Notification::make()
                            ->title('Redemption expired')
                            ->success()
                            ->send();
                    }),

                EditAction::make()
                    ->label('View / Edit'),
            ]);
    }

    private static function appendStaffNote(?string $existingNotes, string $action, ?string $staffNote): string
    {
        $line = now()->format('d M Y H:i') . ' - Staff ' . $action;

        if (filled($staffNote)) {
            $line .= ': ' . trim((string) $staffNote);
        }

        return trim(implode(PHP_EOL . PHP_EOL, array_filter([
            $existingNotes,
            $line,
        ])));
    }

    private static function sendRedemptionSuccessEmail(MemberRewardRedemption $redemption): void
    {
        $member = $redemption->member;

        if (! $member || blank($member->email)) {
            return;
        }

        try {
            $result = app(MembershipEmailRelayService::class)->sendView('emails.membership.reward-redemption-success', [
                'member' => $member,
                'redemption' => $redemption,
                'dashboardUrl' => route('membership.dashboard'),
            ], [
                'to' => $member->email,
                'cc' => self::mailRecipients(config('mail.guest_cc')),
                'bcc' => self::mailRecipients(config('mail.guest_bcc')),
                'subject' => 'Your Reward Has Been Successfully Redeemed',
            ]);

            if (! $result['success']) {
                Log::warning('Reward redemption success email could not be sent through relay.', [
                    'member_id' => $member->id,
                    'redemption_id' => $redemption->id,
                    'email' => $member->email,
                    'relay_response' => $result,
                ]);
            }
        } catch (Throwable $exception) {
            Log::warning('Reward redemption success email could not be sent.', [
                'member_id' => $member->id,
                'redemption_id' => $redemption->id,
                'email' => $member->email,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * @return array<int, string>
     */
    private static function mailRecipients(mixed $value): array
    {
        return collect(explode(',', (string) $value))
            ->map(fn(mixed $recipient): string => trim((string) $recipient))
            ->filter()
            ->values()
            ->all();
    }
}

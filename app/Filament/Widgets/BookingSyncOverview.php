<?php

namespace App\Filament\Widgets;

use App\Models\BookingSyncLog;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BookingSyncOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 10;

    protected string $view = 'filament.widgets.collapsible-stats-overview';

    public function getSectionHeading(): string
    {
        return 'Booking Synchronization';
    }

    public function getSectionDescription(): string
    {
        return 'Latest WebHotelier synchronization health and activity.';
    }

    public function getSectionCollapseId(): string
    {
        return 'booking-synchronization';
    }

    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $last = BookingSyncLog::query()->latest()->first();
        $lastSuccess = BookingSyncLog::query()->where('status', BookingSyncLog::STATUS_SUCCESS)->latest('finished_at')->first();
        $lastFailed = BookingSyncLog::query()->where('status', BookingSyncLog::STATUS_FAILED)->latest('finished_at')->first();

        return [
            Stat::make('Last Sync Status', $last?->status ? ucfirst($last->status) : 'Never')
                ->color(match ($last?->status) {
                    BookingSyncLog::STATUS_SUCCESS => 'success',
                    BookingSyncLog::STATUS_FAILED => 'danger',
                    BookingSyncLog::STATUS_RUNNING => 'warning',
                    default => 'gray',
                }),

            Stat::make('Last Sync Time', $last?->finished_at?->format('d M Y H:i') ?? $last?->started_at?->format('d M Y H:i') ?? '-'),
            Stat::make('Last Successful Sync', $lastSuccess?->finished_at?->format('d M Y H:i') ?? '-')->color('success'),
            Stat::make('Last Failed Sync', $lastFailed?->finished_at?->format('d M Y H:i') ?? '-')->color('danger'),
            Stat::make('Bookings Received', (string) ($last?->bookings_received ?? 0)),
            Stat::make('Members Created', (string) ($last?->members_created ?? 0)),
        ];
    }
}

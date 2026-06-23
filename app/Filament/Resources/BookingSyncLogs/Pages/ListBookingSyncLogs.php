<?php

namespace App\Filament\Resources\BookingSyncLogs\Pages;

use App\Filament\Resources\BookingSyncLogs\BookingSyncLogResource;
use App\Services\BookingSyncService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Cache;

class ListBookingSyncLogs extends ListRecords
{
    protected static string $resource = BookingSyncLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('runSyncNow')
                ->label('Run Sync Now')
                ->icon('heroicon-o-play')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function (BookingSyncService $syncService): void {
                    $lock = Cache::lock('booking-sync-cron', 600);

                    if (! $lock->get()) {
                        Notification::make()
                            ->title('Booking sync is already running')
                            ->warning()
                            ->send();

                        return;
                    }

                    try {
                        $summary = $syncService->sync();

                        Notification::make()
                            ->title($summary['success'] ? 'Booking sync completed' : 'Booking sync failed')
                            ->body($summary['message'])
                            ->color($summary['success'] ? 'success' : 'danger')
                            ->send();
                    } finally {
                        optional($lock)->release();
                    }
                }),
        ];
    }
}

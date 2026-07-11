<?php

namespace App\Filament\Resources\Vouchers\Pages;

use App\Filament\Resources\Vouchers\VoucherResource;
use App\Services\Voucher\ExperienceVoucherSynchronizer;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListVouchers extends ListRecords
{
    protected static string $resource = VoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('synchronizeExperiences')
                ->label('Synchronize Experiences')
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation()
                ->modalHeading('Synchronize experience vouchers?')
                ->modalDescription('This updates linked voucher titles, wording, prices, categories, and images. Voucher discounts and sales settings are preserved.')
                ->action(function (ExperienceVoucherSynchronizer $synchronizer): void {
                    $count = $synchronizer->synchronize();

                    Notification::make()
                        ->title("{$count} experience vouchers synchronized")
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}

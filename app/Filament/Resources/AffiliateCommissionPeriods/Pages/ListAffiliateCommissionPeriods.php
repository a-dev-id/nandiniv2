<?php

namespace App\Filament\Resources\AffiliateCommissionPeriods\Pages;

use App\Filament\Resources\AffiliateCommissionPeriods\AffiliateCommissionPeriodResource;
use App\Services\Affiliate\Finance\PrepareAffiliateCommissionPeriodService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListAffiliateCommissionPeriods extends ListRecords
{
    protected static string $resource = AffiliateCommissionPeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('prepare')->label('Prepare Commission Period')->form([
                TextInput::make('year')->integer()->required()->default(now()->subMonthNoOverflow()->year)->minValue(2000)->maxValue(2200),
                Select::make('month')->required()->options(collect(range(1, 12))->mapWithKeys(fn (int $month): array => [$month => now()->setMonth($month)->format('F')])->all())->default(now()->subMonthNoOverflow()->month),
            ])->action(function (array $data): void {
                $summary = app(PrepareAffiliateCommissionPeriodService::class)->prepare((int) $data['year'], (int) $data['month'], auth()->user());
                Notification::make()->title('Commission period prepared')->body("Created {$summary['created']}; unchanged {$summary['unchanged']}; skipped {$summary['skipped']}; unavailable {$summary['unavailable']}.")->success()->send();
            }),
        ];
    }
}

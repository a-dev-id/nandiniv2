<?php

namespace App\Filament\Widgets;

use App\Models\Member;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MembershipOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $tierCounts = Member::query()
            ->selectRaw('tier, COUNT(*) as aggregate')
            ->groupBy('tier')
            ->pluck('aggregate', 'tier');

        return [
            Stat::make('Total Members', number_format(Member::query()->count()))
                ->color('primary'),

            Stat::make('Total Member Points', number_format((int) Member::query()->sum('points')))
                ->color('primary'),

            Stat::make('Dana Tier', number_format((int) ($tierCounts[Member::TIER_BRONZE] ?? 0)))
                ->color('gray'),

            Stat::make('Upaya Tier', number_format((int) ($tierCounts[Member::TIER_SILVER] ?? 0)))
                ->color('info'),

            Stat::make('Dhyana Tier', number_format((int) ($tierCounts[Member::TIER_GOLD] ?? 0)))
                ->color('warning'),

            Stat::make('Jnana Tier', number_format((int) ($tierCounts[Member::TIER_PLATINUM] ?? 0)))
                ->color('success'),
        ];
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\Member;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MembershipOverview extends StatsOverviewWidget
{
    private const ACTIVE_LOGIN_DAYS = 30;
    private const INACTIVE_LOGIN_DAYS = 90;

    protected static ?int $sort = 20;

    protected ?string $heading = 'Member Overview';

    protected ?string $description = 'Membership activity, points, and tier distribution.';

    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $activeSince = now()->subDays(self::ACTIVE_LOGIN_DAYS);
        $inactiveBefore = now()->subDays(self::INACTIVE_LOGIN_DAYS);

        $tierCounts = Member::query()
            ->selectRaw('tier, COUNT(*) as aggregate')
            ->groupBy('tier')
            ->pluck('aggregate', 'tier');

        return [
            Stat::make('Total Members', number_format(Member::query()->count()))
                ->color('primary'),

            Stat::make('Active Members', number_format(Member::query()->where('last_login_at', '>=', $activeSince)->count()))
                ->description('Logged in during the last ' . self::ACTIVE_LOGIN_DAYS . ' days')
                ->color('success'),

            Stat::make('Inactive Members', number_format(Member::query()
                ->where(function ($query) use ($inactiveBefore): void {
                    $query
                        ->whereNull('last_login_at')
                        ->orWhere('last_login_at', '<', $inactiveBefore);
                })
                ->count()))
                ->description('Never logged in or inactive for ' . self::INACTIVE_LOGIN_DAYS . '+ days')
                ->color('danger'),

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

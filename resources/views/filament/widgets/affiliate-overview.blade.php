<x-filament-widgets::widget>
    <style>
        .affiliate-overview { display: grid; gap: 1.25rem; }
        .affiliate-overview .overview-metrics { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: .75rem; }
        .affiliate-overview .overview-card { border: 1px solid rgb(229 231 235); border-radius: .75rem; background: rgb(255 255 255); padding: 1rem; }
        .dark .affiliate-overview .overview-card { border-color: rgb(55 65 81); background: rgb(24 24 27); }
        .affiliate-overview .overview-label { color: rgb(107 114 128); font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; }
        .affiliate-overview .overview-value { margin-top: .5rem; color: rgb(17 24 39); font-size: 1.5rem; font-weight: 700; }
        .dark .affiliate-overview .overview-value { color: rgb(255 255 255); }
        .affiliate-overview .overview-grid { display: grid; grid-template-columns: minmax(0, 1.5fr) minmax(18rem, 1fr); gap: 1rem; }
        .affiliate-overview .overview-section-title { margin-bottom: .75rem; color: rgb(17 24 39); font-size: .875rem; font-weight: 700; }
        .dark .affiliate-overview .overview-section-title { color: rgb(255 255 255); }
        .affiliate-overview table { width: 100%; border-collapse: collapse; font-size: .875rem; }
        .affiliate-overview th { padding: .625rem; border-bottom: 1px solid rgb(229 231 235); color: rgb(107 114 128); text-align: left; font-size: .75rem; font-weight: 600; }
        .affiliate-overview td { padding: .75rem .625rem; border-bottom: 1px solid rgb(243 244 246); color: rgb(55 65 81); }
        .dark .affiliate-overview th, .dark .affiliate-overview td { border-color: rgb(55 65 81); color: rgb(209 213 219); }
        .affiliate-overview .overview-number { text-align: right; }
        .affiliate-overview .overview-exceptions { display: grid; gap: .5rem; }
        .affiliate-overview .overview-exception { display: flex; align-items: center; justify-content: space-between; gap: 1rem; border-bottom: 1px solid rgb(243 244 246); padding: .625rem 0; color: rgb(55 65 81); font-size: .8125rem; }
        .dark .affiliate-overview .overview-exception { border-color: rgb(55 65 81); color: rgb(209 213 219); }
        .affiliate-overview .overview-badge { min-width: 2rem; border-radius: 9999px; padding: .2rem .5rem; background: rgb(254 243 199); color: rgb(146 64 14); text-align: center; font-weight: 700; }
        .affiliate-overview .overview-badge-clear { background: rgb(220 252 231); color: rgb(22 101 52); }
        @media (max-width: 1279px) { .affiliate-overview .overview-metrics { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
        @media (max-width: 767px) { .affiliate-overview .overview-metrics, .affiliate-overview .overview-grid { grid-template-columns: 1fr; } }
    </style>

    <x-filament::section
        heading="Affiliate Overview"
        description="Important activity and items requiring attention from the last 30 days."
        collapse-id="affiliate-overview"
        collapsible
        collapsed
    >
        <div class="affiliate-overview">
            <div class="overview-metrics">
                @foreach ([
                    'New Affiliates' => $metrics['new_affiliates'],
                    'Total Clicks' => $metrics['total_clicks'],
                    'Unique Clicks' => $metrics['unique_clicks'],
                    'New Bookings' => $metrics['tracked_bookings'],
                    'Pending Payment' => $metrics['pending_commissions'],
                    'Paid' => $metrics['paid_commissions'],
                ] as $label => $value)
                    <div class="overview-card"><p class="overview-label">{{ $label }}</p><p class="overview-value">{{ number_format($value) }}</p></div>
                @endforeach
            </div>

            <div class="overview-grid">
                <div class="overview-card">
                    <h3 class="overview-section-title">Top affiliates by clicks</h3>
                    <div style="overflow-x:auto">
                        <table>
                            <thead><tr><th>Affiliate</th><th>Code</th><th class="overview-number">Clicks</th><th class="overview-number">Unique</th><th>Last Click</th></tr></thead>
                            <tbody>
                                @forelse ($analytics['top_affiliates'] as $row)
                                    <tr><td>{{ $row['name'] }}</td><td>{{ $row['code'] }}</td><td class="overview-number">{{ number_format($row['total']) }}</td><td class="overview-number">{{ number_format($row['unique']) }}</td><td>{{ $row['last_click'] ? \Illuminate\Support\Carbon::parse($row['last_click'])->format('d M Y H:i') : '—' }}</td></tr>
                                @empty
                                    <tr><td colspan="5">No click activity in the last 30 days.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="overview-card">
                    <h3 class="overview-section-title">Needs attention</h3>
                    <div class="overview-exceptions">
                        @foreach ($importantExceptions as $label => $value)
                            <div class="overview-exception"><span>{{ $label }}</span><span class="overview-badge {{ $value === 0 ? 'overview-badge-clear' : '' }}">{{ number_format($value) }}</span></div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

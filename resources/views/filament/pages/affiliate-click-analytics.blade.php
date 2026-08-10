<x-filament-panels::page>
    <style>
        .affiliate-click-analytics { display: grid; gap: 1.5rem; }
        .affiliate-click-analytics .analytics-filter-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; }
        .affiliate-click-analytics .analytics-filter { display: grid; min-width: 0; gap: .5rem; color: rgb(17 24 39); font-size: .875rem; font-weight: 500; }
        .affiliate-click-analytics .analytics-filter select { display: block; width: 100%; min-height: 2.625rem; border: 1px solid rgb(209 213 219); border-radius: .5rem; padding: .5rem .75rem; background: rgb(255 255 255); color: rgb(17 24 39); font-size: .875rem; }
        .dark .affiliate-click-analytics .analytics-filter { color: rgb(255 255 255); }
        .dark .affiliate-click-analytics .analytics-filter select { border-color: rgb(75 85 99); background: rgb(17 24 39); color: rgb(255 255 255); }
        .affiliate-click-analytics .analytics-metric-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; }
        .affiliate-click-analytics .analytics-metric-label { color: rgb(107 114 128); font-size: .875rem; font-weight: 500; }
        .affiliate-click-analytics .analytics-metric-value { margin-top: .5rem; color: rgb(17 24 39); font-size: 1.875rem; line-height: 2.25rem; font-weight: 600; letter-spacing: -.025em; }
        .dark .affiliate-click-analytics .analytics-metric-value { color: rgb(255 255 255); }
        .affiliate-click-analytics .analytics-table-wrap { width: 100%; overflow-x: auto; }
        .affiliate-click-analytics .analytics-table { width: 100%; min-width: 52rem; border-collapse: collapse; text-align: left; font-size: .875rem; }
        .affiliate-click-analytics .analytics-table th { padding: .75rem; border-bottom: 1px solid rgb(229 231 235); color: rgb(107 114 128); font-weight: 500; white-space: nowrap; }
        .affiliate-click-analytics .analytics-table td { padding: .75rem; border-bottom: 1px solid rgb(243 244 246); color: rgb(55 65 81); }
        .dark .affiliate-click-analytics .analytics-table th { border-color: rgb(255 255 255 / .1); color: rgb(156 163 175); }
        .dark .affiliate-click-analytics .analytics-table td { border-color: rgb(255 255 255 / .05); color: rgb(229 231 235); }
        .affiliate-click-analytics .analytics-number { text-align: right; }
        .affiliate-click-analytics .analytics-link { color: rgb(168 132 68); font-weight: 500; }
        .affiliate-click-analytics .analytics-link:hover { text-decoration: underline; }
        .affiliate-click-analytics .analytics-distribution-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1.5rem; }
        .affiliate-click-analytics .analytics-list { display: grid; gap: .75rem; }
        .affiliate-click-analytics .analytics-list-row { display: flex; align-items: center; justify-content: space-between; gap: 1rem; color: rgb(55 65 81); font-size: .875rem; }
        .dark .affiliate-click-analytics .analytics-list-row { color: rgb(229 231 235); }
        @media (max-width: 767px) {
            .affiliate-click-analytics .analytics-filter-grid,
            .affiliate-click-analytics .analytics-metric-grid,
            .affiliate-click-analytics .analytics-distribution-grid { grid-template-columns: 1fr; }
        }
    </style>

    <div class="affiliate-click-analytics">
        <x-filament::section>
            <div class="analytics-filter-grid">
                <label class="analytics-filter">
                    <span class="text-sm font-medium text-gray-950 dark:text-white">Date range</span>
                    <select wire:model.live="range" class="mt-2 block w-full rounded-lg border-gray-300 bg-white text-sm text-gray-950 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white">
                        <option value="7">Last 7 days</option>
                        <option value="30">Last 30 days</option>
                        <option value="90">Last 90 days</option>
                        <option value="all">All time</option>
                    </select>
                </label>
                <label class="analytics-filter">
                    <span class="text-sm font-medium text-gray-950 dark:text-white">Affiliate</span>
                    <select wire:model.live="affiliateId" class="mt-2 block w-full rounded-lg border-gray-300 bg-white text-sm text-gray-950 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white">
                        <option value="">All affiliates</option>
                        @foreach ($affiliates as $affiliate)
                            <option value="{{ $affiliate->id }}">{{ $affiliate->name }} · {{ $affiliate->affiliate_code }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="analytics-filter">
                    <span class="text-sm font-medium text-gray-950 dark:text-white">Status</span>
                    <select wire:model.live="status" class="mt-2 block w-full rounded-lg border-gray-300 bg-white text-sm text-gray-950 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white">
                        <option value="">All statuses</option>
                        @foreach ($statuses as $statusOption)
                            <option value="{{ $statusOption->value }}">{{ $statusOption->label() }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
        </x-filament::section>

        <div class="analytics-metric-grid">
            @foreach ([
                ['label' => 'Total non-bot clicks', 'value' => $analytics['summary']['total']],
                ['label' => 'Total unique clicks', 'value' => $analytics['summary']['unique']],
                ['label' => 'Bot or preview clicks', 'value' => $analytics['summary']['bots']],
            ] as $metric)
                <x-filament::section>
                    <p class="analytics-metric-label">{{ $metric['label'] }}</p>
                    <p class="analytics-metric-value">{{ number_format($metric['value']) }}</p>
                </x-filament::section>
            @endforeach
        </div>

        <x-filament::section heading="Top affiliates by clicks">
            <div class="analytics-table-wrap">
                <table class="analytics-table">
                    <thead class="border-b border-gray-200 text-gray-500 dark:border-white/10 dark:text-gray-400">
                        <tr><th class="px-3 py-3 font-medium">Affiliate</th><th class="px-3 py-3 font-medium">Affiliate Code</th><th class="px-3 py-3 text-right font-medium">Total Clicks</th><th class="px-3 py-3 text-right font-medium">Unique Clicks</th><th class="px-3 py-3 font-medium">Top Country</th><th class="px-3 py-3 font-medium">Last Click</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700 dark:divide-white/5 dark:text-gray-200">
                        @forelse ($analytics['top_affiliates'] as $row)
                            <tr>
                                <td><a class="analytics-link" href="{{ \App\Filament\Resources\Affiliates\AffiliateResource::getUrl('view', ['record' => $row['id']]) }}">{{ $row['name'] }}</a></td>
                                <td>{{ $row['code'] }}</td><td class="analytics-number">{{ number_format($row['total']) }}</td><td class="analytics-number">{{ number_format($row['unique']) }}</td><td>{{ $row['top_country'] ?: 'Unknown' }}</td><td>{{ $row['last_click'] ? \Illuminate\Support\Carbon::parse($row['last_click'])->format('d M Y H:i') : '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-3 py-8 text-center text-gray-500 dark:text-gray-400">No click activity matches these filters.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        <div class="analytics-distribution-grid">
            <x-filament::section heading="Top countries">
                <div class="analytics-list">
                    @forelse ($analytics['countries'] as $country)
                        <div class="analytics-list-row"><span>{{ $country['country'] }}</span><span>{{ number_format($country['clicks']) }} · {{ number_format($country['percentage'], 1) }}%</span></div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">No country data yet.</p>
                    @endforelse
                </div>
            </x-filament::section>
            <x-filament::section heading="Device distribution">
                <div class="analytics-list">
                    @forelse ($analytics['devices'] as $device)
                        <div class="analytics-list-row"><span>{{ $device['label'] }}</span><span>{{ number_format($device['clicks']) }} · {{ number_format($device['percentage'], 1) }}%</span></div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">No device data yet.</p>
                    @endforelse
                </div>
            </x-filament::section>
        </div>
    </div>
</x-filament-panels::page>

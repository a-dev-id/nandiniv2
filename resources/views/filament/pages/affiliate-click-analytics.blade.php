<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <div class="grid gap-4 md:grid-cols-3">
                <label class="block">
                    <span class="text-sm font-medium text-gray-950 dark:text-white">Date range</span>
                    <select wire:model.live="range" class="mt-2 block w-full rounded-lg border-gray-300 bg-white text-sm text-gray-950 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white">
                        <option value="7">Last 7 days</option>
                        <option value="30">Last 30 days</option>
                        <option value="90">Last 90 days</option>
                        <option value="all">All time</option>
                    </select>
                </label>
                <label class="block">
                    <span class="text-sm font-medium text-gray-950 dark:text-white">Affiliate</span>
                    <select wire:model.live="affiliateId" class="mt-2 block w-full rounded-lg border-gray-300 bg-white text-sm text-gray-950 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white">
                        <option value="">All affiliates</option>
                        @foreach ($affiliates as $affiliate)
                            <option value="{{ $affiliate->id }}">{{ $affiliate->name }} · {{ $affiliate->affiliate_code }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
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

        <div class="grid gap-4 md:grid-cols-3">
            @foreach ([
                ['label' => 'Total non-bot clicks', 'value' => $analytics['summary']['total']],
                ['label' => 'Total unique clicks', 'value' => $analytics['summary']['unique']],
                ['label' => 'Bot or preview clicks', 'value' => $analytics['summary']['bots']],
            ] as $metric)
                <x-filament::section>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $metric['label'] }}</p>
                    <p class="mt-2 text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">{{ number_format($metric['value']) }}</p>
                </x-filament::section>
            @endforeach
        </div>

        <x-filament::section heading="Top affiliates by clicks">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[52rem] text-left text-sm">
                    <thead class="border-b border-gray-200 text-gray-500 dark:border-white/10 dark:text-gray-400">
                        <tr><th class="px-3 py-3 font-medium">Affiliate</th><th class="px-3 py-3 font-medium">Affiliate Code</th><th class="px-3 py-3 text-right font-medium">Total Clicks</th><th class="px-3 py-3 text-right font-medium">Unique Clicks</th><th class="px-3 py-3 font-medium">Top Country</th><th class="px-3 py-3 font-medium">Last Click</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700 dark:divide-white/5 dark:text-gray-200">
                        @forelse ($analytics['top_affiliates'] as $row)
                            <tr>
                                <td class="px-3 py-3"><a class="font-medium text-primary-600 hover:underline dark:text-primary-400" href="{{ \App\Filament\Resources\Affiliates\AffiliateResource::getUrl('view', ['record' => $row['id']]) }}">{{ $row['name'] }}</a></td>
                                <td class="px-3 py-3">{{ $row['code'] }}</td><td class="px-3 py-3 text-right">{{ number_format($row['total']) }}</td><td class="px-3 py-3 text-right">{{ number_format($row['unique']) }}</td><td class="px-3 py-3">{{ $row['top_country'] ?: 'Unknown' }}</td><td class="px-3 py-3">{{ $row['last_click'] ? \Illuminate\Support\Carbon::parse($row['last_click'])->format('d M Y H:i') : '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-3 py-8 text-center text-gray-500 dark:text-gray-400">No click activity matches these filters.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        <div class="grid gap-6 lg:grid-cols-2">
            <x-filament::section heading="Top countries">
                <div class="space-y-3">
                    @forelse ($analytics['countries'] as $country)
                        <div class="flex items-center justify-between gap-4 text-sm text-gray-700 dark:text-gray-200"><span>{{ $country['country'] }}</span><span>{{ number_format($country['clicks']) }} · {{ number_format($country['percentage'], 1) }}%</span></div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">No country data yet.</p>
                    @endforelse
                </div>
            </x-filament::section>
            <x-filament::section heading="Device distribution">
                <div class="space-y-3">
                    @forelse ($analytics['devices'] as $device)
                        <div class="flex items-center justify-between gap-4 text-sm text-gray-700 dark:text-gray-200"><span>{{ $device['label'] }}</span><span>{{ number_format($device['clicks']) }} · {{ number_format($device['percentage'], 1) }}%</span></div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">No device data yet.</p>
                    @endforelse
                </div>
            </x-filament::section>
        </div>
    </div>
</x-filament-panels::page>

<x-filament-panels::page>
    <style>
        .affiliate-finance-overview { display: grid; gap: 1.5rem; }
        .affiliate-finance-overview .finance-metrics { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 1rem; }
        .affiliate-finance-overview .finance-metric { position: relative; min-width: 0; overflow: hidden; border: 1px solid rgb(229 231 235); border-radius: .75rem; background: rgb(255 255 255); padding: 1.25rem; box-shadow: 0 1px 2px rgb(0 0 0 / .04); }
        .affiliate-finance-overview .finance-metric::before { content: ''; position: absolute; inset: 0 auto 0 0; width: .2rem; background: rgb(168 132 68); }
        .affiliate-finance-overview .finance-metric-label { min-height: 2.5rem; color: rgb(107 114 128); font-size: .75rem; font-weight: 600; line-height: 1.25rem; letter-spacing: .04em; text-transform: uppercase; }
        .affiliate-finance-overview .finance-metric-value { margin-top: .75rem; color: rgb(17 24 39); font-size: 2rem; font-weight: 650; line-height: 1; letter-spacing: -.025em; }
        .affiliate-finance-overview .finance-summaries { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1.5rem; }
        .affiliate-finance-overview .finance-total-list { display: grid; gap: .75rem; }
        .affiliate-finance-overview .finance-total-row { display: flex; align-items: center; justify-content: space-between; gap: 1rem; border-bottom: 1px solid rgb(229 231 235); padding-bottom: .75rem; color: rgb(55 65 81); font-size: .875rem; }
        .affiliate-finance-overview .finance-total-row:last-child { border-bottom: 0; padding-bottom: 0; }
        .affiliate-finance-overview .finance-total-value { flex: none; color: rgb(17 24 39); font-size: 1rem; font-weight: 600; }
        .affiliate-finance-overview .finance-empty { display: flex; min-height: 5rem; align-items: center; gap: .875rem; color: rgb(107 114 128); font-size: .875rem; line-height: 1.5rem; }
        .affiliate-finance-overview .finance-empty-icon { display: flex; height: 2.5rem; width: 2.5rem; flex: none; align-items: center; justify-content: center; border-radius: 9999px; background: rgb(249 250 251); color: rgb(156 163 175); }
        .affiliate-finance-overview .finance-note { display: flex; align-items: flex-start; gap: .875rem; border: 1px solid rgb(229 231 235); border-left: .2rem solid rgb(168 132 68); border-radius: .75rem; background: rgb(255 255 255); padding: 1rem 1.25rem; color: rgb(75 85 99); font-size: .875rem; line-height: 1.5rem; }
        .affiliate-finance-overview .finance-note svg { margin-top: .125rem; height: 1.25rem; width: 1.25rem; flex: none; color: rgb(168 132 68); }
        .dark .affiliate-finance-overview .finance-metric,
        .dark .affiliate-finance-overview .finance-note { border-color: rgb(255 255 255 / .1); background: rgb(24 24 27); }
        .dark .affiliate-finance-overview .finance-metric-label,
        .dark .affiliate-finance-overview .finance-empty { color: rgb(161 161 170); }
        .dark .affiliate-finance-overview .finance-metric-value,
        .dark .affiliate-finance-overview .finance-total-value { color: rgb(250 250 250); }
        .dark .affiliate-finance-overview .finance-total-row { border-color: rgb(255 255 255 / .1); color: rgb(212 212 216); }
        .dark .affiliate-finance-overview .finance-empty-icon { background: rgb(255 255 255 / .05); color: rgb(113 113 122); }
        .dark .affiliate-finance-overview .finance-note { color: rgb(212 212 216); }
        @media (max-width: 1279px) {
            .affiliate-finance-overview .finance-metrics { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }
        @media (max-width: 767px) {
            .affiliate-finance-overview .finance-metrics { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .affiliate-finance-overview .finance-summaries { grid-template-columns: 1fr; }
        }
        @media (max-width: 479px) {
            .affiliate-finance-overview .finance-metrics { grid-template-columns: 1fr; }
            .affiliate-finance-overview .finance-metric-label { min-height: 0; }
        }
    </style>

    <div class="affiliate-finance-overview">
        <section class="finance-metrics" aria-label="Affiliate finance metrics">
            @foreach ([
                ['label' => 'Pending Commission Review', 'value' => $counts['pending_review']],
                ['label' => 'Held Commission', 'value' => $counts['held']],
                ['label' => 'Payouts Ready', 'value' => $counts['ready']],
                ['label' => 'Payouts Processing', 'value' => $counts['processing']],
                ['label' => 'Overdue Payouts', 'value' => $counts['overdue']],
            ] as $metric)
                <article class="finance-metric">
                    <p class="finance-metric-label">{{ $metric['label'] }}</p>
                    <p class="finance-metric-value">{{ number_format($metric['value']) }}</p>
                </article>
            @endforeach
        </section>

        <div class="finance-summaries">
            <x-filament::section heading="Approved Unpaid">
                @if ($approved->isNotEmpty())
                    <div class="finance-total-list">
                        @foreach ($approved as $total)
                            <div class="finance-total-row">
                                <span>{{ $total->currency }}</span>
                                <span class="finance-total-value">{{ app(\App\Services\Affiliate\Booking\AffiliateMoneyFormatter::class)->format($total->getRawOriginal('amount'), $total->currency) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="finance-empty">
                        <span class="finance-empty-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        </span>
                        <span>No approved unpaid commission.</span>
                    </div>
                @endif
            </x-filament::section>

            <x-filament::section heading="Paid This Month">
                @if ($paidThisMonth->isNotEmpty())
                    <div class="finance-total-list">
                        @foreach ($paidThisMonth as $total)
                            <div class="finance-total-row">
                                <span>{{ $total->currency }}</span>
                                <span class="finance-total-value">{{ app(\App\Services\Affiliate\Booking\AffiliateMoneyFormatter::class)->format($total->getRawOriginal('amount'), $total->currency) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="finance-empty">
                        <span class="finance-empty-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        </span>
                        <span>No payouts recorded as paid this month.</span>
                    </div>
                @endif
            </x-filament::section>
        </div>

        <aside class="finance-note">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M11.25 11.25 12 10.5m0 0 .75.75M12 10.5v5.25m9-3.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
            <p>Payouts shown here are prepared and recorded by Finance after payment is completed externally. This system does not transfer funds through Wise or a bank.</p>
        </aside>
    </div>
</x-filament-panels::page>

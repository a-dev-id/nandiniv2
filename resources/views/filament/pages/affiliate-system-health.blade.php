<x-filament-panels::page>
    @php
        $checkCollection = collect($checks)->keyBy('label');
        $statusCounts = collect($checks)->countBy('status');
        $groups = [
            'Infrastructure & Configuration' => [
                'Affiliate Domain Configuration',
                'Short-Link Domain Configuration',
                'Database Status',
                'Storage Availability',
                'Affiliate Email Relay',
                'GeoIP / Country Detection',
            ],
            'Automation & Processing' => [
                'Queue Status',
                'Scheduler Status',
                'Failed Queue Jobs',
                'Last Booking Sync',
                'Last Click Cleanup',
                'Last Commission Preparation',
            ],
            'Launch Readiness' => [
                'Voucher Field Availability',
                'Affiliate Terms and Privacy',
            ],
        ];
    @endphp

    <style>
        .affiliate-system-health { display: grid; gap: 1.75rem; }
        .affiliate-system-health .health-overview { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 1.5rem; align-items: center; border: 1px solid rgb(229 231 235); border-left: .2rem solid rgb(168 132 68); border-radius: .75rem; background: rgb(255 255 255); padding: 1.125rem 1.25rem; }
        .affiliate-system-health .health-overview-copy { color: rgb(75 85 99); font-size: .875rem; line-height: 1.5rem; }
        .affiliate-system-health .health-overview-counts { display: flex; flex-wrap: wrap; justify-content: flex-end; gap: .5rem; }
        .affiliate-system-health .health-count { display: inline-flex; align-items: center; gap: .4rem; border: 1px solid rgb(229 231 235); border-radius: 9999px; padding: .35rem .65rem; color: rgb(75 85 99); font-size: .75rem; font-weight: 600; white-space: nowrap; }
        .affiliate-system-health .health-count-dot { height: .5rem; width: .5rem; border-radius: 9999px; background: rgb(156 163 175); }
        .affiliate-system-health .health-count-healthy .health-count-dot { background: rgb(34 197 94); }
        .affiliate-system-health .health-count-attention .health-count-dot { background: rgb(234 179 8); }
        .affiliate-system-health .health-count-unconfigured .health-count-dot { background: rgb(239 68 68); }
        .affiliate-system-health .health-group { display: grid; gap: 1rem; }
        .affiliate-system-health .health-group-heading { display: flex; align-items: center; gap: .75rem; color: rgb(55 65 81); font-size: .75rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; }
        .affiliate-system-health .health-group-heading::after { content: ''; height: 1px; flex: 1; background: rgb(229 231 235); }
        .affiliate-system-health .health-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; }
        .affiliate-system-health .health-card { position: relative; min-width: 0; overflow: hidden; border: 1px solid rgb(229 231 235); border-radius: .75rem; background: rgb(255 255 255); padding: 1.125rem 1.25rem 1.25rem; box-shadow: 0 1px 2px rgb(0 0 0 / .04); }
        .affiliate-system-health .health-card::before { content: ''; position: absolute; inset: 0 0 auto; height: .2rem; background: rgb(156 163 175); }
        .affiliate-system-health .health-card-healthy::before { background: rgb(34 197 94); }
        .affiliate-system-health .health-card-attention-required::before { background: rgb(234 179 8); }
        .affiliate-system-health .health-card-not-configured::before { background: rgb(239 68 68); }
        .affiliate-system-health .health-card-header { display: flex; align-items: flex-start; justify-content: space-between; gap: .75rem; }
        .affiliate-system-health .health-card-title { min-width: 0; color: rgb(17 24 39); font-size: .875rem; font-weight: 650; line-height: 1.35rem; }
        .affiliate-system-health .health-card-summary { margin-top: .75rem; color: rgb(107 114 128); font-size: .8125rem; line-height: 1.35rem; }
        .dark .affiliate-system-health .health-overview,
        .dark .affiliate-system-health .health-card { border-color: rgb(255 255 255 / .1); background: rgb(24 24 27); }
        .dark .affiliate-system-health .health-overview-copy,
        .dark .affiliate-system-health .health-card-summary { color: rgb(161 161 170); }
        .dark .affiliate-system-health .health-count { border-color: rgb(255 255 255 / .1); color: rgb(212 212 216); }
        .dark .affiliate-system-health .health-group-heading { color: rgb(161 161 170); }
        .dark .affiliate-system-health .health-group-heading::after { background: rgb(255 255 255 / .1); }
        .dark .affiliate-system-health .health-card-title { color: rgb(250 250 250); }
        @media (max-width: 1279px) {
            .affiliate-system-health .health-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 767px) {
            .affiliate-system-health .health-overview { grid-template-columns: 1fr; }
            .affiliate-system-health .health-overview-counts { justify-content: flex-start; }
            .affiliate-system-health .health-grid { grid-template-columns: 1fr; }
        }
    </style>

    <div class="affiliate-system-health">
        <section class="health-overview">
            <p class="health-overview-copy">This page reports local application evidence only. Configuration presence does not confirm that DNS, SSL, mail delivery, queues, the scheduler, or external booking services are working in production.</p>
            <div class="health-overview-counts" aria-label="Health check summary">
                @foreach ([
                    ['status' => 'Healthy', 'class' => 'healthy'],
                    ['status' => 'Attention Required', 'class' => 'attention'],
                    ['status' => 'Not Configured', 'class' => 'unconfigured'],
                    ['status' => 'Unknown', 'class' => 'unknown'],
                ] as $summaryStatus)
                    @if (($statusCounts[$summaryStatus['status']] ?? 0) > 0)
                        <span class="health-count health-count-{{ $summaryStatus['class'] }}">
                            <span class="health-count-dot" aria-hidden="true"></span>
                            {{ $statusCounts[$summaryStatus['status']] }} {{ $summaryStatus['status'] }}
                        </span>
                    @endif
                @endforeach
            </div>
        </section>

        @foreach ($groups as $groupLabel => $labels)
            <section class="health-group" aria-labelledby="health-group-{{ Str::slug($groupLabel) }}">
                <h2 id="health-group-{{ Str::slug($groupLabel) }}" class="health-group-heading">{{ $groupLabel }}</h2>
                <div class="health-grid">
                    @foreach ($labels as $label)
                        @php
                            $check = $checkCollection->get($label);
                            $color = match ($check['status']) {
                                'Healthy' => 'success',
                                'Attention Required' => 'warning',
                                'Not Configured' => 'danger',
                                default => 'gray',
                            };
                            $statusClass = Str::slug($check['status']);
                        @endphp
                        <article class="health-card health-card-{{ $statusClass }}">
                            <div class="health-card-header">
                                <h3 class="health-card-title">{{ $check['label'] }}</h3>
                                <x-filament::badge :color="$color">{{ $check['status'] }}</x-filament::badge>
                            </div>
                            <p class="health-card-summary">{{ $check['summary'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>
</x-filament-panels::page>

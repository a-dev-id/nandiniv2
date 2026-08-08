<x-filament-panels::page>
    <style>
        .affiliate-report { display: grid; gap: 1.5rem; }
        .affiliate-report .report-filter-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem; align-items: end; }
        .affiliate-report .report-filter-grid label { display: grid; gap: .5rem; min-width: 0; font-size: .875rem; font-weight: 500; }
        .affiliate-report .report-filter-grid input,
        .affiliate-report .report-filter-grid select { display: block; width: 100%; min-height: 2.625rem; border: 1px solid rgb(209 213 219); border-radius: .5rem; padding: .5rem .75rem; background: transparent; color: inherit; }
        .dark .affiliate-report .report-filter-grid input,
        .dark .affiliate-report .report-filter-grid select { border-color: rgb(75 85 99); background: rgb(17 24 39); }
        .affiliate-report .report-filter-action { display: flex; align-items: end; }
        .affiliate-report .report-filter-action > * { width: 100%; justify-content: center; }
        .affiliate-report .report-metric-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem; }
        .affiliate-report .report-metric-label { font-size: .875rem; color: rgb(107 114 128); }
        .affiliate-report .report-metric-value { margin-top: .5rem; font-size: 1.875rem; line-height: 2.25rem; font-weight: 600; }
        .affiliate-report .report-two-column { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1.5rem; }
        .affiliate-report .report-status-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1.5rem; }
        .affiliate-report .report-list { display: grid; gap: .75rem; }
        .affiliate-report .report-pair { display: flex; align-items: center; justify-content: space-between; gap: 1rem; font-size: .875rem; }
        .affiliate-report .report-pair strong { flex: none; font-weight: 600; }
        .affiliate-report .report-table-wrap { width: 100%; overflow-x: auto; }
        .affiliate-report .report-table { width: 100%; min-width: 72rem; border-collapse: collapse; text-align: left; font-size: .875rem; }
        .affiliate-report .report-table th { padding: .75rem; border-bottom: 1px solid rgb(209 213 219); color: rgb(107 114 128); font-size: .75rem; text-transform: uppercase; letter-spacing: .04em; white-space: nowrap; }
        .affiliate-report .report-table td { padding: .875rem .75rem; border-bottom: 1px solid rgb(229 231 235); vertical-align: top; }
        .dark .affiliate-report .report-table th,
        .dark .affiliate-report .report-table td { border-color: rgb(55 65 81); }
        .affiliate-report .report-number { text-align: right; }
        .affiliate-report .report-exception-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: .75rem; }
        .affiliate-report .report-exception { display: flex; align-items: center; justify-content: space-between; gap: 1rem; min-width: 0; border: 1px solid rgb(209 213 219); border-radius: .5rem; padding: 1rem; }
        .dark .affiliate-report .report-exception { border-color: rgb(75 85 99); }
        .affiliate-report .report-exception-copy { min-width: 0; }
        .affiliate-report .report-exception-copy span { display: block; font-size: .875rem; line-height: 1.35; }
        .affiliate-report .report-exception-copy a { display: inline-block; margin-top: .35rem; font-size: .75rem; font-weight: 600; }
        .affiliate-report .report-badge { flex: none; min-width: 2rem; border-radius: 9999px; padding: .25rem .625rem; text-align: center; font-size: .875rem; font-weight: 600; }
        .affiliate-report .report-badge-warning { background: rgb(254 243 199); color: rgb(146 64 14); }
        .affiliate-report .report-badge-success { background: rgb(220 252 231); color: rgb(22 101 52); }
        .affiliate-report .report-actions { display: flex; flex-wrap: wrap; gap: .75rem; }
        @media (max-width: 1279px) {
            .affiliate-report .report-filter-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .affiliate-report .report-status-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .affiliate-report .report-exception-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 767px) {
            .affiliate-report .report-filter-grid,
            .affiliate-report .report-metric-grid,
            .affiliate-report .report-two-column,
            .affiliate-report .report-status-grid,
            .affiliate-report .report-exception-grid { grid-template-columns: 1fr; }
        }
    </style>

    <div class="affiliate-report">
    <x-filament::section heading="Report filters" description="All calculations use the same selected date range. Monetary totals remain separated by currency.">
        <form method="GET" class="report-filter-grid">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-200">From<input type="date" name="from" value="{{ $range->from->toDateString() }}" class="mt-2 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900"></label>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-200">To<input type="date" name="to" value="{{ $range->to->toDateString() }}" class="mt-2 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900"></label>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-200">Affiliate Status<select name="status" class="mt-2 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900"><option value="">All statuses</option>@foreach ($statuses as $option)<option value="{{ $option->value }}" @selected($status === $option->value)>{{ $option->label() }}</option>@endforeach</select></label>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-200">Currency<select name="currency" class="mt-2 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900"><option value="">All currencies</option>@foreach ($currencies as $option)<option value="{{ $option }}" @selected($currency === $option)>{{ $option }}</option>@endforeach</select></label>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-200">Affiliate<select name="affiliateId" class="mt-2 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900"><option value="">All affiliates</option>@foreach ($affiliates as $option)<option value="{{ $option->id }}" @selected($affiliateId === (string) $option->id)>{{ $option->name }}{{ $option->affiliate_code ? ' · '.$option->affiliate_code : '' }}</option>@endforeach</select></label>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-200">Registration Source<select name="registrationSource" class="mt-2 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900"><option value="">All sources</option>@foreach ($registrationSources as $option)<option value="{{ $option->value }}" @selected($registrationSource === $option->value)>{{ $option->label() }}</option>@endforeach</select></label>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-200">Approver<select name="approverId" class="mt-2 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900"><option value="">All approvers</option>@foreach ($approvers as $option)<option value="{{ $option->id }}" @selected($approverId === (string) $option->id)>{{ $option->name }}</option>@endforeach</select></label>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-200">Commission Status<select name="commissionStatus" class="mt-2 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900"><option value="">All commission statuses</option>@foreach ($commissionStatuses as $option)<option value="{{ $option->value }}" @selected($commissionStatus === $option->value)>{{ $option->label() }}</option>@endforeach</select></label>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-200">Payout Status<select name="payoutStatus" class="mt-2 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900"><option value="">All payout statuses</option>@foreach ($payoutStatuses as $option)<option value="{{ $option->value }}" @selected($payoutStatus === $option->value)>{{ $option->label() }}</option>@endforeach</select></label>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-200">Commission Reviewer<select name="reviewerId" class="mt-2 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900"><option value="">All reviewers</option>@foreach ($reviewers as $option)<option value="{{ $option->id }}" @selected($reviewerId === (string) $option->id)>{{ $option->name }}</option>@endforeach</select></label>
            <div class="report-filter-action"><x-filament::button type="submit">Apply Filters</x-filament::button></div>
        </form>
    </x-filament::section>

    <div class="report-metric-grid">
        @foreach ([['New Registrations', $report['registration']['new']], ['Self-Registered', $report['registration']['self_registered']], ['Created by Nandini', $report['registration']['created_by_nandini']], ['Pending Review', $report['statuses']['Pending'] ?? 0]] as [$label, $value])
            <x-filament::section><p class="report-metric-label">{{ $label }}</p><p class="report-metric-value">{{ number_format($value) }}</p></x-filament::section>
        @endforeach
    </div>

    <div class="report-two-column">
        <x-filament::section heading="Commission Amounts by Status and Currency">
            <div class="report-list">@forelse ($report['commission_totals'] as $total)<div class="report-pair"><span>{{ \App\Enums\AffiliateCommissionItemStatus::from($total->status)->label() }} · {{ $total->currency }}</span><strong>{{ app(\App\Services\Affiliate\Booking\AffiliateMoneyFormatter::class)->format($total->getRawOriginal('amount'), $total->currency) }}</strong></div>@empty<p class="text-sm text-gray-500">No commission totals.</p>@endforelse</div>
        </x-filament::section>
        <x-filament::section heading="Payout Amounts by Status and Currency">
            <div class="report-list">@forelse ($report['payout_totals'] as $total)<div><div class="report-pair"><span>{{ \App\Enums\AffiliatePayoutStatus::from($total->status)->label() }} · {{ $total->currency }}</span><strong>{{ number_format($total->payout_count) }} payout(s)</strong></div><div class="mt-1 text-xs text-gray-500">Gross {{ app(\App\Services\Affiliate\Booking\AffiliateMoneyFormatter::class)->format($total->getRawOriginal('gross_amount'), $total->currency) }} · Adjustment {{ app(\App\Services\Affiliate\Booking\AffiliateMoneyFormatter::class)->format($total->getRawOriginal('adjustment_amount'), $total->currency) }} · Net {{ app(\App\Services\Affiliate\Booking\AffiliateMoneyFormatter::class)->format($total->getRawOriginal('net_amount'), $total->currency) }}</div></div>@empty<p class="text-sm text-gray-500">No payout totals.</p>@endforelse</div>
        </x-filament::section>
    </div>

    <div class="report-status-grid">
        <x-filament::section heading="Affiliate Status"><div class="report-list">@foreach ($report['statuses'] as $label => $value)<div class="report-pair"><span>{{ $label }}</span><strong>{{ number_format($value) }}</strong></div>@endforeach</div></x-filament::section>
        <x-filament::section heading="Commission Status"><div class="report-list">@foreach ($report['commission_statuses'] as $label => $value)<div class="report-pair"><span>{{ $label }}</span><strong>{{ number_format($value) }}</strong></div>@endforeach</div></x-filament::section>
        <x-filament::section heading="Payout Status"><div class="report-list">@foreach ($report['payout_statuses'] as $label => $value)<div class="report-pair"><span>{{ $label }}</span><strong>{{ number_format($value) }}</strong></div>@endforeach</div></x-filament::section>
        <x-filament::section heading="Payment Profiles"><div class="report-list">@foreach ($report['payment_profiles'] as $label => $value)<div class="report-pair"><span>{{ $label }}</span><strong>{{ number_format($value) }}</strong></div>@endforeach</div></x-filament::section>
    </div>

    <x-filament::section heading="Affiliate Performance" description="Conversion is a tracked Affiliate indicator, not a universal website conversion rate. It is omitted when bookings exceed tracked clicks.">
        <div class="report-table-wrap">
            <table class="report-table">
                <thead class="border-b border-gray-200 text-xs uppercase text-gray-500 dark:border-gray-700"><tr><th class="p-3">Affiliate</th><th class="p-3">Code</th><th class="p-3 text-right">Clicks</th><th class="p-3 text-right">Unique</th><th class="p-3 text-right">Bookings</th><th class="p-3 text-right">Room Nights</th><th class="p-3">Estimated</th><th class="p-3">Approved</th><th class="p-3">Paid</th><th class="p-3 text-right">Indicator</th><th class="p-3">Last Activity</th></tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($report['performance'] as $row)
                        <tr><td class="p-3 font-medium">{{ $row['name'] }}</td><td class="p-3">{{ $row['code'] ?: '—' }}</td><td class="p-3 text-right">{{ number_format($row['clicks']) }}</td><td class="p-3 text-right">{{ number_format($row['unique']) }}</td><td class="p-3 text-right">{{ number_format($row['bookings']) }}</td><td class="p-3 text-right">{{ number_format($row['room_nights']) }}</td>
                            @foreach (['estimated', 'approved', 'paid'] as $moneyKey)<td class="p-3">@forelse ($row[$moneyKey] as $total)<span class="block whitespace-nowrap">{{ app(\App\Services\Affiliate\Booking\AffiliateMoneyFormatter::class)->format($total['amount'], $total['currency']) }}</span>@empty<span class="text-gray-400">—</span>@endforelse</td>@endforeach
                            <td class="p-3 text-right">{{ $row['conversion'] === null ? '—' : number_format($row['conversion'], 1).'%' }}</td><td class="p-3">{{ $row['last_activity'] ? \Illuminate\Support\Carbon::parse($row['last_activity'])->format('d M Y H:i') : '—' }}</td></tr>
                    @empty<tr><td colspan="11" class="p-5 text-center text-gray-500">No Affiliate performance data for this selection.</td></tr>@endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>

    <x-filament::section heading="Operational Exceptions" description="These counts link operational staff back to existing resources; no separate ticketing system is created.">
        @php
            $reportUser = auth()->user();
            $exceptionRoutes = [
                'Pending reviews older than 48 hours' => $reportUser->hasPermissionTo(\App\Models\Permission::AFFILIATE_VIEW) ? route('filament.admin.resources.affiliates.index') : null,
                'Approved affiliates without payment details' => $reportUser->hasPermissionTo(\App\Models\Permission::AFFILIATE_PAYMENT_PROFILE_VIEW) ? route('filament.admin.resources.affiliate-payment-profiles.index') : null,
                'Unknown affiliate voucher codes' => route('filament.admin.resources.affiliate-bookings.index'),
                'Bookings missing room revenue' => route('filament.admin.resources.affiliate-bookings.index'),
                'Bookings with unknown statuses' => route('filament.admin.resources.affiliate-bookings.index'),
                'Commission items on hold' => $reportUser->hasPermissionTo(\App\Models\Permission::AFFILIATE_COMMISSION_VIEW) ? route('filament.admin.resources.affiliate-commission-items.index') : null,
                'Source-changed commission items' => $reportUser->hasPermissionTo(\App\Models\Permission::AFFILIATE_COMMISSION_VIEW) ? route('filament.admin.resources.affiliate-commission-items.index') : null,
                'Payouts past due' => $reportUser->hasPermissionTo(\App\Models\Permission::AFFILIATE_PAYOUT_VIEW) ? route('filament.admin.resources.affiliate-payouts.index') : null,
                'Failed payouts' => $reportUser->hasPermissionTo(\App\Models\Permission::AFFILIATE_PAYOUT_VIEW) ? route('filament.admin.resources.affiliate-payouts.index') : null,
                'Country detection unavailable' => $reportUser->hasPermissionTo(\App\Models\Permission::AFFILIATE_CLICK_VIEW) ? route('filament.admin.pages.affiliate-click-analytics') : null,
                'Recent booking-sync failure' => $reportUser->hasPermissionTo(\App\Models\Permission::AFFILIATE_SYSTEM_HEALTH_VIEW) ? route('filament.admin.pages.affiliate-system-health') : null,
                'Failed Affiliate notification jobs' => $reportUser->hasPermissionTo(\App\Models\Permission::AFFILIATE_SYSTEM_HEALTH_VIEW) ? route('filament.admin.pages.affiliate-system-health') : null,
            ];
        @endphp
        <div class="report-exception-grid">@foreach ($report['exceptions'] as $label => $value)<div class="report-exception"><div class="report-exception-copy"><span>{{ $label }}</span>@if ($exceptionRoutes[$label])<a href="{{ $exceptionRoutes[$label] }}">Review source records</a>@endif</div><span class="report-badge {{ $value > 0 ? 'report-badge-warning' : 'report-badge-success' }}">{{ number_format($value) }}</span></div>@endforeach</div>
    </x-filament::section>

    <x-filament::section heading="Privacy-safe CSV exports" description="Exports exclude guest identity, raw payloads, click identifiers, full payment data, secrets, and authentication data.">
        @php
            $exportFilters = array_filter([
                'status' => $status,
                'currency' => $currency,
                'affiliate_id' => $affiliateId,
                'registration_source' => $registrationSource,
                'approver_id' => $approverId,
                'commission_status' => $commissionStatus,
                'payout_status' => $payoutStatus,
                'reviewer_id' => $reviewerId,
            ], fn ($value) => $value !== '');
        @endphp
        <div class="report-actions">
            @foreach (['affiliates' => 'Affiliates', 'performance' => 'Performance Summary', 'bookings' => 'Affiliate Bookings', 'commission-items' => 'Commission Items', 'payouts' => 'Payouts', 'exceptions' => 'Operational Exceptions'] as $type => $label)
                <x-filament::button tag="a" color="gray" href="{{ route('affiliate.operations.export', ['type' => $type, 'range' => 'custom', 'from' => $range->from->toDateString(), 'to' => $range->to->toDateString()] + $exportFilters) }}">{{ $label }}</x-filament::button>
            @endforeach
        </div>
    </x-filament::section>
    </div>
</x-filament-panels::page>

@push('meta')
<title>Affiliate Dashboard | Nandini Partner Circle</title>
<meta name="description" content="Nandini Partner Circle affiliate dashboard.">
<meta name="robots" content="noindex,nofollow">
@endpush

@php
$reviewTimeMessage = preg_replace('/^Thank you for registering\.\s*/i', '', (string) $settings->review_time_message);
@endphp

<x-layouts.affiliate>
    <div x-data="{
        pendingReviewOpen: @js($showPendingReviewModal),
        approvedWelcomeOpen: @js($showApprovedWelcomeModal),
        doNotShowApprovedWelcomeAgain: false,
        copied: null,
        historyLoading: null,
        closeApprovedWelcome() {
            if (this.doNotShowApprovedWelcomeAgain) {
                this.$refs.approvedWelcomeDismissForm.submit();
                return;
            }

            this.approvedWelcomeOpen = false;
        },
        async copy(value, key) {
            await navigator.clipboard.writeText(value);
            this.copied = key;
            window.setTimeout(() => { if (this.copied === key) this.copied = null }, 2000);
        },
        async loadHistoryPage(event, historyType) {
            const link = event.target.closest('a');

            if (! link || ! link.href || this.historyLoading) return;

            event.preventDefault();
            this.historyLoading = historyType;
            const currentScrollPosition = window.scrollY;

            try {
                const response = await fetch(link.href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });

                if (! response.ok) throw new Error('Unable to load history.');

                const documentFragment = new DOMParser().parseFromString(await response.text(), 'text/html');
                const selector = `[data-history=${historyType}]`;
                const replacement = documentFragment.querySelector(selector);
                const current = document.querySelector(selector);

                if (! replacement || ! current) throw new Error('History section was not found.');

                current.innerHTML = replacement.innerHTML;
                window.requestAnimationFrame(() => window.scrollTo({
                    top: currentScrollPosition,
                    left: 0,
                    behavior: 'auto',
                }));
            } finally {
                this.historyLoading = null;
            }
        }
    }">
        <section class="min-h-[70vh] px-5 py-12 sm:px-8 sm:py-16 lg:px-10">
            <div class="mx-auto w-full max-w-5xl">
                <p class="text-xs font-medium uppercase tracking-[0.12em] text-[#A88444] sm:text-sm">
                    Nandini Partner Circle
                </p>

                <h1 class="mt-3 text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">
                    Affiliate Dashboard
                </h1>

                <p class="mt-5 text-xs leading-relaxed text-gray-600 sm:text-sm">
                    Welcome, {{ $affiliate->name }}.
                </p>

                @if (session('status'))
                <div class="mt-6 max-w-3xl border-l-4 border-emerald-600 bg-emerald-50 px-5 py-4 text-xs leading-relaxed text-emerald-900 sm:text-sm" role="status">
                    {{ session('status') }}
                </div>
                @endif

                @if (session('error'))
                <div class="mt-6 max-w-3xl border-l-4 border-red-500 bg-red-50 px-5 py-4 text-xs leading-relaxed text-red-800 sm:text-sm" role="alert">
                    {{ session('error') }}
                </div>
                @endif

                @if ($affiliate->email_verified_at === null)
                <div class="mt-6 max-w-3xl border-l-4 border-amber-500 bg-amber-50 px-5 py-4 sm:px-6" data-email-verification-notice>
                    <h2 class="text-sm font-medium uppercase leading-snug text-slate-700 sm:text-base">Verify your email address</h2>
                    <p class="mt-2 text-xs leading-relaxed text-gray-600 sm:text-sm">
                        We sent a verification link to <strong>{{ $affiliate->email }}</strong>. You can continue using your account while you verify your email.
                    </p>
                    <form method="POST" action="{{ route('affiliate.verification.send') }}" class="mt-4">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center border border-[#A88444] bg-[#A88444] px-4 py-2 text-xs font-medium uppercase tracking-[0.08em] text-white transition hover:border-[#B8945B] hover:bg-[#B8945B] sm:text-sm">
                            Resend verification email
                        </button>
                    </form>
                </div>
                @endif

                @if ($affiliate->isPending())
                <div class="mt-8 max-w-3xl border-l-4 border-amber-500 bg-amber-50 px-5 py-5 sm:px-6" data-affiliate-status="pending">
                    <h2 class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">Account under review</h2>
                    <p class="mt-3 text-xs leading-relaxed text-gray-600 sm:text-sm">
                        {{ $reviewTimeMessage }}
                    </p>
                </div>
                @elseif ($affiliate->isApproved())
                <div class="mt-8 max-w-3xl border border-slate-200 bg-white px-5 py-6 sm:px-7 sm:py-8" data-affiliate-status="approved">
                    <h2 class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">Your account is approved</h2>
                    <p class="mt-4 text-xs leading-relaxed text-gray-600 sm:text-sm">
                        Your Partner Circle account is active. Share your short link or affiliate code with your audience.
                    </p>

                    @if ($hasActiveTools)
                    <div class="mt-7 border-t border-slate-200 pt-6">
                        <p class="text-xs font-medium uppercase tracking-[0.1em] text-slate-500">Affiliate code</p>
                        <div class="mt-2 flex flex-col gap-3 sm:flex-row sm:items-center">
                            <code class="break-all text-lg font-semibold text-slate-950">{{ $affiliate->affiliate_code }}</code>
                            <button type="button" @click="copy(@js($affiliate->affiliate_code), 'code')" class="inline-flex w-fit items-center justify-center border border-slate-300 px-4 py-2 text-xs font-medium uppercase tracking-[0.08em] text-slate-700 transition hover:border-[#A88444] hover:text-[#8B6B35]">Copy code</button>
                            <span x-cloak x-show="copied === 'code'" role="status" aria-live="polite" class="text-sm font-medium text-emerald-700">Copied</span>
                        </div>
                    </div>

                    <div class="mt-7 border-t border-slate-200 pt-6">
                        <p class="text-xs font-medium uppercase tracking-[0.1em] text-slate-500">Short affiliate link</p>
                        <a href="{{ $shortLink }}" target="_blank" rel="noopener noreferrer" class="mt-2 block break-all text-base font-medium text-[#8B6B35] underline-offset-4 hover:underline">{{ $shortLink }}</a>
                        <div class="mt-4 flex flex-wrap items-center gap-3">
                            <button type="button" @click="copy(@js($shortLink), 'short')" class="inline-flex items-center justify-center border border-[#A88444] bg-[#A88444] px-4 py-2 text-xs font-medium uppercase tracking-[0.08em] text-white transition hover:bg-[#B8945B]">Copy link</button>
                            <a href="{{ $shortLink }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center border border-slate-300 px-4 py-2 text-xs font-medium uppercase tracking-[0.08em] text-slate-700 transition hover:border-[#A88444] hover:text-[#8B6B35]">Open link</a>
                            <span x-cloak x-show="copied === 'short'" role="status" aria-live="polite" class="text-sm font-medium text-emerald-700">Copied</span>
                        </div>
                    </div>

                    @else
                    <p class="mt-6 border-l-4 border-amber-400 bg-amber-50 px-4 py-3 text-sm leading-6 text-slate-700">Your affiliate tools are being prepared. Please contact the Nandini team if they do not appear shortly.</p>
                    @endif
                </div>

                <section id="affiliate-click-analytics-section" class="mt-10" aria-labelledby="click-analytics-heading">
                    <div class="flex flex-col gap-5 border-b border-slate-200 pb-6 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-[0.1em] text-[#A88444]">Link performance</p>
                            <h2 id="click-analytics-heading" class="mt-2 text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">Click analytics</h2>
                        </div>

                        <form method="GET" action="{{ route('affiliate.dashboard') }}" class="w-full sm:w-auto">
                            <input type="hidden" name="bookings" value="{{ $bookingFilter }}">
                            <label for="analytics-range" class="block text-xs font-medium uppercase tracking-[0.08em] text-slate-600">Date range</label>
                            <select id="analytics-range" name="range" data-dashboard-filter data-filter-target="#affiliate-click-analytics-section" class="mt-2 w-full border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 focus:border-[#A88444] focus:outline-none disabled:cursor-wait disabled:opacity-60 sm:min-w-48">
                                <option value="7" @selected($analyticsRange==='7' )>Last 7 days</option>
                                <option value="30" @selected($analyticsRange==='30' )>Last 30 days</option>
                                <option value="90" @selected($analyticsRange==='90' )>Last 90 days</option>
                                <option value="all" @selected($analyticsRange==='all' )>All time</option>
                            </select>
                        </form>
                    </div>

                    @if ($analytics['summary']['total'] === 0)
                    <div class="mt-6 border-l-4 border-[#A88444] bg-white px-5 py-6 sm:px-7">
                        <h3 class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">No link activity yet.</h3>
                        <p class="mt-3 max-w-2xl text-xs leading-relaxed text-gray-600 sm:text-sm">Share your affiliate link with your audience. Click activity will appear here after people begin using your link.</p>
                    </div>
                    @else
                    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ([
                        ['label' => 'Total Clicks', 'value' => number_format($analytics['summary']['total'])],
                        ['label' => 'Unique Clicks', 'value' => number_format($analytics['summary']['unique'])],
                        ['label' => 'Clicks This Month', 'value' => number_format($analytics['summary']['this_month'])],
                        ['label' => 'Top Country', 'value' => $analytics['summary']['top_country'] ?: 'No country data yet'],
                        ] as $metric)
                        <div class="border border-slate-200 bg-white px-5 py-5">
                            <p class="text-xs font-medium uppercase tracking-[0.08em] text-slate-500">{{ $metric['label'] }}</p>
                            <p class="mt-3 text-2xl font-medium text-slate-950">{{ $metric['value'] }}</p>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-6 grid gap-6 lg:grid-cols-2">
                        <div class="border border-slate-200 bg-white px-5 py-6 sm:px-7">
                            <h3 class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">Country breakdown</h3>
                            <div class="mt-5 overflow-x-auto">
                                <table class="w-full min-w-80 text-left text-sm">
                                    <thead class="border-b border-slate-200 text-xs uppercase tracking-[0.08em] text-slate-500">
                                        <tr>
                                            <th class="pb-3 font-medium">Country</th>
                                            <th class="pb-3 text-right font-medium">Clicks</th>
                                            <th class="pb-3 text-right font-medium">Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-slate-700">
                                        @foreach ($analytics['countries'] as $country)
                                        <tr>
                                            <td class="py-3 pr-4">{{ $country['country'] }}</td>
                                            <td class="py-3 text-right">{{ number_format($country['clicks']) }}</td>
                                            <td class="py-3 text-right">{{ number_format($country['percentage'], 1) }}%</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="border border-slate-200 bg-white px-5 py-6 sm:px-7">
                            <h3 class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">Device breakdown</h3>
                            <div class="mt-5 space-y-5">
                                @foreach ($analytics['devices'] as $device)
                                <div>
                                    <div class="flex items-center justify-between gap-4 text-sm text-slate-700">
                                        <span>{{ $device['label'] }}</span>
                                        <span>{{ number_format($device['clicks']) }} · {{ number_format($device['percentage'], 1) }}%</span>
                                    </div>
                                    <div class="mt-2 h-2 overflow-hidden bg-slate-100">
                                        <div class="h-full bg-[#A88444]" style="width: {{ min(100, $device['percentage']) }}%"></div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 border border-slate-200 bg-white px-5 py-6 sm:px-7">
                        <h3 class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">Click trend</h3>
                        <div class="mt-5 max-h-[32rem] overflow-auto">
                            <table class="w-full min-w-96 text-left text-sm">
                                <thead class="sticky top-0 border-b border-slate-200 bg-white text-xs uppercase tracking-[0.08em] text-slate-500">
                                    <tr>
                                        <th class="pb-3 font-medium">Date</th>
                                        <th class="pb-3 text-right font-medium">Total Clicks</th>
                                        <th class="pb-3 text-right font-medium">Unique Clicks</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                    @foreach ($analytics['trend'] as $point)
                                    <tr>
                                        <td class="py-3 pr-4">{{ $point['label'] }}</td>
                                        <td class="py-3 text-right">{{ number_format($point['total']) }}</td>
                                        <td class="py-3 text-right">{{ number_format($point['unique']) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </section>

                <section id="affiliate-bookings-section" class="mt-12" aria-labelledby="affiliate-bookings-heading">
                    @if ($bookingDataMayBeStale)
                    <div class="mb-6 border border-amber-300 bg-amber-50 px-4 py-3 text-xs leading-6 text-amber-900 sm:text-sm" role="status">
                        Booking information is currently being refreshed and may not include the latest activity.
                    </div>
                    @endif
                    <div class="flex flex-col gap-5 border-b border-slate-200 pb-6 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-[0.1em] text-[#A88444]">Stay performance</p>
                            <h2 id="affiliate-bookings-heading" class="mt-2 text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">Tracked bookings</h2>
                        </div>

                        <form method="GET" action="{{ route('affiliate.dashboard') }}" class="w-full sm:w-auto">
                            <input type="hidden" name="range" value="{{ $analyticsRange }}">
                            <label for="booking-filter" class="block text-xs font-medium uppercase tracking-[0.08em] text-slate-600">Booking view</label>
                            <select id="booking-filter" name="bookings" data-dashboard-filter data-filter-target="#affiliate-bookings-section" class="mt-2 w-full border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 focus:border-[#A88444] focus:outline-none disabled:cursor-wait disabled:opacity-60 sm:min-w-48">
                                <option value="upcoming" @selected($bookingFilter==='upcoming' )>Upcoming</option>
                                <option value="completed" @selected($bookingFilter==='completed' )>Completed</option>
                                <option value="ineligible" @selected($bookingFilter==='ineligible' )>Not Eligible</option>
                                <option value="all" @selected($bookingFilter==='all' )>All</option>
                            </select>
                        </form>
                    </div>

                    <div class="mt-6 grid gap-4 sm:grid-cols-3">
                        <div class="border border-slate-200 bg-white px-5 py-5">
                            <p class="text-xs font-medium uppercase tracking-[0.08em] text-slate-500">Tracked Bookings</p>
                            <p class="mt-3 text-2xl font-medium text-slate-950">{{ number_format($bookingAnalytics['summary']['tracked_bookings']) }}</p>
                        </div>
                        <div class="border border-slate-200 bg-white px-5 py-5">
                            <p class="text-xs font-medium uppercase tracking-[0.08em] text-slate-500">Room Nights</p>
                            <p class="mt-3 text-2xl font-medium text-slate-950">{{ number_format($bookingAnalytics['summary']['room_nights']) }}</p>
                        </div>
                        <div class="border border-slate-200 bg-white px-5 py-5">
                            <p class="text-xs font-medium uppercase tracking-[0.08em] text-slate-500">Estimated Commission</p>
                            @forelse ($bookingAnalytics['summary']['commission_totals'] as $total)
                            <p class="mt-3 text-xl font-medium text-slate-950">{{ $money->format($total['amount'], $total['currency']) }}</p>
                            @empty
                            @php
                            $commissionStates = $bookingAnalytics['summary']['commission_states'];
                            $unavailableCommissions = $commissionStates[\App\Enums\AffiliateCommissionState::CalculationUnavailable->value] ?? 0;
                            $ineligibleCommissions = $commissionStates[\App\Enums\AffiliateCommissionState::Ineligible->value] ?? 0;
                            @endphp
                            <p class="mt-3 text-base font-medium text-slate-600">
                                @if ($unavailableCommissions > 0)
                                Pending calculation
                                @elseif ($ineligibleCommissions > 0)
                                Not eligible
                                @else
                                No commission yet
                                @endif
                            </p>
                            @endforelse
                        </div>
                    </div>

                    @if ($bookingAnalytics['bookings']->isEmpty())
                    <div class="mt-6 border-l-4 border-[#A88444] bg-white px-5 py-6 sm:px-7">
                        <h3 class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">No bookings yet</h3>
                    </div>
                    @else
                    <div class="mt-6 space-y-4">
                        @foreach ($bookingAnalytics['bookings'] as $booking)
                        <article class="border border-slate-200 bg-white px-5 py-5 sm:px-7 sm:py-6">
                            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-5">
                                <div class="sm:col-span-2 lg:col-span-1">
                                    <p class="text-xs font-medium uppercase tracking-[0.08em] text-slate-500">Room Type</p>
                                    <p class="mt-2 text-sm font-medium leading-6 text-slate-950">{{ $booking->roomTypesLabel() }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium uppercase tracking-[0.08em] text-slate-500">Length of Stay</p>
                                    <p class="mt-2 text-sm font-medium text-slate-950">{{ $booking->stay_nights }} {{ Str::plural('night', $booking->stay_nights) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium uppercase tracking-[0.08em] text-slate-500">Check-in</p>
                                    <p class="mt-2 text-sm font-medium text-slate-950">{{ $booking->check_in_date->format('d M Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium uppercase tracking-[0.08em] text-slate-500">Check-out</p>
                                    <p class="mt-2 text-sm font-medium text-slate-950">{{ $booking->check_out_date->format('d M Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium uppercase tracking-[0.08em] text-slate-500">Estimated Commission</p>
                                    @if ($booking->commission_state === \App\Enums\AffiliateCommissionState::Ineligible)
                                    <p class="mt-2 text-sm font-medium text-slate-700">{{ $booking->commissionStatusLabel() }}</p>
                                    @elseif ($booking->commission_state === \App\Enums\AffiliateCommissionState::CalculationUnavailable)
                                    <p class="mt-2 text-sm font-medium text-slate-700">Pending calculation</p>
                                    @else
                                    <p class="mt-2 text-sm font-medium text-slate-950">{{ $money->format($booking->estimated_commission_amount, $booking->currency) }}</p>
                                    @if ($booking->commission_state !== \App\Enums\AffiliateCommissionState::PendingValidation)
                                    <p class="mt-1 text-xs leading-5 text-slate-600">{{ $booking->commission_state->label() }}</p>
                                    @endif
                                    @endif
                                </div>
                            </div>
                        </article>
                        @endforeach
                    </div>

                    @if ($bookingAnalytics['bookings']->hasPages())
                    <div class="mt-6">{{ $bookingAnalytics['bookings']->links() }}</div>
                    @endif
                    @endif
                </section>

                <section class="mt-12" aria-labelledby="commission-summary-heading">
                    <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-[0.1em] text-[#A88444]">Commission and payouts</p>
                            <h2 id="commission-summary-heading" class="mt-2 text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">Commission summary</h2>
                        </div>
                        <button type="button" class="inline-flex w-fit items-center justify-center border border-[#A88444] px-4 py-2 text-xs font-medium uppercase tracking-[0.08em] text-[#8B6B35] transition hover:bg-[#A88444] hover:text-white sm:text-sm" @click="approvedWelcomeOpen = true; doNotShowApprovedWelcomeAgain = false">
                            Terms &amp; Conditions
                        </button>
                    </div>

                    @foreach ($finance['notices'] as $notice)
                    <div class="mt-5 border-l-4 border-amber-400 bg-amber-50 px-5 py-4 text-sm leading-6 text-slate-700">
                        {{ $notice['message'] }}
                        @if ($notice['type'] === 'profile')
                        <a href="{{ route('affiliate.payment-details.edit') }}" class="ml-1 font-medium text-[#8B6B35] underline underline-offset-4">Add payment details</a>
                        @endif
                    </div>
                    @endforeach

                    <div class="mt-6 grid gap-4 sm:grid-cols-3">
                        @foreach ([
                        'estimated' => 'Estimated',
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        ] as $key => $label)
                        <div class="border border-slate-200 bg-white px-5 py-5">
                            <p class="text-xs font-medium uppercase tracking-[0.08em] text-slate-500">{{ $label }}</p>
                            @forelse ($finance['summary'][$key] as $total)
                            <p class="mt-3 text-lg font-medium text-slate-950">{{ $money->format($total['amount'], $total['currency']) }}</p>
                            @empty
                            <p class="mt-3 text-base font-medium text-slate-500">No balance</p>
                            @endforelse
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-10 flex items-center justify-between gap-4 border-b border-slate-200 pb-4">
                        <h3 class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">Commission history</h3>
                        <a href="{{ route('affiliate.payment-details.edit') }}" class="text-sm font-medium text-[#8B6B35] underline-offset-4 hover:underline">Payment details</a>
                    </div>
                    <div data-history="commission" @click.prevent="loadHistoryPage($event, 'commission')" :class="historyLoading === 'commission' ? 'opacity-50 pointer-events-none' : ''" class="transition-opacity">
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[52rem] text-left text-sm">
                                <thead class="border-b border-slate-200 text-xs uppercase tracking-[0.08em] text-slate-500">
                                    <tr>
                                        <th class="py-3 pr-4 font-medium">Reference Code</th>
                                        <th class="py-3 pr-4 font-medium">Stay Period</th>
                                        <th class="py-3 pr-4 font-medium">Room Type</th>
                                        <th class="py-3 pr-4 font-medium">Commission Amount</th>
                                        <th class="py-3 font-medium">Commission Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                    @forelse ($finance['commissionHistory'] as $booking)
                                    @php
                                    $item = $booking->commissionItem;
                                    $commissionAmount = $item?->approved_commission_amount ?? $item?->original_commission_amount ?? $booking->estimated_commission_amount;
                                    $status = $booking->commission_state === \App\Enums\AffiliateCommissionState::Ineligible
                                    ? $booking->commissionStatusLabel()
                                    : ($item?->status?->label() ?? $booking->commission_state->label());
                                    @endphp
                                    <tr>
                                        <td class="py-4 pr-4">{{ $booking->external_booking_reference ?: $booking->external_booking_id }}</td>
                                        <td class="py-4 pr-4">{{ $booking->check_in_date->format('d M Y') }} – {{ $booking->check_out_date->format('d M Y') }}</td>
                                        <td class="py-4 pr-4">{{ $booking->roomTypesLabel() }}</td>
                                        <td class="py-4 pr-4">
                                            {{ $commissionAmount !== null ? $money->format($commissionAmount, $booking->currency) : 'Pending calculation' }}
                                        </td>
                                        <td class="py-4">{{ $status }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="py-5 text-slate-500">No commission history yet.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($finance['commissionHistory']->hasPages()) <div class="mt-5">{{ $finance['commissionHistory']->links('vendor.pagination.affiliate') }}</div> @endif
                    </div>

                    <h3 class="mt-10 border-b border-slate-200 pb-4 text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">Payout history</h3>
                    <div data-history="payout" @click.prevent="loadHistoryPage($event, 'payout')" :class="historyLoading === 'payout' ? 'opacity-50 pointer-events-none' : ''" class="transition-opacity">
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[54rem] text-left text-sm">
                                <thead class="border-b border-slate-200 text-xs uppercase tracking-[0.08em] text-slate-500">
                                    <tr>
                                        <th class="py-3 pr-4 font-medium">Payout Number</th>
                                        <th class="py-3 pr-4 font-medium">Amount</th>
                                        <th class="py-3 pr-4 font-medium">Currency</th>
                                        <th class="py-3 pr-4 font-medium">Payment Method</th>
                                        <th class="py-3 pr-4 font-medium">Status</th>
                                        <th class="py-3 pr-4 font-medium">Payment Date</th>
                                        <th class="py-3 font-medium">Payment Reference</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                    @forelse ($finance['payoutHistory'] as $payout)
                                    <tr>
                                        <td class="py-4 pr-4">{{ $payout->payout_number }}</td>
                                        <td class="py-4 pr-4">{{ $money->format($payout->net_payout_amount, $payout->currency) }}@if (bccomp($payout->adjustment_amount, '0.00', 2) !== 0)<span class="block text-xs text-slate-500">Includes payout adjustment</span>@endif</td>
                                        <td class="py-4 pr-4">{{ $payout->currency }}</td>
                                        <td class="py-4 pr-4">{{ $payout->payment_details_masked_snapshot }}</td>
                                        <td class="py-4 pr-4">{{ $payout->status->label() }}</td>
                                        <td class="py-4 pr-4">{{ $payout->paid_at?->format('d M Y') ?? '—' }}</td>
                                        <td class="py-4">{{ $payout->paid_at ? ($payout->payment_reference ?: '—') : '—' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="py-5 text-slate-500">No payout history yet.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($finance['payoutHistory']->hasPages()) <div class="mt-5">{{ $finance['payoutHistory']->links('vendor.pagination.affiliate') }}</div> @endif
                    </div>
                </section>
                @elseif ($affiliate->isRejected())
                <div class="mt-8 max-w-3xl border-l-4 border-slate-400 bg-white px-5 py-5 sm:px-6" data-affiliate-status="rejected">
                    <h2 class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">Account not active</h2>
                    <p class="mt-3 text-xs leading-relaxed text-gray-600 sm:text-sm">
                        This affiliate account is not active. Please contact the Nandini team if you need assistance.
                    </p>
                    @if ($affiliate->rejection_reason)
                    <p class="mt-3 text-xs leading-relaxed text-gray-600 sm:text-sm"><span class="font-medium text-slate-900">Reason:</span> {{ $affiliate->rejection_reason }}</p>
                    @endif
                </div>
                @elseif ($affiliate->isSuspended())
                <div class="mt-8 max-w-3xl border-l-4 border-red-500 bg-red-50 px-5 py-5 sm:px-6" data-affiliate-status="suspended">
                    <h2 class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">Account suspended</h2>
                    <p class="mt-3 text-xs leading-relaxed text-gray-600 sm:text-sm">
                        This affiliate account is temporarily suspended. Active affiliate tools are unavailable. Please contact the Nandini team for assistance.
                    </p>
                </div>
                @endif
            </div>
        </section>

        @if ($showPendingReviewModal)
        <div x-cloak x-show="pendingReviewOpen" x-transition.opacity class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/55 px-5 py-8" role="dialog" aria-modal="true" aria-labelledby="pending-review-title" data-pending-review-modal>
            <button type="button" class="absolute inset-0 cursor-default" aria-label="Close pending review message" @click="pendingReviewOpen = false"></button>

            <div x-show="pendingReviewOpen" x-transition class="relative w-full max-w-lg border border-slate-200 bg-white px-6 py-8 shadow-2xl sm:px-9 sm:py-10">
                <button type="button" class="absolute right-4 top-4 flex h-9 w-9 items-center justify-center text-2xl leading-none text-slate-500 transition hover:text-slate-950" aria-label="Close" @click="pendingReviewOpen = false">&times;</button>
                <p class="text-xs font-medium uppercase tracking-[0.12em] text-[#A88444] sm:text-sm">Nandini Partner Circle</p>
                <h2 id="pending-review-title" class="mt-3 text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">Thank you for registering.</h2>
                <p class="mt-5 text-xs leading-relaxed text-gray-600 sm:text-sm">
                    {{ $reviewTimeMessage }}
                </p>
                <button type="button" class="mt-7 inline-flex items-center justify-center border border-[#A88444] bg-[#A88444] px-5 py-3 text-xs font-medium uppercase tracking-[0.08em] text-white transition hover:border-[#B8945B] hover:bg-[#B8945B] sm:text-sm" @click="pendingReviewOpen = false">
                    Close
                </button>
            </div>
        </div>
        @endif

        @if ($affiliate->isApproved())
        <div x-cloak x-show="approvedWelcomeOpen" x-transition.opacity @keydown.escape.window="closeApprovedWelcome()" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/55 px-4 py-6 sm:px-6" role="dialog" aria-modal="true" aria-labelledby="approved-welcome-title" aria-describedby="approved-welcome-description" data-approved-welcome-modal>
            <button type="button" class="absolute inset-0 cursor-default" aria-label="Close welcome message" @click="closeApprovedWelcome()"></button>

            <div x-show="approvedWelcomeOpen" x-transition class="relative max-h-[90vh] w-full max-w-3xl overflow-y-auto border border-slate-200 bg-white px-6 py-8 shadow-2xl sm:px-9 sm:py-10">
                <button type="button" class="absolute right-4 top-4 flex h-9 w-9 items-center justify-center text-2xl leading-none text-slate-500 transition hover:text-slate-950 focus:outline-none focus:ring-2 focus:ring-[#A88444]" aria-label="Close" @click="closeApprovedWelcome()">&times;</button>

                <p class="text-xs font-medium uppercase tracking-[0.12em] text-[#A88444] sm:text-sm">Nandini Partner Circle</p>
                <h2 id="approved-welcome-title" class="mt-3 pr-10 text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">Welcome to the Affiliate Dashboard</h2>
                <div id="approved-welcome-description" class="mt-5 space-y-3 text-xs leading-relaxed text-gray-600 sm:text-sm">
                    <p>Welcome to the Nandini Partner Circle Affiliate Dashboard.</p>
                    <p>Here, you can monitor the performance of your affiliate account, including bookings, revenue, and commissions generated through your unique referral link or affiliate ID.</p>
                    <p>Keep sharing your referral link to grow your bookings and maximize your commission earnings.</p>
                </div>

                <section class="mt-7" aria-labelledby="approved-welcome-track-title">
                    <h3 id="approved-welcome-track-title" class="text-sm font-medium uppercase tracking-[0.08em] text-slate-700 sm:text-base">What You Can Track</h3>
                    <ul class="mt-3 list-disc space-y-2 pl-5 text-xs leading-relaxed text-gray-600 marker:text-[#A88444] sm:text-sm">
                        <li>Total bookings generated through your affiliate link or affiliate ID</li>
                        <li>Booking status and check-out dates</li>
                        <li>Commission earned for each eligible booking</li>
                        <li>Monthly commission summary</li>
                        <li>Payment status and payment history</li>
                    </ul>
                </section>

                <section class="mt-7" aria-labelledby="approved-welcome-payment-title">
                    <h3 id="approved-welcome-payment-title" class="text-sm font-medium uppercase tracking-[0.08em] text-slate-700 sm:text-base">Commission Payment Terms</h3>
                    <ul class="mt-3 list-disc space-y-2 pl-5 text-xs leading-relaxed text-gray-600 marker:text-[#A88444] sm:text-sm">
                        <li>Commission is calculated based on the guest's check-out date, not the booking date.</li>
                        <li>Commission is payable on room revenue only. Revenue from food &amp; beverage, spa, activities, transportation, or other ancillary services is excluded.</li>
                        <li>Commission payments are processed monthly with a minimum payout of IDR 1,000,000.</li>
                        <li>If your commission for a month is less than IDR 1,000,000, the balance will automatically roll over to the following month(s) until the minimum payout threshold is reached.</li>
                        <li>Eligible commissions are paid within two weeks of the following month.<span class="mt-1 block text-slate-500">Example: Bookings with a January check-out will be paid by the second week of February.</span></li>
                        <li>Payments are made via Wise to your registered bank account.</li>
                    </ul>
                </section>

                <section class="mt-7" aria-labelledby="approved-welcome-notes-title">
                    <h3 id="approved-welcome-notes-title" class="text-sm font-medium uppercase tracking-[0.08em] text-slate-700 sm:text-base">Important Notes</h3>
                    <ul class="mt-3 list-disc space-y-2 pl-5 text-xs leading-relaxed text-gray-600 marker:text-[#A88444] sm:text-sm">
                        <li>Only completed stays (checked-out bookings) are eligible for commission.</li>
                        <li>Cancelled, no-show, or refunded bookings are not commissionable.</li>
                        <li>Please ensure your payment details in your affiliate profile are accurate to avoid payment delays.</li>
                    </ul>
                </section>

                <form x-ref="approvedWelcomeDismissForm" method="POST" action="{{ route('affiliate.dashboard.welcome.dismiss') }}" class="mt-8 border-t border-slate-200 pt-6">
                    @csrf
                    <label class="flex cursor-pointer items-start gap-3 text-xs leading-6 text-slate-700 sm:text-sm">
                        <input x-model="doNotShowApprovedWelcomeAgain" name="do_not_show_again" type="checkbox" value="1" class="mt-1 h-4 w-4 shrink-0 border-slate-300 text-[#A88444] focus:ring-[#A88444]">
                        <span>Do not show this message again</span>
                    </label>

                    <button type="button" class="mt-6 inline-flex w-full items-center justify-center border border-[#A88444] bg-[#A88444] px-5 py-3 text-xs font-medium uppercase tracking-[0.08em] text-white transition hover:border-[#B8945B] hover:bg-[#B8945B] focus:outline-none focus:ring-2 focus:ring-[#A88444] focus:ring-offset-2 sm:w-auto sm:text-sm" @click="closeApprovedWelcome()">
                        Continue to Dashboard
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>

    <script>
        (() => {
            const bindDashboardFilters = () => {
                document.querySelectorAll('[data-dashboard-filter]').forEach((select) => {
                    if (select.dataset.filterBound === 'true') return;

                    select.dataset.filterBound = 'true';
                    select.addEventListener('change', async () => {
                        const form = select.form;
                        const targetSelector = select.dataset.filterTarget;
                        const url = new URL(form.action, window.location.href);

                        new FormData(form).forEach((value, key) => url.searchParams.set(key, value));
                        select.disabled = true;
                        select.setAttribute('aria-busy', 'true');

                        try {
                            const response = await fetch(url, {
                                headers: {
                                    'Accept': 'text/html',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            });

                            if (!response.ok) throw new Error('Dashboard filter request failed.');

                            const page = new DOMParser().parseFromString(await response.text(), 'text/html');
                            const currentSection = document.querySelector(targetSelector);
                            const updatedSection = page.querySelector(targetSelector);

                            if (!currentSection || !updatedSection) throw new Error('Dashboard filter response was incomplete.');

                            currentSection.replaceWith(updatedSection);
                            window.history.replaceState({}, '', url);
                            bindDashboardFilters();
                        } catch (error) {
                            select.disabled = false;
                            select.removeAttribute('aria-busy');
                            console.error(error);
                        }
                    });
                });
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', bindDashboardFilters, { once: true });
            } else {
                bindDashboardFilters();
            }
        })();
    </script>
</x-layouts.affiliate>
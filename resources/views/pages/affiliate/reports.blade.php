@push('meta')
<title>Reports | Nandini Partner Circle</title>
<meta name="robots" content="noindex,nofollow">
@endpush

<x-layouts.affiliate>
    <section class="min-h-[70vh] px-5 py-12 sm:px-8 sm:py-16 lg:px-10">
        <div class="mx-auto w-full max-w-6xl">
            <p class="text-xs font-medium uppercase tracking-[0.12em] text-[#A88444] sm:text-sm">Private performance</p>
            <h1 class="mt-3 text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">Reports</h1>
            <p class="mt-5 max-w-3xl text-xs leading-relaxed text-gray-600 sm:text-sm">A privacy-safe view of your own tracked activity, commission, and payouts. Money remains separated by currency.</p>

            <form method="GET" class="mt-8 grid gap-4 border border-slate-200 bg-white px-5 py-6 sm:grid-cols-2 sm:px-7 lg:grid-cols-5">
                <div>
                    <label for="range" class="block text-xs font-medium uppercase tracking-[0.08em] text-slate-600">Date range</label>
                    <select id="range" name="range" class="mt-2 w-full border border-slate-300 bg-white px-3 py-2.5 text-sm" onchange="this.form.submit()">
                        @foreach (['this_month' => 'This Month', 'last_month' => 'Last Month', 'last_3_months' => 'Last 3 Months', 'this_year' => 'This Year', 'custom' => 'Custom Range'] as $value => $label)
                            <option value="{{ $value }}" @selected($range->preset === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label for="from" class="block text-xs font-medium uppercase tracking-[0.08em] text-slate-600">From</label><input id="from" type="date" name="from" value="{{ $range->from->toDateString() }}" class="mt-2 w-full border border-slate-300 px-3 py-2.5 text-sm"></div>
                <div><label for="to" class="block text-xs font-medium uppercase tracking-[0.08em] text-slate-600">To</label><input id="to" type="date" name="to" value="{{ $range->to->toDateString() }}" class="mt-2 w-full border border-slate-300 px-3 py-2.5 text-sm"></div>
                <div class="self-end"><button type="submit" class="inline-flex min-h-11 w-full items-center justify-center border border-[#A88444] bg-[#A88444] px-4 text-xs font-medium uppercase tracking-[0.08em] text-white">Apply</button></div>
                <div class="self-end text-sm text-slate-600">{{ $range->from->format('d M Y') }} – {{ $range->to->format('d M Y') }}</div>
            </form>

            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([['Total Clicks', $summary['total_clicks']], ['Unique Clicks', $summary['unique_clicks']], ['Tracked Bookings', $summary['tracked_bookings']], ['Room Nights', $summary['room_nights']]] as [$label, $value])
                    <div class="border border-slate-200 bg-white px-5 py-5"><p class="text-xs font-medium uppercase tracking-[0.08em] text-slate-500">{{ $label }}</p><p class="mt-3 text-2xl font-medium text-slate-950">{{ number_format($value) }}</p></div>
                @endforeach
            </div>

            <div class="mt-4 grid gap-4 sm:grid-cols-3">
                @foreach (['estimated' => 'Estimated', 'pending' => 'Pending', 'paid' => 'Paid'] as $key => $label)
                    <div class="border border-slate-200 bg-white px-5 py-5"><p class="text-xs font-medium uppercase tracking-[0.08em] text-slate-500">{{ $label }}</p>@forelse ($summary[$key] as $total)<p class="mt-3 text-lg font-medium text-slate-950">{{ $money->format($total['amount'], $total['currency']) }}</p>@empty<p class="mt-3 text-sm text-slate-500">No balance</p>@endforelse</div>
                @endforeach
            </div>

        </div>
    </section>
</x-layouts.affiliate>

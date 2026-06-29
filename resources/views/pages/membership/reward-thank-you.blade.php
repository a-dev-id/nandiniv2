@push('meta')
@php
$metaTitle = $page->meta_title ?: $page->title;

$metaDescription = $page->meta_description
?: \Illuminate\Support\Str::limit(strip_tags($page->description ?: $page->excerpt ?: ''), 160, '');

$metaImage = $page->hero_image ?: $page->hero_mobile_image ?: null;
@endphp

<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">

<meta name="author" content="Nandini Jungle by Hanging Gardens">
<link rel="canonical" href="{{ url()->current() }}">

<meta property="og:type" content="website">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="Nandini Jungle by Hanging Gardens">

@if ($metaImage)
<meta property="og:image" content="{{ asset('storage/' . $metaImage) }}">
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
@endpush

<x-layouts.app>
    <x-heroes.membership-hero :page="$page" :show-content="false" :show-overlay="false" />

    <x-sections.page-description :page="$page" />

    <section class="bg-white px-6 pb-14 md:px-12 md:pb-20 lg:px-[70px]">
        <div class="mx-auto w-full max-w-3xl text-center">
            <div class="border border-slate-200 bg-slate-50 px-6 py-8">
                <p class="text-xs uppercase text-slate-500 sm:text-sm">
                    Redemption Code
                </p>

                <p class="mt-2 text-xl md:text-3xl font-semibold text-slate-950 sm:text-2xl">
                    {{ $redemption->redemption_code }}
                </p>

                <div class="mt-8 grid gap-5 text-left md:grid-cols-2">
                    <div class="border border-slate-200 bg-white px-5 py-5">
                        <p class="text-xs uppercase text-slate-500 sm:text-sm">
                            Reward
                        </p>

                        <p class="mt-2 text-sm font-medium text-slate-950 sm:text-base">
                            {{ $redemption->reward_name }}
                        </p>
                    </div>

                    <div class="border border-slate-200 bg-white px-5 py-5">
                        <p class="text-xs uppercase text-slate-500 sm:text-sm">
                            Points Used
                        </p>

                        <p class="mt-2 text-sm font-medium text-slate-950 sm:text-base">
                            {{ number_format((int) $redemption->points_used, 0) }}
                        </p>
                    </div>
                </div>

                @if ($redemption->expires_at)
                <div class="mt-2 border border-slate-200 bg-white px-5 py-5 text-left">
                    <p class="text-xs uppercase text-slate-500 sm:text-sm">
                        Valid Until
                    </p>

                    <p class="mt-2 text-sm text-slate-950 sm:text-base">
                        {{ $redemption->expires_at->format('d F Y') }}
                    </p>
                </div>
                @endif

                <p class="mt-2 text-xs leading-relaxed text-slate-600 sm:text-sm">
                    Please save this code and show it to our team when claiming your reward.
                </p>

                <div class="mt-2 border border-[#eee8df] bg-white px-5 py-5 text-left">
                    <p class="text-xs font-semibold uppercase text-[#A88444] sm:text-sm">
                        Terms & Conditions
                    </p>

                    <ul class="mt-3 list-disc space-y-2 pl-5 text-xs leading-relaxed text-slate-600 sm:text-sm">
                        <li>Experience redemption is subject to availability on the preferred date and time</li>
                        <li>The voucher is valid for one month from the date of redemption</li>
                        <li>This voucher cannot be used in conjunction with any other offers, promotions, or discounts</li>
                        <li>To enjoy your experience, please present your unique voucher code to our team upon redemption</li>
                        <li>Any voucher not utilized before its expiry date will be considered void and cannot be reinstated</li>
                        <li>Unused experiences, whether in full or in part, are non-refundable and non-transferable for credit</li>
                        <li>Vouchers hold no cash value and cannot be exchanged for cash or monetary compensation</li>
                    </ul>
                </div>
            </div>

            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('membership.dashboard') }}" class="inline-flex w-full sm:w-auto min-w-[150px] items-center justify-center border border-[#A88444] bg-[#A88444] px-5 py-2.5 text-xs uppercase text-white transition hover:border-[#B8945B] hover:bg-[#B8945B] tracking-[0.08em] font-medium sm:text-sm">
                    Go to Dashboard
                </a>

                <a href="{{ route('membership.privilege-redemption') }}" class="inline-flex w-full sm:w-auto min-w-[150px] items-center justify-center border border-slate-950 px-5 py-2.5 text-xs uppercase text-slate-950 transition hover:border-[#B8945B] hover:bg-[#B8945B] hover:text-white tracking-[0.08em] font-medium sm:text-sm">
                    Redeem More Points
                </a>
            </div>
        </div>
    </section>
</x-layouts.app>

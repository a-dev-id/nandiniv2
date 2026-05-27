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

    <section class="bg-white px-6 pb-20 md:px-12 lg:px-[70px]">
        <div class="mx-auto w-full max-w-3xl text-center">
            <div class="border border-slate-200 bg-slate-50 px-6 py-8">
                <p class="text-sm uppercase tracking-[0.18em] text-slate-500">
                    Redemption Code
                </p>

                <p class="mt-4 text-2xl md:text-3xl font-semibold tracking-[0.12em] text-slate-950">
                    {{ $redemption->redemption_code }}
                </p>

                <div class="mt-8 grid gap-5 text-left md:grid-cols-2">
                    <div class="border border-slate-200 bg-white px-5 py-5">
                        <p class="text-xs uppercase tracking-[0.16em] text-slate-500">
                            Reward
                        </p>

                        <p class="mt-2 text-base font-medium text-slate-950">
                            {{ $redemption->reward_name }}
                        </p>
                    </div>

                    <div class="border border-slate-200 bg-white px-5 py-5">
                        <p class="text-xs uppercase tracking-[0.16em] text-slate-500">
                            Points Used
                        </p>

                        <p class="mt-2 text-base font-medium text-slate-950">
                            {{ number_format((int) $redemption->points_used, 0) }}
                        </p>
                    </div>
                </div>

                @if ($redemption->expires_at)
                <div class="mt-5 border border-slate-200 bg-white px-5 py-5 text-left">
                    <p class="text-xs uppercase tracking-[0.16em] text-slate-500">
                        Valid Until
                    </p>

                    <p class="mt-2 text-base text-slate-950">
                        {{ $redemption->expires_at->format('d F Y') }}
                    </p>
                </div>
                @endif

                <p class="mt-6 text-sm leading-relaxed text-slate-600">
                    Please save this code and show it to our team when claiming your reward.
                </p>
            </div>

            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('membership.dashboard') }}" class="inline-flex w-full sm:w-auto min-w-[190px] items-center justify-center border border-[#b1823b] bg-[#b1823b] px-7 py-4 text-sm uppercase tracking-[0.14em] text-white hover:bg-white hover:text-[#b1823b] transition">
                    Go to Dashboard
                </a>

                <a href="{{ route('membership.privilege-redemption') }}" class="inline-flex w-full sm:w-auto min-w-[190px] items-center justify-center border border-slate-950 px-7 py-4 text-sm uppercase tracking-[0.14em] text-slate-950 hover:border-[#b1823b] hover:bg-[#b1823b] hover:text-white transition">
                    Redeem More Points
                </a>
            </div>
        </div>
    </section>
</x-layouts.app>
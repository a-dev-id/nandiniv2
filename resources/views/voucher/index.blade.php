@push('meta')
@php
    $metaTitle = $landingPage?->meta_title ?: $landingPage?->title ?: 'Nandini Jungle Vouchers';
    $metaDescription = $landingPage?->meta_description ?: $landingPage?->excerpt ?: 'Purchase elegant Nandini Jungle by Hanging Gardens vouchers for yourself or someone special.';
    $heroImagePath = $landingPage?->hero_image ?: $landingPage?->hero_mobile_image;
    $heroImageUrl = $heroImagePath ? Storage::disk('public')->url($heroImagePath) : null;
@endphp
<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
<link rel="canonical" href="{{ route('voucher.index') }}">
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ route('voucher.index') }}">
<meta property="og:site_name" content="Nandini Jungle by Hanging Gardens">
@if ($heroImageUrl)
<meta property="og:image" content="{{ $heroImageUrl }}">
<meta name="twitter:image" content="{{ $heroImageUrl }}">
@endif
<meta name="twitter:card" content="summary_large_image">
@endpush

<x-layouts.app>
    @if ($landingPage)
        <x-heroes.image-hero :page="$landingPage" />
        <x-sections.page-description :page="$landingPage" />
    @else
        <section class="bg-white px-6 py-14 text-center md:py-20">
            <div class="mx-auto max-w-3xl">
                <h1 class="text-xl font-medium uppercase text-slate-700 sm:text-2xl">Gift Voucher</h1>
                <p class="mt-4 text-sm leading-7 text-slate-600">Choose a refined voucher for yourself or someone special.</p>
            </div>
        </section>
    @endif

    <section id="featured-vouchers" class="bg-[#F7F7F7] px-6 py-14 md:py-20">
        <div class="mx-auto max-w-6xl">
            <h2 class="text-center text-xl uppercase text-slate-700 sm:text-2xl">Most Popular</h2>
            @if ($featuredVouchers->isEmpty())
                <p class="mt-8 text-center text-sm text-slate-600">Voucher sales will open soon.</p>
            @else
                <div class="item-carousel-wrap relative mx-auto mt-9 px-0 lg:px-16">
                    <div class="itemcarousel-slick">
                        @foreach ($featuredVouchers as $voucher)
                            <div class="flex h-full px-3">
                                @include('voucher.partials.card', ['voucher' => $voucher])
                            </div>
                        @endforeach
                    </div>

                    <button type="button" class="itemcarousel-prev fold-carousel-arrow fold-image-carousel-arrow home-mobile-image-arrow absolute left-2 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center bg-[#A88444] text-white transition hover:bg-[#B8945B] md:h-12 md:w-12 lg:left-0" aria-label="Previous voucher">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"></path>
                        </svg>
                    </button>

                    <button type="button" class="itemcarousel-next fold-carousel-arrow fold-image-carousel-arrow home-mobile-image-arrow absolute right-2 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center bg-[#A88444] text-white transition hover:bg-[#B8945B] md:h-12 md:w-12 lg:right-0" aria-label="Next voucher">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"></path>
                        </svg>
                    </button>
                </div>
            @endif
        </div>
    </section>

    <section class="bg-white px-6 py-14 md:py-20">
        <div class="mx-auto max-w-6xl" x-data="{ visibleCount: 9 }">
            <h2 class="text-center text-xl uppercase text-slate-700 sm:text-2xl">All Experiences</h2>

            @if ($allVouchers->isEmpty())
                <p class="mt-8 text-center text-sm text-slate-600">Voucher experiences will be available soon.</p>
            @else
                <div class="mt-9 grid gap-7 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($allVouchers as $voucher)
                        <div x-cloak x-show="{{ $loop->index }} < visibleCount" x-transition.opacity class="flex h-full">
                            @include('voucher.partials.card', ['voucher' => $voucher])
                        </div>
                    @endforeach
                </div>

                @if ($allVouchers->count() > 9)
                    <div class="mt-14 flex justify-center">
                        <button type="button" x-show="visibleCount < {{ $allVouchers->count() }}" x-on:click="visibleCount += 9" class="inline-flex items-center justify-center border border-slate-900 px-7 py-3 text-xs font-semibold uppercase tracking-[0.08em] text-slate-700 transition hover:border-[#B8945B] hover:bg-[#B8945B] hover:text-white sm:text-sm">
                            Show More
                        </button>
                    </div>
                @endif
            @endif
        </div>
    </section>
</x-layouts.app>

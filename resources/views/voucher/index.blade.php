@push('meta')
@php
    $metaTitle = $landingPage?->meta_title ?: $landingPage?->title ?: 'Nandini Jungle Vouchers';
    $metaDescription = $landingPage?->meta_description ?: $landingPage?->excerpt ?: 'Purchase elegant Nandini Jungle by Hanging Gardens vouchers for yourself or someone special.';
    $heroImageUrl = $landingPage?->hero_image ? asset('storage/' . $landingPage->hero_image) : asset('images/home/hero.jpg');
@endphp
<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
<link rel="canonical" href="{{ route('voucher.index') }}">
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
@endpush

<x-layouts.app>
    <section class="relative min-h-[76vh] bg-slate-950">
        <picture>
            @if ($landingPage?->hero_mobile_image)
                <source media="(max-width: 767px)" srcset="{{ asset('storage/' . $landingPage->hero_mobile_image) }}">
            @endif
            <img src="{{ $heroImageUrl }}" alt="{{ $landingPage?->hero_image_alt ?: 'Nandini Jungle by Hanging Gardens' }}" class="absolute inset-0 h-full w-full object-cover opacity-65" onerror="this.style.display='none'">
        </picture>
        <div class="absolute inset-0 bg-black/35"></div>
    </section>

    <section class="bg-white px-6 py-14 md:py-20">
        <div class="mx-auto max-w-6xl">
            <div class="mx-auto max-w-3xl text-center">
                <h2 class="text-xl uppercase text-slate-700 sm:text-2xl">{{ $landingPage?->title ?: 'Reserve a Nandini Moment' }}</h2>
                @if ($landingPage?->subtitle)
                    <p class="mt-3 text-base italic leading-7 text-slate-600">{{ $landingPage->subtitle }}</p>
                @endif
                <div class="mt-4 text-sm leading-7 text-slate-600 [&_p]:mb-4 [&_p:last-child]:mb-0">
                    {!! $landingPage?->description ?: '<p>Choose a refined voucher for yourself or as a gift, add the voucher holder details, then complete payment securely through Flywire. Vouchers are issued only after confirmed payment.</p>' !!}
                </div>
            </div>
        </div>
    </section>

    @if ($categories->isNotEmpty())
    <section class="bg-[#F7F7F7] px-6 py-14 md:py-20">
        <div class="mx-auto max-w-6xl">
            <h2 class="text-center text-xl uppercase text-slate-700 sm:text-2xl">Voucher Categories</h2>
            <div class="mt-9 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($categories as $category)
                    @php
                        $categoryDescription = trim(strip_tags((string) $category->description));
                    @endphp
                    <a href="{{ route('voucher.category.show', $category) }}" class="block border border-slate-200 bg-white p-6 transition hover:border-[#A88444]">
                        <h3 class="text-base uppercase text-slate-700">{{ $category->name }}</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-600">{{ $categoryDescription ?: 'Explore available Nandini experiences.' }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <section id="featured-vouchers" class="bg-white px-6 py-14 md:py-20">
        <div class="mx-auto max-w-6xl">
            <h2 class="text-center text-xl uppercase text-slate-700 sm:text-2xl">Featured Vouchers</h2>
            @if ($featuredVouchers->isEmpty())
                <p class="mt-8 text-center text-sm text-slate-600">Voucher sales will open soon.</p>
            @else
                <div class="mt-9 grid gap-7 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($featuredVouchers as $voucher)
                        @include('voucher.partials.card', ['voucher' => $voucher])
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="bg-[#F7F7F7] px-6 py-14 md:py-20">
        <div class="mx-auto grid max-w-6xl gap-8 md:grid-cols-2">
            <div>
                <h2 class="text-xl uppercase text-slate-700 sm:text-2xl">Terms Summary</h2>
                <p class="mt-4 text-sm leading-7 text-slate-600">Validity, inclusions, and redemption rules are shown on each voucher. Full terms are included in the issued voucher PDF.</p>
            </div>
            <div>
                <h2 class="text-xl uppercase text-slate-700 sm:text-2xl">FAQ</h2>
                <p class="mt-4 text-sm leading-7 text-slate-600">Vouchers are delivered after payment confirmation. You may purchase for yourself or send the voucher to another holder.</p>
            </div>
        </div>
    </section>
</x-layouts.app>

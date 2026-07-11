@push('meta')
<title>Nandini Jungle Vouchers</title>
<meta name="description" content="Purchase elegant Nandini Jungle by Hanging Gardens vouchers for yourself or someone special.">
<link rel="canonical" href="{{ route('voucher.index') }}">
<meta property="og:type" content="website">
<meta property="og:title" content="Nandini Jungle Vouchers">
<meta property="og:description" content="Purchase Nandini Jungle by Hanging Gardens vouchers for yourself or someone special.">
@endpush

<x-layouts.app>
    <section class="relative min-h-[76vh] bg-slate-950">
        <img src="{{ asset('images/home/hero.jpg') }}" alt="Nandini Jungle by Hanging Gardens" class="absolute inset-0 h-full w-full object-cover opacity-65" onerror="this.style.display='none'">
        <div class="absolute inset-0 bg-black/35"></div>
        <div class="relative mx-auto flex min-h-[76vh] max-w-6xl flex-col justify-end px-6 pb-16 pt-36 text-white md:pb-20">
            <p class="text-xs uppercase tracking-[0.18em]">Nandini Jungle by Hanging Gardens</p>
            <h1 class="mt-4 max-w-3xl text-3xl uppercase leading-tight sm:text-5xl">Nandini Jungle Vouchers</h1>
            <p class="mt-5 max-w-2xl text-sm leading-7 text-white/90 sm:text-base">Purchase a spa ritual, dining moment, or jungle experience for yourself or someone special.</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="#featured-vouchers" class="inline-flex items-center justify-center border border-[#A88444] bg-[#A88444] px-5 py-3 text-xs font-medium uppercase tracking-[0.08em] text-white transition hover:border-[#B8945B] hover:bg-[#B8945B] sm:text-sm">Browse Vouchers</a>
            </div>
        </div>
    </section>

    <section class="bg-white px-6 py-14 md:py-20">
        <div class="mx-auto max-w-6xl">
            <div class="mx-auto max-w-3xl text-center">
                <h2 class="text-xl uppercase text-slate-700 sm:text-2xl">Reserve a Nandini Moment</h2>
                <p class="mt-4 text-sm leading-7 text-slate-600">Choose a refined voucher for yourself or as a gift, add the voucher holder details, then complete payment securely through Flywire. Vouchers are issued only after confirmed payment.</p>
            </div>

            <div class="mt-10 grid gap-5 md:grid-cols-3">
                @foreach (['Choose a voucher', 'Select self or gift', 'Receive by email'] as $step)
                    <div class="border border-slate-200 bg-[#F7F7F7] p-6 text-center">
                        <h3 class="text-base uppercase text-slate-700">{{ $step }}</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-600">A calm, secure purchase flow designed for personal use and thoughtful gifting.</p>
                    </div>
                @endforeach
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

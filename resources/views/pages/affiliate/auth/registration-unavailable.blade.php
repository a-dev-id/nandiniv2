@push('meta')
<title>Registration Temporarily Unavailable | Nandini Partner Circle</title>
<meta name="description" content="Nandini Partner Circle registration is temporarily unavailable.">
<meta name="robots" content="noindex,nofollow">
@endpush

<x-layouts.app>
    <x-heroes.image-hero
        :image-src="asset('images/membership/join-today.webp')"
        alt-text="Nandini Partner Circle"
    />

    <section class="w-full bg-[#F7F7F7] px-6 py-16 md:py-24 lg:py-28" aria-labelledby="registration-unavailable-heading">
        <div class="mx-auto max-w-2xl bg-white px-6 py-10 text-center shadow-xl sm:px-10 md:py-12">
            <p class="text-xs font-medium uppercase tracking-[0.12em] text-[#A88444] sm:text-sm">Nandini Partner Circle</p>
            <h1 id="registration-unavailable-heading" class="mb-3 mt-4 text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">Registration Temporarily Unavailable</h1>
            <p class="mx-auto max-w-xl text-xs leading-relaxed text-gray-600 sm:text-sm">New Affiliate registration is temporarily unavailable. Please check back later. Existing partners can continue to sign in.</p>
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <a href="{{ route('affiliate.landing') }}" class="inline-flex items-center justify-center border border-slate-950 bg-transparent px-4 py-2 text-xs font-medium uppercase tracking-[0.08em] text-slate-950 transition hover:border-[#B8945B] hover:bg-[#B8945B] hover:text-white sm:text-sm">Affiliate Program</a>
                <a href="{{ route('affiliate.login') }}" class="inline-flex items-center justify-center border border-[#A88444] bg-[#A88444] px-4 py-2 text-xs font-medium uppercase tracking-[0.08em] text-white transition hover:border-[#B8945B] hover:bg-[#B8945B] sm:text-sm">Partner Login</a>
            </div>
        </div>
    </section>
</x-layouts.app>

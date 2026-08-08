@php
    $pageTitle = $page?->title ?: 'Join the Nandini Partner Circle';
    $pageDescription = $page?->description ?: '<p>Share the beauty of Nandini Jungle by Hanging Gardens with your audience and earn rewards for every successful referral.</p><p>The Nandini Partner Circle is our official affiliate program designed for travel creators, bloggers, publishers, travel advisors, wellness professionals, and anyone passionate about inspiring unforgettable travel experiences.</p>';
    $metaTitle = $page?->meta_title ?: ($page ? $pageTitle : 'Nandini Partner Circle | Affiliate Program');
    $metaDescription = $page?->meta_description ?: ($page?->excerpt ?: 'Share the beauty of Nandini Jungle by Hanging Gardens with your audience and earn rewards for every successful referral.');
    $fallbackHeroImage = asset('images/membership/join-today.webp');
    $heroImage = $page?->hero_image ?: $page?->hero_mobile_image;
    $heroMobileImage = $page?->hero_mobile_image ?: $page?->hero_image;
    $heroImageUrl = $heroImage ? \Illuminate\Support\Facades\Storage::disk('public')->url($heroImage) : $fallbackHeroImage;
    $heroMobileImageUrl = $heroMobileImage ? \Illuminate\Support\Facades\Storage::disk('public')->url($heroMobileImage) : $heroImageUrl;
    $heroImageAlt = $page?->hero_image_alt ?: $page?->hero_mobile_image_alt ?: 'Traveler overlooking the pool and lush jungle at Nandini Jungle by Hanging Gardens';
    $metaImage = $heroImageUrl;
@endphp

@push('meta')
<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
<meta name="author" content="Nandini Jungle by Hanging Gardens">
<link rel="canonical" href="{{ route('affiliate.landing') }}">
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ route('affiliate.landing') }}">
<meta property="og:site_name" content="Nandini Jungle by Hanging Gardens">
<meta property="og:image" content="{{ $metaImage }}">
<meta property="og:image:alt" content="Nandini Jungle by Hanging Gardens surrounded by the Bali jungle">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
<meta name="twitter:image" content="{{ $metaImage }}">
@endpush

<x-layouts.app>
    <x-heroes.membership-hero
        :image-src="$heroImageUrl"
        :mobile-image-src-manual="$heroMobileImageUrl"
        :alt-text="$heroImageAlt"
        :show-content="false"
        :show-overlay="false"
    />

    <section class="bg-white px-6 py-14 md:py-20" aria-labelledby="affiliate-intro-heading">
        <div class="mx-auto max-w-[950px] text-center">
            <h1 id="affiliate-intro-heading" class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">
                {{ $pageTitle }}
            </h1>

            <div class="affiliate-page-description mx-auto mt-8 max-w-4xl space-y-3 text-xs leading-relaxed text-gray-600 sm:text-sm">
                {!! $pageDescription !!}
            </div>

        </div>
    </section>

    <section class="bg-slate-50 px-6 py-14 md:py-20" aria-labelledby="affiliate-benefits-heading">
        <div class="mx-auto max-w-6xl">
            <div class="mx-auto max-w-4xl text-center">
                <h2 id="affiliate-benefits-heading" class="mb-3 text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">Why Join?</h2>
                <p class="mt-6 text-xs leading-relaxed text-gray-600 sm:text-sm">As a Nandini Partner, you'll enjoy:</p>
            </div>

            <ol class="mt-12 grid border-t border-[#d8c49a] md:grid-cols-2">
                @foreach ([
                    "Earn up to {$commissionPercentage}% commission on every completed stay booked through your referral.",
                    "Exclusive up to {$guestDiscountPercentage}% guest discount, giving your audience an added reason to book with you.",
                    'Personalized affiliate link and referral code for seamless booking tracking.',
                    'Dedicated Affiliate dashboard to review tracked bookings, room nights, and commission status after booking synchronization.',
                    'Monthly payout processing for qualified completed stays, subject to Finance validation and the applicable payout threshold.',
                    "No joining fee or sales target—it's free to join and you earn based on your performance.",
                ] as $benefit)
                    <li class="flex gap-5 border-b border-[#d8c49a]/70 py-7 md:px-8 md:py-8 md:odd:pl-0 md:even:border-l md:even:pr-0">
                        <span class="shrink-0 pt-0.5 text-xs font-medium text-[#b28a4a] sm:text-sm" aria-hidden="true">
                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                        </span>
                        <p class="text-xs leading-relaxed text-gray-600 sm:text-sm">{{ $benefit }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <section class="bg-white px-6 py-16 md:py-24">
        <div class="mx-auto max-w-4xl text-center">
            <p class="mx-auto max-w-3xl text-xs leading-relaxed text-gray-600 sm:text-sm">
                Become part of our growing community of trusted partners and introduce travelers to one of Bali's most tranquil jungle retreats. It's a simple way to monetize your audience while helping guests enjoy exclusive savings when they book directly with Nandini Jungle by Hanging Gardens.
            </p>

            <div class="mt-8 flex flex-wrap justify-center gap-4">
                @if ($showLoginCta)
                    @if ($registrationEnabled)
                        <a href="{{ $primaryCtaUrl }}" class="inline-flex items-center justify-center border border-slate-950 bg-transparent px-3 py-2 text-[10px] font-medium uppercase tracking-[0.08em] text-slate-950 transition duration-300 hover:border-[#B8945B] hover:bg-[#B8945B] hover:text-white sm:px-4 sm:text-sm lg:px-5">
                            {{ $primaryCtaLabel }}
                        </a>
                    @else
                        <span class="inline-flex cursor-not-allowed items-center justify-center border border-slate-300 bg-slate-100 px-3 py-2 text-[10px] font-medium uppercase tracking-[0.08em] text-slate-500 sm:px-4 sm:text-sm lg:px-5" aria-disabled="true">
                            {{ $primaryCtaLabel }}
                        </span>
                    @endif
                    <a href="{{ route('affiliate.login') }}" class="inline-flex items-center justify-center border border-[#A88444] bg-[#A88444] px-3 py-2 text-[10px] font-medium uppercase tracking-[0.08em] text-white transition duration-300 hover:border-[#B8945B] hover:bg-[#B8945B] hover:text-white sm:px-4 sm:text-sm lg:px-5">
                        Login
                    </a>
                @else
                    <a href="{{ $primaryCtaUrl }}" class="inline-flex items-center justify-center border border-slate-950 bg-transparent px-3 py-2 text-[10px] font-medium uppercase tracking-[0.08em] text-slate-950 transition duration-300 hover:border-[#B8945B] hover:bg-[#B8945B] hover:text-white sm:px-4 sm:text-sm lg:px-5">
                        {{ $primaryCtaLabel }}
                    </a>
                @endif
            </div>
        </div>
    </section>
</x-layouts.app>

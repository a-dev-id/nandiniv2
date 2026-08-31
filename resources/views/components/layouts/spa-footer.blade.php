@php
$spaHomeUrl = route('spa-site.home');
$mainDomainBase = rtrim(request()->getScheme() . '://' . config('domains.main'), '/');
$mainRoute = fn (string $name): string => $mainDomainBase . route($name, [], false);
$navigation = [
    ['label' => 'Home', 'href' => $spaHomeUrl],
    ['label' => 'Spa & Wellness', 'href' => $spaHomeUrl . '#wellness'],
    ['label' => 'Offers', 'href' => $mainRoute('offers.index')],
    ['label' => 'Contact', 'href' => $mainRoute('contact.index')],
];
@endphp

<footer class="bg-black text-white">
    <div class="mx-auto grid max-w-7xl gap-10 px-6 py-14 text-center md:grid-cols-3 md:px-10 md:text-left lg:py-16">
        <div class="flex flex-col items-center md:items-start">
            <a href="{{ $spaHomeUrl }}" aria-label="Nandini Spa home"><img src="{{ asset('images/logo-njhg.png') }}" alt="Nandini Jungle by Hanging Gardens" class="h-auto w-40 brightness-0 invert" loading="lazy"></a>
            <a href="{{ $mainDomainBase }}" class="mt-6 text-xs uppercase tracking-[0.12em] text-white/80 transition hover:text-white">Visit the main Nandini website</a>
        </div>
        <div>
            <h2 class="text-base uppercase sm:text-lg">Explore</h2>
            <nav class="mt-5 flex flex-col items-center gap-3 text-xs text-white/80 md:items-start sm:text-sm" aria-label="Spa footer navigation">
                @foreach ($navigation as $item)
                <a href="{{ $item['href'] }}" class="transition hover:text-white hover:underline">{{ $item['label'] }}</a>
                @endforeach
            </nav>
        </div>
        <div>
            <h2 class="text-base uppercase sm:text-lg">Contact</h2>
            <div class="mt-5 space-y-3 text-xs leading-6 text-white/80 sm:text-sm">
                <p>Banjar Susut, Desa Buahan, Payangan, Bali 80571, Indonesia</p>
                <p><a href="tel:+6281236871170" class="hover:text-white hover:underline">+62 812 3687 1170</a></p>
                <p><a href="mailto:reservation@nandinibali.com" class="break-all hover:text-white hover:underline">reservation@nandinibali.com</a></p>
            </div>
        </div>
    </div>
    <div class="border-t border-white/15 px-6 py-5 text-center text-[11px] leading-5 text-white/65 sm:text-xs">Copyright © {{ date('Y') }} Nandini Jungle by Hanging Gardens.</div>
</footer>

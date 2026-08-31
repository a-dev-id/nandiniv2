@props(['page' => null])

@php
$spaHomeUrl = route('spa-site.home');
$mainDomainBase = rtrim(request()->getScheme() . '://' . config('domains.main'), '/');
$mainRoute = fn (string $name): string => $mainDomainBase . route($name, [], false);
$contactUrl = $mainRoute('contact.index');
$hasHero = $page && ($page->hero_image || $page->hero_mobile_image);
$navigation = [
    ['label' => 'Home', 'href' => $spaHomeUrl],
    ['label' => 'Spa', 'href' => $spaHomeUrl . '#spa'],
    ['label' => 'Wellness', 'href' => $spaHomeUrl . '#wellness'],
    ['label' => 'Offers', 'href' => $mainRoute('offers.index')],
    ['label' => 'Contact', 'href' => $contactUrl],
];
@endphp

<div x-data="{ open: false, setOpen(value) { this.open = value; document.documentElement.classList.toggle('overflow-hidden', value); document.body.classList.toggle('overflow-hidden', value); } }" x-on:keydown.escape.window="setOpen(false)" x-on:resize.window="if (window.innerWidth >= 1024) setOpen(false)">
    <nav class="absolute inset-x-0 top-0 z-50 border-b {{ $hasHero ? 'border-white/20 bg-black/30 text-white backdrop-blur-sm' : 'border-slate-200 bg-white text-slate-700 shadow-sm' }}" aria-label="Spa navigation">
        <div class="mx-auto flex h-20 max-w-[1440px] items-center px-4 sm:px-6 lg:h-24 lg:px-10 2xl:px-14">
            <a href="{{ $spaHomeUrl }}" class="shrink-0" aria-label="Nandini Spa home">
                <img src="{{ asset('images/logo-njhg.png') }}" alt="Nandini Jungle by Hanging Gardens" class="h-14 w-auto {{ $hasHero ? 'brightness-0 invert' : '' }} lg:h-20" width="250" height="104">
            </a>
            <div class="ml-auto hidden items-center gap-6 lg:flex xl:gap-8">
                @foreach ($navigation as $item)
                <a href="{{ $item['href'] }}" class="text-xs font-medium uppercase tracking-[0.12em] transition hover:text-[#A88444] xl:text-sm">{{ $item['label'] }}</a>
                @endforeach
                <a href="{{ $contactUrl }}" class="inline-flex min-h-11 items-center justify-center border border-[#A88444] bg-[#A88444] px-5 text-xs font-medium uppercase tracking-[0.12em] text-white transition hover:border-[#B8945B] hover:bg-[#B8945B] xl:text-sm">Book Now</a>
            </div>
            <div class="ml-auto flex items-center gap-2 lg:hidden">
                <a href="{{ $contactUrl }}" class="inline-flex min-h-11 items-center justify-center border border-[#A88444] bg-[#A88444] px-3 text-[11px] font-medium uppercase tracking-[0.1em] text-white transition hover:bg-[#B8945B] sm:px-4 sm:text-xs">Book Now</a>
                <button type="button" class="inline-flex h-11 w-11 items-center justify-center" x-on:click="setOpen(true)" aria-label="Open navigation menu" :aria-expanded="open.toString()" aria-controls="spa-mobile-menu">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" /></svg>
                </button>
            </div>
        </div>
    </nav>
    <div x-cloak x-show="open" x-transition.opacity class="fixed inset-0 z-[60] bg-black/50 lg:hidden" x-on:click="setOpen(false)" aria-hidden="true"></div>
    <div id="spa-mobile-menu" x-cloak x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="fixed inset-y-0 right-0 z-[70] flex w-[min(88vw,380px)] flex-col overflow-y-auto bg-white px-6 pb-8 pt-6 text-slate-700 shadow-2xl lg:hidden" role="dialog" aria-modal="true" aria-label="Spa mobile navigation">
        <div class="flex items-center justify-between">
            <img src="{{ asset('images/logo-njhg.png') }}" alt="Nandini Jungle by Hanging Gardens" class="h-16 w-auto" width="250" height="104">
            <button type="button" class="inline-flex h-11 w-11 items-center justify-center" x-on:click="setOpen(false)" aria-label="Close navigation menu">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18" /></svg>
            </button>
        </div>
        <div class="mt-8 flex flex-1 flex-col">
            @foreach ($navigation as $item)
            <a href="{{ $item['href'] }}" class="border-b border-slate-200 py-4 text-sm font-medium uppercase tracking-[0.12em]" x-on:click="setOpen(false)">{{ $item['label'] }}</a>
            @endforeach
            <a href="{{ $contactUrl }}" class="mt-8 inline-flex min-h-12 items-center justify-center bg-[#A88444] px-5 text-sm font-medium uppercase tracking-[0.12em] text-white transition hover:bg-[#B8945B]" x-on:click="setOpen(false)">Book Now</a>
        </div>
    </div>
</div>

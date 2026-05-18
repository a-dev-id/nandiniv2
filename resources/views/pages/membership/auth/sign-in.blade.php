@push('meta')
@php
$metaTitle = $page->meta_title ?: $page->title;

$metaDescription = $page->meta_description
?: \Illuminate\Support\Str::limit(strip_tags($page->description ?: $page->excerpt ?: ''), 160, '');
@endphp

<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
@endpush

@php
$desktopImage = $page->hero_image ?: $page->hero_mobile_image;
$mobileImage = $page->hero_mobile_image ?: $page->hero_image;

$desktopImageUrl = $desktopImage ? asset('storage/' . $desktopImage) : '';
$mobileImageUrl = $mobileImage ? asset('storage/' . $mobileImage) : '';

$imageAlt = $page->hero_image_alt
?: $page->hero_mobile_image_alt
?: $page->title
?: 'Nandini Inner Circle Sign In';

$title = $page->title ?: 'Sign In';
$subtitle = $page->subtitle ?: 'Nandini Inner Circle';

$description = $page->description
?: $page->excerpt
?: 'Access your membership dashboard, view your rewards, and continue your journey with Nandini Inner Circle.';

$descriptionHasHtml = is_string($description) && $description !== strip_tags($description);
@endphp

<x-layouts.app>
    {{-- HERO IMAGE ONLY --}}
    <header class="relative shadow-xl">
        <div class="relative w-full h-[48vh] min-h-[360px] md:h-[58vh] lg:h-[62vh] overflow-hidden bg-slate-100">
            @if ($desktopImageUrl || $mobileImageUrl)
            <picture class="block h-full w-full">
                @if ($mobileImageUrl)
                <source media="(max-width: 767px)" srcset="{{ $mobileImageUrl }}">
                @endif

                <img src="{{ $desktopImageUrl ?: $mobileImageUrl }}" alt="{{ $imageAlt }}" class="absolute inset-0 h-full w-full object-cover object-center" loading="eager">
            </picture>
            @endif
        </div>
    </header>

    {{-- SIGN IN SECTION --}}
    <section class="w-full bg-[#F7F7F7] py-16 md:py-24 lg:py-28">
        <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto grid max-w-6xl grid-cols-1 items-center gap-10 lg:grid-cols-12 lg:gap-14">

                {{-- LEFT CONTENT --}}
                <div class="lg:col-span-5">
                    <div class="flex h-full flex-col justify-center px-4 sm:px-8 md:px-10 lg:px-0">
                        <div class="text-center lg:text-left">

                            @if ($subtitle)
                            <p class="text-xs sm:text-sm uppercase tracking-[0.22em] text-[#A67C3D]">
                                {{ $subtitle }}
                            </p>
                            @endif

                            <h1 class="mt-4 font-serif text-[38px] uppercase leading-[1.15] tracking-[0.22em] text-slate-900 sm:text-[44px] md:text-[52px] lg:text-[58px]">
                                {{ $title }}
                            </h1>

                            <div class="mt-5 h-px w-20 bg-slate-400/70 mx-auto lg:mx-0"></div>

                            @if ($description)
                            <div class="mt-7 max-w-md text-[15px] leading-7 text-slate-700 mx-auto lg:mx-0 [&_p]:mb-2 [&_p:last-child]:mb-0">
                                @if ($descriptionHasHtml)
                                {!! $description !!}
                                @else
                                {!! nl2br(e($description)) !!}
                                @endif
                            </div>
                            @endif

                        </div>
                    </div>
                </div>

                {{-- FORM CARD --}}
                <div class="lg:col-span-7">
                    <div class="mx-auto w-full max-w-[560px] bg-white px-6 py-8 shadow-xl sm:px-8 md:px-10 md:py-10 lg:px-12">

                        <form method="POST" action="{{ route('membership.login.submit') }}" class="space-y-6">
                            @csrf

                            {{-- Email --}}
                            <div>
                                <label for="email" class="mb-3 block text-xs sm:text-sm uppercase tracking-[0.18em] text-slate-500">
                                    Email Address
                                </label>

                                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" class="w-full border border-slate-300 bg-white px-4 py-3 text-[15px] leading-7 text-slate-700 outline-none transition focus:border-[#A67C3D]">

                                @error('email')
                                <p class="mt-2 text-[14px] leading-6 text-red-600">
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div>
                                <label for="password" class="mb-3 block text-xs sm:text-sm uppercase tracking-[0.18em] text-slate-500">
                                    Password
                                </label>

                                <input id="password" type="password" name="password" required autocomplete="current-password" class="w-full border border-slate-300 bg-white px-4 py-3 text-[15px] leading-7 text-slate-700 outline-none transition focus:border-[#A67C3D]">

                                @error('password')
                                <p class="mt-2 text-[14px] leading-6 text-red-600">
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            {{-- Remember --}}
                            <label class="flex items-center gap-3 text-[15px] leading-7 text-slate-700">
                                <input type="checkbox" name="remember" value="1" class="h-4 w-4 border-slate-300 text-[#A67C3D] focus:ring-[#A67C3D]">

                                <span>Keep me signed in</span>
                            </label>

                            {{-- Submit --}}
                            <button type="submit" class="inline-flex w-full items-center justify-center bg-[#A67C3D] px-7 py-4 text-[12px] font-bold uppercase tracking-[0.22em] text-white transition hover:bg-[#8F6B34]">
                                Sign In
                            </button>

                            {{-- Join Link --}}
                            <div class="pt-2 text-center text-[15px] leading-7 text-slate-700">
                                Not a member yet?

                                <a href="{{ route('membership.register') }}" class="font-bold uppercase tracking-[0.18em] text-[#A67C3D] transition hover:text-[#8F6B34]">
                                    Join Now
                                </a>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </section>
</x-layouts.app>
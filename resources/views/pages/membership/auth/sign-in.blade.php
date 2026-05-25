@push('meta')
@php
$metaTitle = $page->meta_title ?: ($page->title ?: 'Sign In');

$metaDescription = $page->meta_description
?: \Illuminate\Support\Str::limit(
strip_tags($page->description ?: $page->excerpt ?: 'Access your membership dashboard, view your rewards, and continue your journey with Nandini Inner Circle.'),
160,
''
);
@endphp

<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
@endpush

@php
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
    {{-- HERO --}}
    <x-heroes.image-hero :page="$page" :alt-text="$imageAlt" />

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

                        @if (session('status'))
                        <div class="mb-6 border border-green-200 bg-green-50 px-4 py-3 text-[14px] leading-6 text-green-700">
                            {{ session('status') }}
                        </div>
                        @endif

                        @if ($errors->any())
                        <div class="mb-6 border border-red-200 bg-red-50 px-4 py-3 text-[14px] leading-6 text-red-700">
                            <p class="font-semibold">
                                Please check the following:
                            </p>

                            <ul class="mt-2 list-disc space-y-1 pl-5">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form method="POST" action="{{ route('membership.login.submit') }}" class="space-y-6">
                            @csrf

                            {{-- <p class="text-[13px] leading-6 text-slate-500">
                                <span class="text-red-600">*</span> Required fields
                            </p> --}}

                            {{-- Email --}}
                            <div>
                                <label for="email" class="mb-3 block text-xs sm:text-sm uppercase tracking-[0.18em] text-slate-500">
                                    Email Address {{--<span class="text-red-600">*</span>--}}
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
                                    Password {{--<span class="text-red-600">*</span>--}}
                                </label>

                                <div class="relative">
                                    <input id="password" type="password" name="password" required autocomplete="current-password" class="w-full border border-slate-300 bg-white px-4 py-3 pr-14 text-[15px] leading-7 text-slate-700 outline-none transition focus:border-[#A67C3D]">

                                    <button type="button" data-toggle-password="password" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 transition hover:text-[#A67C3D]" aria-label="Show password">
                                        {{-- Eye Open --}}
                                        <svg data-eye-open class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>

                                        {{-- Eye Closed --}}
                                        <svg data-eye-closed class="hidden h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.58 10.58A2 2 0 0 0 12 14a2 2 0 0 0 1.42-.58" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.88 4.74A9.44 9.44 0 0 1 12 4.5c6 0 9.75 7.5 9.75 7.5a17.47 17.47 0 0 1-2.16 3.19" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6.61 6.61C3.78 8.49 2.25 12 2.25 12s3.75 7.5 9.75 7.5a9.7 9.7 0 0 0 4.39-1.04" />
                                        </svg>
                                    </button>
                                </div>

                                @error('password')
                                <p class="mt-2 text-[14px] leading-6 text-red-600">
                                    {{ $message }}
                                </p>
                                @enderror

                                <p class="mt-3 text-[13px] leading-6 text-slate-500">
                                    If your account was created automatically from a booking, please use your booking number as your temporary password.
                                </p>
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleButtons = document.querySelectorAll('[data-toggle-password]');

            toggleButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const inputId = button.getAttribute('data-toggle-password');
                    const input = document.getElementById(inputId);

                    if (!input) {
                        return;
                    }

                    const eyeOpen = button.querySelector('[data-eye-open]');
                    const eyeClosed = button.querySelector('[data-eye-closed]');
                    const isHidden = input.type === 'password';

                    input.type = isHidden ? 'text' : 'password';

                    if (eyeOpen && eyeClosed) {
                        eyeOpen.classList.toggle('hidden', isHidden);
                        eyeClosed.classList.toggle('hidden', !isHidden);
                    }

                    button.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
                });
            });
        });
    </script>
</x-layouts.app>
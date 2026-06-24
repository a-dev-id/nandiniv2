@push('meta')
<title>{{ $page->meta_title ?: $page->title }}</title>
<meta name="description" content="{{ $page->meta_description ?? '' }}">

@if (! empty($page->meta_keywords))
<meta name="keywords" content="{{ $page->meta_keywords }}">
@endif

<meta name="author" content="Nandini Jungle by Hanging Gardens">
<link rel="canonical" href="{{ url()->current() }}">

<meta property="og:type" content="website">
<meta property="og:title" content="{{ $page->meta_title ?: $page->title }}">
<meta property="og:description" content="{{ $page->meta_description ?? '' }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="Nandini Jungle by Hanging Gardens">

@if (! empty($page->hero_image))
<meta property="og:image" content="{{ asset('storage/' . $page->hero_image) }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta name="twitter:image" content="{{ asset('storage/' . $page->hero_image) }}">
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $page->meta_title ?: $page->title }}">
<meta name="twitter:description" content="{{ $page->meta_description ?? '' }}">
@endpush

<x-layouts.app>
    @php
    $descriptionHtml = (string) ($page->description ?? '');
    $descriptionText = trim(strip_tags($descriptionHtml));

    $phone = '+62 812-3687-1170';
    $phoneClean = preg_replace('/[^0-9]/', '', $phone);

    $reservationEmail = 'reservation@nandinibali.com';
    $mediaEmail = 'info@nandinibali.com';

    $address = 'Banjar Susut, Desa Buahan, Payangan, Bali 80571, Indonesia';
    @endphp

    <x-heroes.image-hero :page="$page" />

    <section class="px-6 py-14 md:py-20">
        <div class="mx-auto max-w-6xl text-center">
            <h1 class="text-xl leading-snug uppercase text-slate-700 font-medium mb-3 sm:text-2xl">
                {{ $page->title }}
            </h1>

            @if ($descriptionText !== '')
            <div class="text-xs leading-relaxed text-gray-600 max-w-2xl sm:max-w-3xl md:max-w-5xl mx-auto sm:text-sm">
                {!! $descriptionHtml !!}
            </div>
            @endif

            <div class="mt-16 grid grid-cols-1 gap-10 md:grid-cols-3 md:gap-12">
                {{-- Phone --}}
                <div class="flex flex-col items-center text-center">
                    <div class="mb-4 text-slate-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1C10.61 21 3 13.39 3 4c0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.24.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
                        </svg>
                    </div>

                    <a href="tel:{{ $phoneClean }}" class="text-xs leading-7 text-slate-700 hover:text-[#A67C3D] sm:text-sm">
                        {{ $phone }}
                    </a>
                </div>

                {{-- Address --}}
                <div class="flex flex-col items-center text-center">
                    <div class="mb-4 text-slate-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5z" />
                        </svg>
                    </div>

                    <p class="max-w-xs text-xs leading-7 text-slate-700 sm:text-sm">
                        Banjar Susut, Desa Buahan,<br>
                        Payangan, Bali 80571,<br>
                        Indonesia
                    </p>

                    <a href="https://wa.me/{{ $phoneClean }}" target="_blank" rel="noopener" class="mt-8 inline-flex items-center gap-3 bg-[#004225] px-4 py-2 text-xs font-medium text-white transition hover:bg-[#00351e] tracking-[0.08em] sm:text-sm">
                        Chat with Us

                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 32 32" fill="currentColor">
                            <path d="M16.02 3C8.84 3 3 8.82 3 15.98c0 2.29.6 4.53 1.75 6.5L3 29l6.68-1.72A13.02 13.02 0 0 0 16.02 29C23.18 29 29 23.18 29 16.02S23.18 3 16.02 3zm0 23.8c-2.02 0-4-.54-5.73-1.57l-.41-.24-3.96 1.02 1.06-3.86-.27-.43a10.71 10.71 0 0 1-1.51-5.74c0-5.96 4.85-10.8 10.82-10.8s10.8 4.84 10.8 10.8-4.84 10.82-10.8 10.82zm5.94-8.1c-.33-.17-1.93-.95-2.23-1.06-.3-.11-.52-.17-.74.17-.22.33-.85 1.06-1.04 1.28-.19.22-.39.25-.72.08-.33-.17-1.39-.51-2.65-1.63-.98-.87-1.64-1.95-1.83-2.28-.19-.33-.02-.51.14-.67.14-.14.33-.39.5-.58.17-.19.22-.33.33-.55.11-.22.06-.41-.03-.58-.08-.17-.74-1.78-1.02-2.44-.27-.64-.54-.55-.74-.56h-.63c-.22 0-.58.08-.88.41-.3.33-1.16 1.13-1.16 2.76s1.19 3.21 1.35 3.43c.17.22 2.34 3.57 5.67 5.01.79.34 1.41.55 1.89.7.79.25 1.51.21 2.08.13.63-.09 1.93-.79 2.2-1.55.27-.76.27-1.41.19-1.55-.08-.14-.3-.22-.63-.39z" />
                        </svg>
                    </a>
                </div>

                {{-- Email --}}
                <div class="flex flex-col items-center text-center">
                    <div class="mb-4 text-slate-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z" />
                        </svg>
                    </div>

                    <a href="mailto:{{ $reservationEmail }}" class="text-xs leading-7 text-slate-700 hover:text-[#A67C3D] sm:text-sm">
                        {{ $reservationEmail }}
                    </a>
                </div>
            </div>

            <div class="mt-14">
                <h2 class="text-lg leading-snug uppercase font-medium text-slate-700 mb-3 sm:text-xl">
                    Press &amp; Media Partnership
                </h2>

                <a href="mailto:{{ $mediaEmail }}" class="mt-2 inline-block text-xs leading-7 text-slate-700 hover:text-[#A67C3D] sm:text-sm">
                    {{ $mediaEmail }}
                </a>
            </div>
        </div>
    </section>

    <section class="w-full">
        <iframe title="Nandini Jungle by Hanging Gardens Location" src="https://www.google.com/maps?q=Nandini%20Jungle%20by%20Hanging%20Gardens%2C%20Banjar%20Susut%2C%20Desa%20Buahan%2C%20Payangan%2C%20Bali%2080571%2C%20Indonesia&t=k&output=embed" class="h-[420px] w-full border-0 md:h-[520px]" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen>
        </iframe>
    </section>
</x-layouts.app>

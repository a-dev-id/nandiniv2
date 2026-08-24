@php
$metaTitle = $page->meta_title ?: $page->title;
$metaDescription = $page->meta_description ?: $page->excerpt;
$heroImage = 'https://nandinibali.com/storage/pages/hero/74e30cb4-6fec-4883-8b57-c474baa71740.webp';
$utm = 'utm_source=instagram&utm_medium=social&utm_campaign=explore_page&utm_content=bio';
$socialLinks = [
'instagram' => 'https://www.instagram.com/nandinijungleresort/',
'facebook' => 'https://www.facebook.com/nandinijungleresort/',
'youtube' => 'https://www.youtube.com/@NandiniJunglebyHangingGardens',
'tripadvisor' => 'https://www.tripadvisor.com/Hotel_Review-g21379722-d603743-Reviews-Nandini_Jungle_By_Hanging_Gardens-Buahan_Payangan_Gianyar_Regency_Bali.html',
];
$linkClass = 'block w-full rounded-full bg-[#A88444] px-5 py-3 text-center font-serif text-base text-white shadow-[0_10px_20px_rgba(15,23,42,0.18)] transition hover:bg-white hover:text-[#0B2341] focus:outline-none focus:ring-2 focus:ring-[#A88444] focus:ring-offset-2';
$headingClass = 'mt-16 text-center font-serif text-xl uppercase tracking-[0.18em] text-[#0B2341]';
@endphp

<x-layouts.minimal>
    @push('meta')
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:url" content="{{ url()->full() }}">
    <meta property="og:type" content="website">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:image" content="{{ $heroImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="{{ url()->full() }}">
    <meta name="twitter:creator" content="Nandini Jungle by Hanging Gardens">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $heroImage }}">
    <link href="{{ url()->full() }}" rel="canonical">
    <meta name="keywords" content="Ubud Resort, Bali Luxury Resort, Bali Resort with Private Pool">
    @endpush

    <div class="min-h-screen bg-[#F7F3ED] bg-cover bg-center bg-fixed px-5 py-8 text-[#0B2341]" style="background-image: linear-gradient(rgba(247, 243, 237, 0.88), rgba(247, 243, 237, 0.94)), url('{{ $heroImage }}');">
        <div class="mx-auto flex w-full max-w-2xl flex-col items-center">
            <a href="{{ route('home') }}" aria-label="Nandini Jungle by Hanging Gardens home">
                <img class="h-auto w-40" src="{{ asset('images/logo-njhg.png') }}" alt="Nandini Jungle by Hanging Gardens">
            </a>

            {{-- <img class="mt-7 w-full max-w-md object-cover shadow-[0_16px_35px_rgba(15,23,42,0.16)]" src="{{ $heroImage }}" alt="{{ $page->hero_image_alt ?: $page->title }}"> --}}

            <div class="mt-12 w-full space-y-7">
                <a href="{{ route('home') . '?' . $utm }}" target="_blank" rel="noopener" class="{{ $linkClass }}">
                    Official Website
                </a>
                <a href="{{ route('membership.index') . '?' . $utm }}" target="_blank" rel="noopener" class="{{ $linkClass }}">
                    Be a Member
                </a>
                <a href="{{ route('holy-river.index') . '?' . $utm }}" target="_blank" rel="noopener" class="{{ $linkClass }}">
                    Holy River
                </a>
                <a href="https://wa.me/6281236871170" target="_blank" rel="noopener" class="{{ $linkClass }}">
                    WhatsApp
                </a>
                <a href="mailto:reservation@nandinibali.com" target="_blank" rel="noopener" class="{{ $linkClass }}">
                    Email
                </a>
            </div>

            @if ($offers->isNotEmpty())
            <h1 class="{{ $headingClass }}">Exclusive Offers</h1>

            <div class="mt-8 w-full space-y-7">
                @foreach ($offers as $offer)
                <a href="{{ route('offers.show', $offer) . '?' . $utm }}" target="_blank" rel="noopener" class="{{ $linkClass }}">
                    {{ $offer->title }}
                </a>
                @endforeach
            </div>
            @endif

            @if ($experiences->isNotEmpty())
            <h2 class="{{ $headingClass }}">Unique Experiences</h2>

            <div class="mt-8 w-full space-y-7">
                @foreach ($experiences as $experience)
                @php
                $experienceRoute = in_array($experience->slug, [
                    'balinese-blessing-purification-at-the-holy-river',
                    'nandini-signature-spa-on-the-river',
                ], true) ? 'holy-river.show' : 'experiences.show';
                @endphp
                <a href="{{ route($experienceRoute, $experience) . '?' . $utm }}" target="_blank" rel="noopener" class="{{ $linkClass }}">
                    {{ $experience->title }}
                </a>
                @endforeach
            </div>
            @endif

            @if ($blogNews->isNotEmpty())
            <h2 class="{{ $headingClass }}">Blog &amp; News</h2>

            <div class="mt-8 w-full space-y-7">
                @foreach ($blogNews as $article)
                <a href="{{ route('blog.show', $article) . '?' . $utm }}" target="_blank" rel="noopener" class="{{ $linkClass }}">
                    {{ $article->title }}
                </a>
                @endforeach
            </div>
            @endif

            <div class="my-16 flex items-center justify-center gap-5 text-[#A88444]">
                <a href="{{ $socialLinks['instagram'] }}" target="_blank" rel="noopener" aria-label="Instagram" class="transition hover:text-[#0B2341]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 fill-current" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M8 3C5.239 3 3 5.239 3 8v8c0 2.761 2.239 5 5 5h8c2.761 0 5-2.239 5-5V8c0-2.761-2.239-5-5-5H8zm10 2a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm-6 2a5 5 0 1 1 0 10 5 5 0 0 1 0-10zm0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6z" />
                    </svg>
                </a>
                <a href="{{ $socialLinks['facebook'] }}" target="_blank" rel="noopener" aria-label="Facebook" class="transition hover:text-[#0B2341]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 fill-current" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 2C6.477 2 2 6.477 2 12c0 5.013 3.693 9.153 8.505 9.876V14.65H8.031v-2.629h2.474v-1.749c0-2.896 1.411-4.167 3.818-4.167 1.153 0 1.762.085 2.051.124v2.294h-1.642c-1.022 0-1.379.969-1.379 2.061v1.437h2.995l-.406 2.629h-2.588v7.247C18.235 21.236 22 17.062 22 12 22 6.477 17.523 2 12 2z" />
                    </svg>
                </a>
                <a href="{{ $socialLinks['youtube'] }}" target="_blank" rel="noopener" aria-label="YouTube" class="transition hover:text-[#0B2341]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 fill-current" viewBox="0 0 30 30" aria-hidden="true">
                        <path d="M15 4c-4.186 0-9.619 1.049-9.619 1.049A4.004 4.004 0 0 0 2 9v12a4 4 0 0 0 3.377 3.945s5.437 1.057 9.623 1.057 9.619-1.051 9.619-1.051A4 4 0 0 0 28 21V9a4 4 0 0 0-3.377-3.945S19.186 4 15 4zm-3 6.398L20 15l-8 4.602v-9.204z" />
                    </svg>
                </a>
                <a href="{{ $socialLinks['tripadvisor'] }}" target="_blank" rel="noopener" aria-label="Tripadvisor" class="transition hover:text-[#0B2341]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 fill-current" viewBox="0 0 50 50" aria-hidden="true">
                        <path d="M25 11c-5.832 0-11.156 1.512-15.211 4H2s1.754 2.152 2.578 4.578A11.94 11.94 0 0 0 2 27c0 6.629 5.371 12 12 12 3.496 0 6.637-1.508 8.828-3.895L25 38l2.172-2.895C29.363 37.492 32.504 39 36 39c6.629 0 12-5.371 12-12 0-2.805-.969-5.379-2.578-7.422C46.246 17.152 48 15 48 15h-7.797C36.148 12.512 30.828 11 25 11zM14 18a9 9 0 1 1 0 18 9 9 0 0 1 0-18zm22 0a9 9 0 1 1 0 18 9 9 0 0 1 0-18zM14 21a6 6 0 1 0 0 12 6 6 0 0 0 0-12zm22 0a6 6 0 1 0 0 12 6 6 0 0 0 0-12zm-22 2a4 4 0 1 1 0 8 4 4 0 0 1 0-8zm22 0a4 4 0 1 1 0 8 4 4 0 0 1 0-8z" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</x-layouts.minimal>

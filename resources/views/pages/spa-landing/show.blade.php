@php
    $title = $page->meta_title ?: $page->title;
    $description = $page->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($page->excerpt ?: $page->description ?: ''), 160, '');
    $canonical = 'https://'.config('domains.spa').'/'.$page->slug;
    $resolveImage = fn (?string $path): ?string => blank($path)
        ? null
        : (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')
            ? asset($path)
            : \Illuminate\Support\Facades\Storage::disk('public')->url($path));
    $heroImage = $resolveImage($page->hero_image);
    $mobileImage = $resolveImage($page->hero_mobile_image ?: $page->hero_image);
@endphp

@push('meta')
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <link rel="canonical" href="{{ $canonical }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonical }}">
@endpush

<x-layouts.app>
    @if ($heroImage)
        <section class="relative min-h-[560px] overflow-hidden bg-[#173126] text-white">
            <picture class="absolute inset-0 block h-full w-full">
                @if ($mobileImage)<source media="(max-width: 767px)" srcset="{{ $mobileImage }}">@endif
                <img src="{{ $heroImage }}" alt="{{ $page->hero_image_alt }}" class="h-full w-full object-cover" fetchpriority="high">
            </picture>
            <div class="absolute inset-0 bg-black/35" aria-hidden="true"></div>
            <div class="relative flex min-h-[560px] items-center px-6 pb-14 pt-28 md:px-12 lg:px-[clamp(64px,5vw,100px)]">
                <h1 class="max-w-3xl font-span text-4xl leading-tight sm:text-5xl">{{ $page->title }}</h1>
            </div>
        </section>
    @endif

    <section class="px-6 py-14 font-sans md:px-10 md:py-20">
        <div class="mx-auto max-w-5xl">
            @unless ($heroImage)<h1 class="font-span text-4xl text-slate-800">{{ $page->title }}</h1>@endunless
            @if ($page->excerpt)<p class="mt-5 text-sm leading-relaxed text-slate-600">{{ $page->excerpt }}</p>@endif
            @if ($page->description)<div class="prose prose-slate mt-8 max-w-none">{!! $page->description !!}</div>@endif
            @foreach ($page->sections as $section)
                <section class="py-10">
                    @if ($section->title)<h2 class="font-span text-3xl text-slate-800">{{ $section->title }}</h2>@endif
                    @if ($section->description)<div class="prose prose-slate mt-5 max-w-none">{!! $section->description !!}</div>@endif
                </section>
            @endforeach
        </div>
    </section>
</x-layouts.app>

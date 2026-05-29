@props([
'section' => null,
])

@php
$resolveImage = function (?string $raw): string {
$raw = trim((string) $raw);

if ($raw === '') {
return '';
}

if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
return $raw;
}

if (str_starts_with($raw, '/storage/')) {
return $raw;
}

if (str_starts_with($raw, 'storage/')) {
return '/' . $raw;
}

if (str_starts_with($raw, '/')) {
return $raw;
}

return asset('storage/' . $raw);
};

$images = $section?->images?->take(3) ?? collect();

$displayTitle = $section?->title ?? '';
$displayDescription = $section?->description ?: $section?->excerpt ?? '';

$displayButtonText = $section?->button_label ?: 'DISCOVER';

$displayButtonLink = null;

if (($section?->button_link_type ?? 'manual') === 'route' && $section?->button_route) {
$displayButtonLink = \Illuminate\Support\Facades\Route::has($section->button_route)
? route($section->button_route)
: null;
} else {
$displayButtonLink = $section?->button_url;
}
@endphp

@if ($section)
<section class="py-14 md:py-28 overflow-x-hidden">

    {{-- WIDE WRAPPER --}}
    <div class="w-[96%] md:w-[94%] mx-auto">

        {{-- MOBILE --}}
        <div class="md:hidden w-full overflow-x-auto snap-x snap-mandatory flex gap-4 px-1 scroll-px-4">
            @foreach ($images as $image)
            @php
            $mobileImage = $image->mobile_image ?: $image->image;
            $imageUrl = $resolveImage($mobileImage);
            $imageAlt = $image->mobile_image_alt ?: $image->image_alt ?: $displayTitle ?: 'Section image';
            @endphp

            <div class="snap-center shrink-0 w-full">
                <div class="group relative aspect-square overflow-hidden bg-slate-100">
                    @if ($imageUrl)
                    <img src="{{ $imageUrl }}" alt="{{ $imageAlt }}" class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" loading="lazy" />

                    <div class="absolute inset-0 bg-black/0 transition duration-500 group-hover:bg-black/35"></div>

                    <div class="absolute inset-0 flex items-center justify-center px-6 opacity-0 transition duration-500 group-hover:opacity-100">
                        <p class="text-white text-center uppercase tracking-[0.22em] text-sm font-medium">
                            {{ $imageAlt }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        {{-- DESKTOP --}}
        <div class="hidden md:grid grid-cols-3 gap-6">
            @foreach ($images as $image)
            @php
            $desktopImage = $image->image ?: $image->mobile_image;
            $imageUrl = $resolveImage($desktopImage);
            $imageAlt = $image->image_alt ?: $image->mobile_image_alt ?: $displayTitle ?: 'Section image';
            @endphp

            <div class="group relative overflow-hidden aspect-3/4 bg-slate-100">
                @if ($imageUrl)
                <img src="{{ $imageUrl }}" alt="{{ $imageAlt }}" class="absolute inset-0 h-full w-full object-cover object-center transition-transform duration-700 ease-out group-hover:scale-105" loading="lazy" />

                {{-- <div class="absolute inset-0 bg-black/0 transition duration-500 group-hover:bg-black/35"></div>

                <div class="absolute inset-0 flex items-center justify-center px-6 opacity-0 transition duration-500 group-hover:opacity-100">
                    <p class="text-white text-center uppercase tracking-[0.22em] text-sm md:text-base font-medium">
                        {{ $imageAlt }}
                    </p>
                </div> --}}
                @endif
            </div>
            @endforeach
        </div>

    </div>

    {{-- CONTENT --}}
    <div class="text-center mt-10 md:mt-16 max-w-3xl mx-auto px-6">

        @if ($displayTitle)
        <h2 class="text-2xl sm:text-3xl md:text-3xl leading-snug tracking-[0.15em] md:tracking-[0.22em] text-slate-800 uppercase mb-6 font-medium">
            {{ $displayTitle }}
        </h2>
        @endif

        @if ($displayDescription)
        <div class="text-[15px] sm:text-base leading-relaxed text-gray-600 max-w-2xl sm:max-w-3xl md:max-w-5xl mx-auto">
            {!! $displayDescription !!}
        </div>
        @endif

        @if ($displayButtonText && $displayButtonLink)
        <div class="mt-10">
            <a href="{{ $displayButtonLink }}" class="inline-flex items-center justify-center bg-[#A67C3D] text-white px-7 py-3 uppercase tracking-[0.22em] text-[12px] font-bold hover:bg-[#8F6B34] transition">
                {{ $displayButtonText }}
            </a>
        </div>
        @endif

    </div>

</section>
@endif

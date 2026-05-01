@props([
'section' => null,
'reverse' => false,
'boxed' => true,
'noButton' => false,
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

$wrapper = $boxed
? 'w-full max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8'
: 'w-full';

$gridOrderImage = $reverse ? 'lg:order-2' : 'lg:order-1';
$gridOrderText = $reverse ? 'lg:order-1' : 'lg:order-2';

$title = $section?->title ?? '';
$description = $section?->description ?: $section?->excerpt ?? '';

$images = $section?->images?->take(2) ?? collect();

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
<section class="py-14 md:py-28 w-full overflow-x-hidden {{ $reverse ? '' : 'bg-[#F7F7F7]' }}">
    <div class="{{ $wrapper }}">
        <div class="grid grid-cols-1 lg:grid-cols-12 items-stretch gap-8 lg:gap-10">

            {{-- IMAGES --}}
            <div class="lg:col-span-8 {{ $gridOrderImage }}">

                {{-- MOBILE: carousel --}}
                <div class="sm:hidden w-full overflow-x-auto snap-x snap-mandatory flex gap-4 scroll-px-4">
                    @foreach ($images as $image)
                    @php
                    $mobileImage = $image->mobile_image ?: $image->image;
                    $imageUrl = $resolveImage($mobileImage);
                    $imageAlt = $image->mobile_image_alt ?: $image->image_alt ?: $title ?: 'Section image';
                    @endphp

                    <div class="snap-center shrink-0 w-full">
                        <div class="group relative aspect-square md:aspect-3/2 overflow-hidden bg-slate-100">
                            @if ($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $imageAlt }}" class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" loading="lazy" />
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- DESKTOP: 2 images grid --}}
                <div class="hidden sm:grid grid-cols-2 gap-6 h-80 sm:h-96 md:h-105 lg:h-130 xl:h-150">
                    @foreach ($images as $image)
                    @php
                    $desktopImage = $image->image ?: $image->mobile_image;
                    $imageUrl = $resolveImage($desktopImage);
                    $imageAlt = $image->image_alt ?: $image->mobile_image_alt ?: $title ?: 'Section image';
                    @endphp

                    <div class="group relative overflow-hidden h-full bg-slate-100">
                        @if ($imageUrl)
                        <img src="{{ $imageUrl }}" alt="{{ $imageAlt }}" class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" loading="lazy" />
                        @endif
                    </div>
                    @endforeach
                </div>

            </div>

            {{-- TEXT PANEL --}}
            <div class="lg:col-span-4 {{ $gridOrderText }}">
                <div class="h-full flex flex-col justify-center px-4 sm:px-8 md:px-10 lg:px-12 md:py-14">
                    <div class="text-center">

                        @if ($title)
                        <h2 class="text-lg sm:text-xl lg:text-2xl tracking-[0.25em] uppercase text-slate-800 font-medium">
                            {{ $title }}
                        </h2>
                        @endif

                        <div class="mt-4 h-px w-20 bg-slate-400/70 mx-auto"></div>

                        @if ($description)
                        <div class="mt-6 max-w-md text-[15px] leading-7 text-slate-700 mx-auto">
                            {!! $description !!}
                        </div>
                        @endif

                        @if (! $noButton && $displayButtonText && $displayButtonLink)
                        <div class="mt-8">
                            <a href="{{ $displayButtonLink }}" class="inline-flex items-center justify-center bg-[#A67C3D] text-white px-7 py-3 uppercase tracking-[0.22em] text-[12px] font-bold hover:bg-[#8F6B34] transition">
                                {{ $displayButtonText }}
                            </a>
                        </div>
                        @endif

                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endif
@props([
'accommodation' => null,
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

$cleanText = function (?string $value): string {
$text = html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
$text = str_replace("\xc2\xa0", ' ', $text);
$text = preg_replace('/\s+/', ' ', $text);

return trim((string) $text);
};

$title = $accommodation?->subtitle ?: $accommodation?->title;
$description = $cleanText($accommodation?->description ?: $accommodation?->excerpt);
$description = \Illuminate\Support\Str::limit($description, 360, '');
$url = $accommodation?->show_url ?? '#';

$leadImage = $resolveImage($accommodation?->hero_image ?: $accommodation?->card_image);
$leadAlt = $accommodation?->hero_image_alt ?: $accommodation?->card_image_alt ?: $title ?: 'Presidential Royal Suite';

$galleryImages = $accommodation?->activeImages
? $accommodation->activeImages->filter(fn ($image) => filled($image->image))
: collect();
@endphp

@if ($accommodation)
<section class="bg-white px-3 pb-12 md:px-10 md:pb-6">
    <div class="mx-auto border border-slate-200 px-5 py-8 text-center md:px-12 md:py-12">
        @if ($title)
        <h2 class="text-xl font-medium uppercase leading-snug text-slate-700 mb-3">
            {{ $title }}
        </h2>
        @endif

        @if ($leadImage)
        <div class="group mx-auto max-w-5xl overflow-hidden bg-slate-100">
            <img src="{{ $leadImage }}" alt="{{ $leadAlt }}" class="aspect-square md:aspect-[3/2] w-full object-cover object-center transition-transform duration-700 ease-out group-hover:scale-105" loading="lazy">
        </div>
        @endif

        @if ($description)
        <p class="mx-auto mt-4 max-w-5xl text-sm leading-relaxed text-slate-700">
            {{ $description }}
        </p>
        @endif

        @if ($galleryImages->isNotEmpty())
        <x-sections.item-carousel :items="$galleryImages" variant="gallery" wrapper-class="mt-10 -mx-5 md:-mx-5" bottom-padding-class="pb-0" inner-padding-class="lg:px-0" item-padding-class="px-1.5 md:px-2" previous-button-class="left-0 md:left-3 lg:-left-5" next-button-class="right-0 md:right-3 lg:-right-5" button-position-class="top-1/2" />
        @endif

        <div class="mt-5 flex justify-center">
            <x-buttons.link-button :href="$url" class="px-4 py-2">
                Explore
            </x-buttons.link-button>
        </div>
    </div>
</section>
@endif

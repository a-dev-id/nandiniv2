@props(['event', 'image' => null])

<section class="relative aspect-[4/3] w-full overflow-hidden bg-slate-100 lg:aspect-auto lg:h-[70vh]" aria-label="{{ $event->title }}" data-gtm-section="hero">
    @if ($image)
        <img src="{{ $image }}" alt="{{ $event->hero_image_alt }}" class="absolute inset-0 h-full w-full object-cover object-center" width="1920" height="1080" fetchpriority="high" decoding="async">
    @endif
</section>

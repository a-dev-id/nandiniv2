@props(['blog', 'sections'])
@php
    $dimensions = require database_path('content/go-tyme-image-dimensions.php');
@endphp
@push('css')
    @vite(['resources/css/retreat-article.css', 'resources/js/retreat-lightbox.js'])
@endpush
<div class="retreat-story px-6 pb-14 text-slate-700 md:pb-20">
    <div class="mx-auto max-w-7xl">
        @foreach ($sections as $section)
        <section class="my-14 md:my-20" aria-labelledby="retreat-section-{{ $section->id }}">
            <div class="mx-auto mb-8 max-w-3xl">
                <p class="mb-3 text-xs uppercase tracking-[0.08em] text-[#8F6B34]">{{ $section->subtitle }}</p>
                <h2 id="retreat-section-{{ $section->id }}" class="blog-detail-section-title">{{ $section->title }}</h2>
                <div class="blog-detail-content">{!! $section->description !!}</div>
            </div>
            @if ($section->images->isNotEmpty())
            @php
                $featuredId = data_get($section->items, 'featured');
                $featured = $section->images->first(fn ($photo) => substr(basename($photo->image), 3, 5) === $featuredId) ?? $section->images->first();
                $remaining = $section->images->reject(fn ($photo) => $photo->id === $featured->id);
            @endphp
            <figure>
                <x-blog.retreat-photo :photo="['image' => $featured->image, 'alt' => $featured->image_alt]" :dimensions="$dimensions" featured />
                @if (data_get($section->items, 'caption'))
                <figcaption class="mt-3 text-xs leading-relaxed text-slate-500">{{ data_get($section->items, 'caption') }}</figcaption>
                @endif
            </figure>
            @foreach ($remaining->chunk(12) as $batch)
            <div class="retreat-gallery mt-5">
                @foreach ($batch as $photo)
                    <x-blog.retreat-photo :photo="['image' => $photo->image, 'alt' => $photo->image_alt]" :dimensions="$dimensions" />
                @endforeach
            </div>
            @if (! $loop->last)
            <p class="mx-auto my-8 max-w-3xl text-center text-xs uppercase tracking-[0.08em] text-slate-500">{{ $section->title }} · A closer look</p>
            @endif
            @endforeach
            @endif
        </section>
        @endforeach
        <div class="mx-auto max-w-3xl border-t border-[#D8C6A8] pt-10 text-center">
            <x-buttons.link-button :href="route('contact.index')" variant="solid">Explore a Group Retreat</x-buttons.link-button>
            <p class="mt-6"><a class="text-xs uppercase tracking-[0.08em] text-slate-500 underline underline-offset-4" href="{{ route('blog.index') }}">Return to the journal</a></p>
        </div>
    </div>
    <dialog class="retreat-lightbox" aria-label="Retreat photograph viewer" data-retreat-lightbox>
        <button type="button" class="retreat-lightbox-close" data-lightbox-close aria-label="Close photograph viewer">Close <span aria-hidden="true">×</span></button>
        <figure>
            <img data-lightbox-image alt="">
            <figcaption data-lightbox-caption aria-live="polite"></figcaption>
        </figure>
        <div class="retreat-lightbox-controls">
            <button type="button" data-lightbox-previous aria-label="Previous photograph">← Previous</button>
            <span data-lightbox-counter aria-live="polite"></span>
            <button type="button" data-lightbox-next aria-label="Next photograph">Next →</button>
        </div>
    </dialog>
</div>

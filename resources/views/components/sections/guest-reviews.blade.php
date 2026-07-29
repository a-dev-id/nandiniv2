@props([
    'reviews' => collect(),
    'seeMoreHref' => null,
])

@if ($reviews->isNotEmpty())
<section class="bg-white px-6 py-16 text-center md:py-24" aria-labelledby="guest-reviews-title">
    <div class="mx-auto max-w-7xl">
        <h2 id="guest-reviews-title" class="mb-12 text-lg font-medium uppercase text-slate-700 sm:text-xl">
            What Our Guests Say
        </h2>

        <div class="guest-review-slider" data-total="{{ $reviews->count() }}">
            @foreach ($reviews as $review)
            @php
            $displayReview = trim((string) ($review->excerpt ?? ''));

            if ($displayReview === '') {
                $displayReview = html_entity_decode(
                    strip_tags((string) $review->review_text),
                    ENT_QUOTES | ENT_HTML5,
                    'UTF-8'
                );
                $displayReview = trim((string) preg_replace('/\s+/', ' ', $displayReview));
            }
            @endphp
            <article class="px-4">
                <div class="mx-auto flex max-w-4xl flex-col items-center">
                    <div class="mb-5 flex items-center justify-center gap-1 text-[#A88444]" aria-label="{{ $review->rating }} out of 5 stars">
                        @for ($star = 1; $star <= 5; $star++)
                        <svg class="h-5 w-5 {{ $star <= $review->rating ? 'fill-current' : 'fill-slate-200 text-slate-200' }}" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="m10 1.7 2.47 5.01 5.53.8-4 3.9.95 5.51L10 14.32l-4.95 2.6.95-5.51-4-3.9 5.53-.8L10 1.7Z" />
                        </svg>
                        @endfor
                    </div>

                    <blockquote class="font-serif text-lg italic leading-relaxed text-slate-600 sm:text-xl md:text-2xl">
                        “{{ $displayReview }}”
                    </blockquote>

                    <div class="mt-8 text-lg font-semibold text-slate-900 sm:text-xl">
                        {{ $review->reviewer_name }}
                    </div>

                    @if ($review->reviewed_at || $review->source)
                    <div class="mt-1 text-xs text-slate-500 sm:text-sm">
                        @if ($review->reviewed_at)
                        <time datetime="{{ $review->reviewed_at->toDateString() }}">{{ $review->reviewed_at->format('M Y') }}</time>
                        @endif
                        @if ($review->reviewed_at && $review->source)
                        <span aria-hidden="true"> · </span>
                        @endif
                        @if ($review->source)
                        <span>{{ $review->source }}</span>
                        @endif
                    </div>
                    @endif
                </div>
            </article>
            @endforeach
        </div>

        @if ($reviews->count() > 1)
        <div class="mt-12 grid grid-cols-[3rem_1fr_3rem] items-center gap-4">
            <button type="button" class="guest-review-prev flex h-12 w-12 items-center justify-center rounded-full bg-[#A88444] text-white transition hover:bg-[#8f6b34]" aria-label="Previous review">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m15 19-7-7 7-7" />
                </svg>
            </button>

            <div class="guest-review-dots slick-slider flex justify-center"></div>

            <button type="button" class="guest-review-next flex h-12 w-12 items-center justify-center rounded-full bg-[#A88444] text-white transition hover:bg-[#8f6b34]" aria-label="Next review">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m9 5 7 7-7 7" />
                </svg>
            </button>
        </div>
        @endif

        @if ($seeMoreHref)
        <div class="mt-10 text-center">
            <x-buttons.link-button :href="$seeMoreHref" variant="solid" class="min-w-[145px]">
                See More
            </x-buttons.link-button>
        </div>
        @endif
    </div>
</section>
@endif

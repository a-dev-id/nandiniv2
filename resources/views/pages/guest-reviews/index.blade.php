@push('meta')
<title>Guest Reviews | Nandini Jungle by Hanging Gardens</title>
<meta name="description" content="Read guest reviews and experiences from stays at Nandini Jungle by Hanging Gardens in Ubud, Bali.">

<meta name="author" content="Nandini Jungle by Hanging Gardens">
<link rel="canonical" href="{{ route('guest-reviews.index') }}">

<meta property="og:type" content="website">
<meta property="og:title" content="Guest Reviews | Nandini Jungle by Hanging Gardens">
<meta property="og:description" content="Read guest reviews and experiences from stays at Nandini Jungle by Hanging Gardens in Ubud, Bali.">
<meta property="og:url" content="{{ route('guest-reviews.index') }}">
<meta property="og:site_name" content="Nandini Jungle by Hanging Gardens">

<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="Guest Reviews | Nandini Jungle by Hanging Gardens">
<meta name="twitter:description" content="Read guest reviews and experiences from stays at Nandini Jungle by Hanging Gardens in Ubud, Bali.">
@endpush

<x-layouts.app>
    <section class="bg-white px-6 pb-14 pt-28 md:pb-20 md:pt-32" aria-labelledby="guest-review-list-title">
        <div class="mx-auto max-w-6xl">
            <h1 id="guest-review-list-title" class="text-center text-xl font-medium uppercase leading-snug text-slate-700 sm:text-2xl">
                What Our Guests Say
            </h1>
            <p class="mx-auto mt-3 max-w-3xl text-center text-xs leading-relaxed text-slate-600 sm:text-sm">
                Discover what our guests say about their time surrounded by nature, warm Balinese hospitality, and the peaceful atmosphere of Nandini Jungle by Hanging Gardens.
            </p>

            @if ($reviews->isNotEmpty())
            <div class="mt-10 grid gap-6 md:grid-cols-2">
                @foreach ($reviews as $review)
                @php
                $reviewExcerpt = trim((string) ($review->excerpt ?? ''));

                if ($reviewExcerpt === '') {
                    $reviewExcerpt = html_entity_decode(
                        strip_tags((string) $review->review_text),
                        ENT_QUOTES | ENT_HTML5,
                        'UTF-8'
                    );
                    $reviewExcerpt = trim((string) preg_replace('/\s+/', ' ', $reviewExcerpt));
                }
                @endphp
                <div class="h-full" x-data="{ reviewOpen: false }">
                    <article class="flex h-full flex-col border border-slate-200 bg-white p-6 sm:p-8">
                        <div class="mb-5 flex items-center gap-1 text-[#A88444]" aria-label="{{ $review->rating }} out of 5 stars">
                            @for ($star = 1; $star <= 5; $star++)
                            <svg class="h-5 w-5 {{ $star <= $review->rating ? 'fill-current' : 'fill-slate-200 text-slate-200' }}" viewBox="0 0 20 20" aria-hidden="true">
                                <path d="m10 1.7 2.47 5.01 5.53.8-4 3.9.95 5.51L10 14.32l-4.95 2.6.95-5.51-4-3.9 5.53-.8L10 1.7Z" />
                            </svg>
                            @endfor
                        </div>

                        <p class="grow text-xs leading-relaxed text-slate-600 sm:text-sm">
                            {{ $reviewExcerpt }}
                        </p>

                        <div class="mt-7 flex flex-wrap items-center justify-between gap-4 border-t border-slate-200 pt-5">
                            <div>
                                <div class="text-base font-semibold text-slate-700 sm:text-lg">
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

                            <button type="button" class="inline-flex min-w-[120px] items-center justify-center border border-slate-700 px-4 py-2.5 text-xs font-medium uppercase tracking-[0.08em] text-slate-700 transition hover:border-[#B8945B] hover:bg-[#B8945B] hover:text-white sm:text-sm" aria-haspopup="dialog" aria-controls="guest-review-modal-{{ $review->id }}" @click="reviewOpen = true">
                                Read More
                            </button>
                        </div>
                    </article>

                    <div id="guest-review-modal-{{ $review->id }}" x-cloak x-show="reviewOpen" x-transition.opacity class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true" aria-labelledby="guest-review-modal-title-{{ $review->id }}" @keydown.escape.window="reviewOpen = false">
                        <div class="absolute inset-0 bg-black/70" aria-hidden="true" @click="reviewOpen = false"></div>

                        <div class="relative z-10 max-h-[85vh] w-full max-w-2xl overflow-y-auto bg-white p-6 shadow-2xl sm:p-8">
                            <button type="button" class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center text-slate-500 transition hover:text-[#A88444]" aria-label="Close full review" @click="reviewOpen = false">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>

                            <div class="pr-10">
                                <div class="mb-5 flex items-center gap-1 text-[#A88444]" aria-label="{{ $review->rating }} out of 5 stars">
                                    @for ($star = 1; $star <= 5; $star++)
                                    <svg class="h-5 w-5 {{ $star <= $review->rating ? 'fill-current' : 'fill-slate-200 text-slate-200' }}" viewBox="0 0 20 20" aria-hidden="true">
                                        <path d="m10 1.7 2.47 5.01 5.53.8-4 3.9.95 5.51L10 14.32l-4.95 2.6.95-5.51-4-3.9 5.53-.8L10 1.7Z" />
                                    </svg>
                                    @endfor
                                </div>

                                <h2 id="guest-review-modal-title-{{ $review->id }}" class="font-sans text-base font-semibold text-slate-700 sm:text-lg">
                                    {{ $review->reviewer_name }}
                                </h2>
                            </div>

                            <div class="prose prose-slate mt-6 max-w-none text-xs leading-relaxed text-slate-600 prose-p:mb-3 prose-p:mt-0 prose-p:last:mb-0 prose-strong:font-semibold prose-ul:my-3 prose-ol:my-3 prose-li:my-1 sm:text-sm">
                                {!! $review->review_text !!}
                            </div>

                            @if ($review->reviewed_at || $review->source)
                            <div class="mt-7 border-t border-slate-200 pt-5 text-xs text-slate-500 sm:text-sm">
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
                    </div>
                </div>
                @endforeach
            </div>

            @if ($reviews->hasPages())
            <div class="mt-12">
                {{ $reviews->onEachSide(1)->links() }}
            </div>
            @endif
            @else
            <p class="text-center text-xs leading-relaxed text-slate-600 sm:text-sm">
                Guest reviews will be available soon.
            </p>
            @endif
        </div>
    </section>
</x-layouts.app>

@props([
'page' => null,
'pageId' => null,
'videoId' => null,
'reverse' => false,
'boxed' => true,
])

@php
use App\Models\Page;

if (! $page && $pageId) {
$page = Page::query()
->where('id', $pageId)
->where('is_active', true)
->first();
}

$extractYoutubeId = function (?string $value): ?string {
$value = trim((string) $value);

if ($value === '') {
return null;
}

// Already a YouTube ID
if (preg_match('/^[a-zA-Z0-9_-]{6,20}$/', $value)) {
return $value;
}

// Extract from YouTube URL
if (preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|shorts/))([a-zA-Z0-9_-]{6,20})~', $value, $matches)) {
return $matches[1];
}

return null;
};

$resolvedVideoId = $extractYoutubeId($videoId) ?: $extractYoutubeId($page?->video_id ?? '');

$embedUrl = $resolvedVideoId
? "https://www.youtube-nocookie.com/embed/{$resolvedVideoId}?rel=0&modestbranding=1&playsinline=1"
: null;

$wrapper = $boxed
? 'w-full max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8'
: 'w-full';

$gridOrderVideo = $reverse ? 'lg:order-2' : 'lg:order-1';
$gridOrderText = $reverse ? 'lg:order-1' : 'lg:order-2';
@endphp

<section class="py-14 md:py-28 w-full {{ $reverse ? '' : 'bg-[#F7F7F7]' }}">
    <div class="{{ $wrapper }}">
        <div class="grid grid-cols-1 lg:grid-cols-12 items-stretch gap-8 lg:gap-10">

            {{-- Video --}}
            <div class="lg:col-span-7 {{ $gridOrderVideo }}">
                <div class="relative aspect-square md:aspect-3/2 overflow-hidden bg-slate-100">
                    @if ($embedUrl)
                    <iframe class="absolute inset-0 h-full w-full" src="{{ $embedUrl }}" title="{{ $page?->title ?? 'Video' }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen>
                    </iframe>
                    @else
                    <div class="h-full w-full flex items-center justify-center text-sm text-slate-500">
                        Invalid / missing video ID
                    </div>
                    @endif
                </div>
            </div>

            {{-- Text --}}
            <div class="lg:col-span-5 {{ $gridOrderText }}">
                <div class="h-full flex flex-col justify-center px-4 sm:px-8 md:px-10 lg:px-12 md:py-14">
                    <div class="text-center">
                        @if ($page?->title)
                        <h1 class="text-2xl sm:text-3xl md:text-3xl leading-snug tracking-[0.15em] md:tracking-[0.25em] uppercase text-slate-800 mb-6 md:mb-8 font-medium">
                            {{ $page->title }}
                        </h1>
                        @endif

                        <div class="mt-4 h-px w-20 bg-slate-400/70 mx-auto"></div>

                        @if ($page?->excerpt)
                        <p class="mt-6 max-w-md text-[15px] leading-7 text-slate-700 mx-auto">
                            {{ $page->excerpt }}
                        </p>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
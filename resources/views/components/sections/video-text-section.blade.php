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
$thumbnailUrl = $resolvedVideoId
? "https://i.ytimg.com/vi/{$resolvedVideoId}/hqdefault.jpg"
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
                <div class="relative aspect-[4/3] md:aspect-3/2 overflow-hidden bg-slate-100">
                    @if ($embedUrl)
                    <div class="absolute inset-0 cursor-pointer bg-black" role="button" tabindex="0" aria-label="Play {{ $page?->title ?? 'video' }}" data-youtube-embed data-src="{{ $embedUrl }}" data-title="{{ $page?->title ?? 'Video' }}">
                        <img src="{{ $thumbnailUrl }}" alt="{{ $page?->title ?? 'Video preview' }}" class="h-full w-full object-cover" width="480" height="360" loading="lazy" decoding="async">
                        <div class="absolute inset-0 bg-black/20"></div>
                        <span class="absolute left-1/2 top-1/2 flex h-14 w-14 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full bg-black/55 text-white ring-1 ring-white/70" aria-hidden="true">
                            <span class="ml-1 h-0 w-0 border-y-[10px] border-l-[16px] border-y-transparent border-l-white"></span>
                        </span>
                    </div>
                    @else
                    <div class="h-full w-full flex items-center justify-center text-xs text-slate-500 sm:text-sm">
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
                        <h1 class="text-xl leading-snug uppercase text-slate-700 font-medium mb-3 sm:text-2xl">
                            {{ $page->title }}
                        </h1>
                        @endif

                        @if ($page?->excerpt)
                        <p class="text-xs leading-relaxed text-gray-600 max-w-2xl sm:max-w-3xl md:max-w-5xl mx-auto sm:text-sm">
                            {{ $page->excerpt }}
                        </p>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

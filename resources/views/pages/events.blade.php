@push('meta')
@php
$metaTitle = $page->meta_title ?: $page->title;
$metaDescription = $page->meta_description
?: \Illuminate\Support\Str::limit(trim(strip_tags((string) $page->description)), 160);
$metaImage = $page->hero_image ?? $page->hero_mobile_image ?? null;
@endphp

<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
<meta name="author" content="Nandini Jungle by Hanging Gardens">
<link rel="canonical" href="{{ route('events.index') }}">
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ route('events.index') }}">
<meta property="og:site_name" content="Nandini Jungle by Hanging Gardens">

@if ($metaImage)
<meta property="og:image" content="{{ Storage::disk('public')->url($metaImage) }}">
<meta name="twitter:image" content="{{ Storage::disk('public')->url($metaImage) }}">
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
@endpush

<x-layouts.app>
    <x-heroes.image-hero :page="$page" />

    <section class="bg-white px-6 py-14 text-center md:py-20" aria-labelledby="events-page-heading">
        <div class="mx-auto max-w-5xl">
            <h1 id="events-page-heading" class="mb-3 text-xl font-medium uppercase leading-snug text-slate-700 sm:text-2xl">
                {{ $page->title }}
            </h1>

            @if (filled($page->subtitle))
            <p class="mx-auto mb-5 max-w-3xl text-xs font-medium uppercase tracking-[0.08em] text-[#A88444] sm:text-sm">
                {{ $page->subtitle }}
            </p>
            @endif

            @if (filled($page->description))
            <div class="mx-auto max-w-4xl text-xs leading-relaxed text-gray-600 sm:text-sm [&_a]:text-[#8B6B35] [&_a]:underline [&_a]:underline-offset-4 [&_h2]:mb-3 [&_h2]:mt-10 [&_h2]:text-lg [&_h2]:font-medium [&_h2]:uppercase [&_h2]:leading-snug [&_h2]:text-slate-700 sm:[&_h2]:text-xl [&_li]:mb-2 [&_ol]:mx-auto [&_ol]:max-w-3xl [&_ol]:list-decimal [&_ol]:pl-5 [&_p]:mb-5 [&_p:last-child]:mb-0 [&_ul]:mx-auto [&_ul]:max-w-3xl [&_ul]:list-disc [&_ul]:pl-5">
                {!! $page->description !!}
            </div>
            @endif
        </div>
    </section>

    <section class="bg-slate-50 px-3 py-14 sm:px-6 md:py-20" aria-labelledby="todays-event-heading">
        <div class="mx-auto max-w-7xl">
            @if ($todayEvent)
            <article class="grid items-stretch gap-8 lg:grid-cols-[minmax(0,600px)_minmax(0,1fr)] lg:gap-10" data-today-event-layout="split">
                <div class="group mx-auto aspect-[12/17] w-full max-w-[600px] overflow-hidden bg-slate-200 lg:mx-0">
                    <img src="{{ Storage::disk('public')->url($todayEvent->image) }}" alt="{{ $todayEvent->alt_text }}" class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" width="600" height="850" loading="lazy" decoding="async">
                </div>

                <div class="flex h-full flex-col justify-center px-5 py-2 text-center sm:px-8 lg:px-10 lg:py-12">
                    <p class="mb-3 text-xs font-medium uppercase tracking-[0.08em] text-[#A88444] sm:text-sm">Happening Today</p>

                    @if (filled($todayEvent->today_schedule_label))
                    <p class="mt-2 text-xs font-medium uppercase tracking-[0.08em] text-slate-500 sm:text-sm">
                        {{ $todayEvent->today_schedule_label }}
                    </p>
                    @endif

                    <h2 id="todays-event-heading" class="mt-4 text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">
                        {{ $todayEvent->title }}
                    </h2>

                    @if (filled($todayEvent->subtitle))
                    <p class="mx-auto mt-3 max-w-xl text-xs font-medium leading-relaxed text-slate-700 sm:text-sm">{{ $todayEvent->subtitle }}</p>
                    @endif

                    @if (filled($todayEvent->excerpt))
                    <p class="mx-auto mt-4 max-w-xl text-xs leading-relaxed text-gray-600 sm:text-sm">{{ $todayEvent->summary }}</p>
                    @endif

                    @if (filled($todayEvent->description))
                    <div class="mx-auto mt-4 max-w-xl text-xs leading-relaxed text-gray-600 sm:text-sm [&_a]:text-[#8B6B35] [&_a]:underline [&_a]:underline-offset-4 [&_h2]:mb-3 [&_h2]:mt-7 [&_h2]:text-lg [&_h2]:font-medium [&_h2]:uppercase [&_h2]:text-slate-700 sm:[&_h2]:text-xl [&_li]:mb-2 [&_ol]:list-inside [&_ol]:list-decimal [&_p]:mb-4 [&_p:last-child]:mb-0 [&_ul]:list-inside [&_ul]:list-disc">
                        {!! $todayEvent->description !!}
                    </div>
                    @endif

                    <a href="https://wa.me/6281236871170" target="_blank" rel="noopener noreferrer" class="mx-auto mt-8 inline-flex w-fit items-center justify-center border border-[#A88444] bg-[#A88444] px-5 py-3 text-xs font-medium uppercase tracking-[0.08em] text-white transition duration-300 hover:border-[#B8945B] hover:bg-[#B8945B] hover:text-white sm:text-sm">
                        Reserve a table
                    </a>
                </div>
            </article>
            @else
            <div class="mb-8 text-center md:mb-10">
                <p class="mb-3 text-xs font-medium uppercase tracking-[0.08em] text-[#A88444] sm:text-sm">Happening Today</p>
                <h2 id="todays-event-heading" class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">Today's Event</h2>
            </div>
            <div class="border border-slate-200 bg-white px-6 py-10 text-center">
                <p class="text-xs leading-relaxed text-gray-600 sm:text-sm">There is no event scheduled for today. Please explore the upcoming and regular events below.</p>
            </div>
            @endif
        </div>
    </section>

    @if ($dishOfTheMonth)
    <section class="bg-white px-3 py-14 sm:px-6 md:py-20" aria-labelledby="dish-of-the-month-heading">
        <div class="mx-auto max-w-7xl">
            <div class="mb-8 text-center md:mb-10">
                <p class="mb-3 text-xs font-medium uppercase tracking-[0.08em] text-[#A88444] sm:text-sm">Chef’s Monthly Selection</p>
                <h2 id="dish-of-the-month-heading" class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">Dish of the Month</h2>
            </div>

            <article class="text-center" data-dish-of-the-month-layout="full-width">
                <div class="group aspect-video w-full overflow-hidden">
                    <img src="{{ Storage::disk('public')->url($dishOfTheMonth->image) }}" alt="{{ $dishOfTheMonth->alt_text }}" class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" width="2048" height="1152" loading="lazy" decoding="async">
                </div>

                <a href="https://wa.me/6281236871170" target="_blank" rel="noopener noreferrer" class="mx-auto mt-8 inline-flex w-fit items-center justify-center border border-[#A88444] bg-[#A88444] px-5 py-3 text-xs font-medium uppercase tracking-[0.08em] text-white transition duration-300 hover:border-[#B8945B] hover:bg-[#B8945B] hover:text-white sm:text-sm">
                    Reserve a table
                </a>
            </article>
        </div>
    </section>
    @endif

    <section class="bg-slate-50 px-3 py-14 sm:px-6 md:py-20" aria-labelledby="upcoming-events-heading">
        <div class="mx-auto max-w-7xl">
            <div class="mb-8 text-center md:mb-10">
                <h2 id="upcoming-events-heading" class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">Upcoming Events</h2>
                <p class="mx-auto mt-4 max-w-4xl text-xs leading-relaxed text-gray-600 sm:text-sm">
                    Our Upcoming Dining Events at Wild Ginger Restaurant offer exclusive dining experiences available on selected dates throughout the year. Open to both in-house and non-staying guests, each event combines exceptional cuisine, live cultural entertainment, and the tranquil jungle ambiance of Nandini Jungle by Hanging Gardens, creating a truly memorable evening in Ubud.
                </p>
            </div>

            @if ($upcomingEvents->isNotEmpty())
            <x-events.slider :events="$upcomingEvents" schedule-mode="upcoming" label="upcoming events" />
            @else
            <div class="border border-slate-200 bg-white px-6 py-10 text-center">
                <p class="text-xs leading-relaxed text-gray-600 sm:text-sm">New upcoming events will be announced here.</p>
            </div>
            @endif
        </div>
    </section>

    <section class="bg-white px-3 py-14 sm:px-6 md:py-20" aria-labelledby="regular-events-heading">
        <div class="mx-auto max-w-7xl">
            <div class="mb-8 text-center md:mb-10">
                <h2 id="regular-events-heading" class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">Regular Events</h2>
                <p class="mx-auto mt-4 max-w-4xl text-xs leading-relaxed text-gray-600 sm:text-sm">
                    At Wild Ginger Restaurant, every dining experience is enriched with contemporary Balinese culture through regular evening events featuring traditional dance performances and live music. Open to both in-house and non-staying guests, these cultural experiences perfectly complement our locally inspired cuisine in the tranquil jungle setting of Ubud.
                </p>
            </div>

            @if ($regularEvents->isNotEmpty())
            <x-events.slider :events="$regularEvents" label="regular events" />
            @else
            <div class="border border-slate-200 bg-slate-50 px-6 py-10 text-center">
                <p class="text-xs leading-relaxed text-gray-600 sm:text-sm">Regular event schedules will appear here when published.</p>
            </div>
            @endif
        </div>
    </section>
</x-layouts.app>

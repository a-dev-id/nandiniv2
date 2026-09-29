@props(['settings' => null])

<section class="bg-white px-6 py-14 text-center md:px-12 md:py-20" aria-labelledby="festive-introduction-title" data-gtm-section="introduction">
    <div class="mx-auto max-w-3xl">
        @if ($settings?->introduction_eyebrow)
            <p class="mb-4 text-[10px] font-medium uppercase tracking-[.18em] text-[#A88444] sm:text-xs">{{ $settings->introduction_eyebrow }}</p>
        @endif
        @if ($settings?->introduction_heading)
            <h2 id="festive-introduction-title" class="text-xl font-medium uppercase leading-snug text-slate-700 [--heading-letter-spacing:.15em] sm:text-2xl">{!! nl2br(e($settings->introduction_heading)) !!}</h2>
        @endif
        @if ($settings?->introduction_description)
            <p class="mx-auto mt-3 max-w-3xl text-xs leading-relaxed text-gray-600 sm:text-sm">{!! nl2br(e($settings->introduction_description)) !!}</p>
        @endif
    </div>
</section>

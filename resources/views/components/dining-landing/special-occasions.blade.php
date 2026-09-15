@props(['settings' => null])

@php
    $uploadedImage = $settings?->private_dining_image;
    $image = $uploadedImage && \Illuminate\Support\Facades\Storage::disk('public')->exists($uploadedImage)
        ? asset('storage/'.$uploadedImage)
        : (str_starts_with((string) $uploadedImage, 'http') || str_starts_with((string) $uploadedImage, '/') ? asset($uploadedImage) : null);
    $occasions = $settings?->private_dining_tags ?? [];
@endphp

<section class="bg-[#f3f4f5] px-6 py-14 font-sans md:py-20" aria-labelledby="dining-occasions-title">
    <div class="mx-auto grid max-w-7xl items-center gap-8 lg:grid-cols-[minmax(0,53fr)_minmax(0,47fr)] lg:gap-10">
        <div class="aspect-[4/3] min-w-0 overflow-hidden bg-white">
            @if ($image)
                <img src="{{ $image }}" alt="{{ $settings?->private_dining_image_alt }}" class="h-full w-full object-cover object-center" width="1200" height="900" loading="lazy" decoding="async">
            @endif
        </div>

        <div class="min-w-0 max-w-[520px]">
            <p class="mb-3 text-[10px] font-medium tracking-[.18em] text-[#A88444] uppercase sm:text-xs">{{ $settings?->private_dining_eyebrow }}</p>
            <h2 id="dining-occasions-title" class="mb-3 text-lg leading-snug font-medium text-slate-700 uppercase sm:text-xl">{!! nl2br(e($settings?->private_dining_heading)) !!}</h2>
            <p class="mb-6 max-w-2xl text-xs leading-relaxed text-slate-600 sm:text-sm">{!! nl2br(e($settings?->private_dining_description)) !!}</p>
            @if (filled($settings?->private_dining_cta_label) && filled($settings?->private_dining_cta_url))
                <x-buttons.link-button
                    :href="$settings->private_dining_cta_url"
                    variant="solid"
                    class="w-full sm:w-auto"
                    data-inquiry-button
                    data-inquiry-title="Private Dining — Dining / Special Occasions"
                    :data-inquiry-image="$image"
                >{{ $settings->private_dining_cta_label }}</x-buttons.link-button>
            @endif

            <ul class="mt-5 grid grid-cols-2 gap-x-5 gap-y-1 sm:flex sm:flex-wrap sm:gap-x-0 sm:gap-y-0" aria-label="{{ $settings?->private_dining_eyebrow }}">
                @foreach ($occasions as $occasion)
                    <li class="flex min-w-0 items-center before:h-3 before:w-px before:shrink-0 before:bg-[#8f6b34]/40 before:content-[''] max-sm:odd:before:hidden max-sm:even:before:mr-3 sm:first:before:hidden">
                        @if (filled($occasion['url'] ?? null))
                            <a href="{{ $occasion['url'] }}" class="inline-flex min-h-11 min-w-0 items-center px-0 text-[11px] leading-[1.5] font-semibold tracking-[.04em] text-[#8f6b34] uppercase transition-colors hover:text-[#20271f] hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#8f6b34] sm:px-3 {{ $loop->first ? 'sm:pl-0' : '' }}">{{ $occasion['label'] ?? '' }}</a>
                        @else
                            <span class="inline-flex min-h-11 min-w-0 items-center px-0 text-[11px] leading-[1.5] font-semibold tracking-[.04em] text-[#8f6b34] uppercase sm:px-3 {{ $loop->first ? 'sm:pl-0' : '' }}">{{ $occasion['label'] ?? '' }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>

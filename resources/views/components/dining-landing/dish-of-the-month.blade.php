@props(['settings' => null])

@php
    $dish = collect($settings?->signature_dishes ?? [])->first();
    $image = $dish['image'] ?? null;
    $imageUrl = null;

    if (filled($image)) {
        $imageUrl = str_starts_with($image, 'http://') || str_starts_with($image, 'https://') || str_starts_with($image, '/')
            ? asset($image)
            : (\Illuminate\Support\Facades\Storage::disk('public')->exists($image) ? asset('storage/'.$image) : null);
    }
@endphp

@if ($dish)
<section class="relative min-h-[500px] w-full overflow-hidden bg-slate-900 font-sans sm:min-h-[600px] lg:min-h-[700px]" aria-labelledby="dish-of-the-month-title">
    @if ($imageUrl)
        <img src="{{ $imageUrl }}" alt="{{ $dish['alt'] ?? '' }}" class="absolute inset-0 h-full w-full object-cover" width="1920" height="1080" loading="lazy" decoding="async">
    @endif
    <div class="relative flex min-h-[500px] items-center px-6 py-14 sm:min-h-[600px] md:px-10 md:py-20 lg:min-h-[700px] 2xl:px-14">
        <div class="max-w-md text-white">
            @if (filled($dish['eyebrow'] ?? null))
                <p class="mb-3 text-[10px] font-medium uppercase tracking-[.18em] text-[#C7A263] sm:text-xs">{{ $dish['eyebrow'] }}</p>
            @endif
            @if (filled($dish['heading'] ?? null))
                <h2 id="dish-of-the-month-title" class="font-serif text-3xl leading-tight text-white sm:text-4xl">{{ $dish['heading'] }}</h2>
            @endif
            @if (filled($dish['introduction'] ?? null))
                <p class="mt-5 text-sm leading-relaxed text-white/90">{{ $dish['introduction'] }}</p>
            @endif
            @if (filled($settings?->signature_menu_label) && filled($settings?->signature_menu_url))
                <div class="mt-7">
                    <x-buttons.link-button :href="$settings->signature_menu_url" variant="solid">{{ $settings->signature_menu_label }}</x-buttons.link-button>
                </div>
            @endif
        </div>
    </div>
</section>
@endif

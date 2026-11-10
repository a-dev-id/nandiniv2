@props(['photo', 'dimensions', 'featured' => false, 'hero' => false])
@php
    $id = substr(basename($photo['image']), 3, 5);
    $size = $dimensions[$id];
    $base = 'images/go-tyme-images/optimized/'.$id;
@endphp
<a href="{{ asset(ltrim($photo['image'], '/')) }}" data-retreat-photo data-alt="{{ $photo['alt'] }}"
    class="retreat-photo block bg-[#F8F4EC] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#A88444]"
    aria-label="Enlarge photograph: {{ $photo['alt'] }}">
    <img src="{{ asset($base.($hero || $featured ? '-1600.webp' : '-960.webp')) }}"
        srcset="{{ asset($base.'-480.webp') }} 480w, {{ asset($base.'-960.webp') }} 960w{{ $hero || $featured ? ', '.asset($base.'-1600.webp').' 1600w' : '' }}"
        sizes="{{ $hero || $featured ? '(min-width: 1280px) 1232px, calc(100vw - 48px)' : '(min-width: 1280px) 296px, (min-width: 768px) 30vw, calc(50vw - 32px)' }}"
        width="{{ $size['width'] }}" height="{{ $size['height'] }}" alt="{{ $photo['alt'] }}"
        class="block h-auto w-full" loading="{{ $hero ? 'eager' : 'lazy' }}" decoding="async"
        @if ($hero) fetchpriority="high" @endif>
</a>

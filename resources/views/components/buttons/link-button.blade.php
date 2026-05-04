@props([
'href' => '#',
'variant' => 'outline',
])

@php
$variantClass = match ($variant) {
'solid' => 'bg-[#B8945B] text-white hover:bg-[#a37e45]',
default => 'border border-slate-700 text-slate-800 hover:bg-[#B8945B] hover:border-[#B8945B] hover:text-white',
};

$cleanHref = html_entity_decode($href, ENT_QUOTES | ENT_HTML5, 'UTF-8');
@endphp

<a href="{{ $cleanHref }}" {{ $attributes->merge([
    'class' => "inline-flex items-center justify-center px-10 py-3 text-[12px] uppercase tracking-[0.25em] transition duration-300 font-medium {$variantClass}",
    ]) }}
    >
    {{ $slot }}
</a>
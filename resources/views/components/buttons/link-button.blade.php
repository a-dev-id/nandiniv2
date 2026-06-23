@props([
'href' => '#',
'variant' => 'outline',
])

@php
$variantClass = match ($variant) {
'solid' => 'bg-[#A67C3D] border border-[#A67C3D] text-white hover:bg-[#B8945B] hover:border-[#B8945B]',
'white-outline' => 'border border-white text-white hover:bg-white hover:border-white hover:text-[#2f2f2f]',
default => 'border border-slate-700 text-slate-700 hover:bg-[#B8945B] hover:border-[#B8945B] hover:text-white',
};

$cleanHref = \App\Support\MemberBookingVoucher::appendToUrl($href);
@endphp

<a href="{{ $cleanHref }}" {{ $attributes->merge([
    'class' => "inline-flex items-center justify-center px-6 py-2.5 text-sm uppercase tracking-[0.08em] transition duration-300 font-medium {$variantClass}",
    ]) }}
    >
    {{ $slot }}
</a>

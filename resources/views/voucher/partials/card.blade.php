@php
    $money = app(\App\Services\Voucher\MoneyFormatter::class);
    $image = $voucher->preview_image;
    $priceSuffix = $money->priceTypeSuffix($voucher->price_type);
    $unitLabel = $money->unitLabel($voucher->unit_type);
@endphp

<article class="group flex h-full flex-col bg-white">
    <a href="{{ route('voucher.show', $voucher) }}" class="block overflow-hidden bg-slate-100">
        @if ($image)
            <img src="{{ asset('storage/' . $image) }}" alt="{{ $voucher->image_alt ?: $voucher->title }}" class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-[1.03]" loading="lazy">
        @else
            <div class="flex aspect-[4/3] items-center justify-center bg-[#F7F7F7] px-6 text-center text-xs uppercase tracking-[0.08em] text-slate-500">
                Nandini Voucher
            </div>
        @endif
    </a>

    <div class="flex flex-1 flex-col border border-slate-200 border-t-0 p-5">
        <p class="text-[11px] uppercase tracking-[0.08em] text-[#A88444]">{{ $voucher->category?->name ?: 'Voucher' }}</p>
        <h2 class="mt-2 text-base uppercase leading-snug text-slate-700 sm:text-lg">{{ $voucher->title }}</h2>
        <p class="mt-3 line-clamp-3 text-sm leading-6 text-slate-600">{{ $voucher->excerpt }}</p>
        <div class="mt-auto pt-5">
            <p class="text-sm font-semibold uppercase tracking-[0.08em] text-slate-700">{{ $money->format($voucher->selling_price, $voucher->currency) }}{{ $priceSuffix }}</p>
            @if ($unitLabel)
                <p class="mt-1 text-xs text-slate-600">{{ $unitLabel }}</p>
            @endif
            <a href="{{ route('voucher.show', $voucher) }}" class="mt-4 inline-flex w-full items-center justify-center border border-[#A88444] bg-[#A88444] px-4 py-2.5 text-xs font-medium uppercase tracking-[0.08em] text-white transition hover:border-[#B8945B] hover:bg-[#B8945B] sm:text-sm">
                View Voucher
            </a>
        </div>
    </div>
</article>

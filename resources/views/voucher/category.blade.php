@php
    $categoryDescription = trim(strip_tags((string) $category->description));
@endphp

@push('meta')
<title>{{ $category->name }} Vouchers | Nandini Jungle</title>
<meta name="description" content="{{ $categoryDescription ?: 'Browse Nandini Jungle vouchers.' }}">
<link rel="canonical" href="{{ route('voucher.category.show', $category) }}">
@endpush

<x-layouts.app>
    <section class="bg-[#F7F7F7] px-6 pb-14 pt-36 md:pb-20">
        <div class="mx-auto max-w-6xl">
            <nav class="text-xs uppercase tracking-[0.08em] text-slate-500">
                <a href="{{ route('voucher.index') }}" class="hover:text-[#A88444]">Vouchers</a>
                <span class="mx-2">/</span>
                <span>{{ $category->name }}</span>
            </nav>
            <h1 class="mt-5 text-2xl uppercase text-slate-700 sm:text-4xl">{{ $category->name }}</h1>
            <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-600">{{ $categoryDescription ?: 'Explore available Nandini experiences.' }}</p>
        </div>
    </section>

    <section class="bg-white px-6 py-14 md:py-20">
        <div class="mx-auto max-w-6xl">
            @if ($vouchers->isEmpty())
                <div class="border border-slate-200 bg-[#F7F7F7] p-8 text-center text-sm text-slate-600">There are no active vouchers in this category yet.</div>
            @else
                <div class="grid gap-7 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($vouchers as $voucher)
                        @include('voucher.partials.card', ['voucher' => $voucher])
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</x-layouts.app>

@push('meta')
<title>{{ $title }} | Nandini Partner Circle</title>
<meta name="robots" content="noindex,nofollow">
@endpush

<x-layouts.affiliate>
    <section class="flex min-h-[65vh] items-center px-5 py-16 sm:px-8 lg:px-10">
        <div class="mx-auto w-full max-w-2xl text-center">
            <p class="text-xs font-medium uppercase tracking-[0.12em] text-[#A88444] sm:text-sm">Nandini Partner Circle</p>
            <h1 class="mt-4 text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">{{ $title }}</h1>
            <p class="mx-auto mt-5 max-w-xl text-xs leading-relaxed text-gray-600 sm:text-sm">{{ $message }}</p>
            <a href="{{ auth('affiliate')->check() ? route('affiliate.dashboard') : route('affiliate.login') }}" class="mt-8 inline-flex items-center justify-center border border-[#A88444] bg-[#A88444] px-6 py-3 text-xs font-medium uppercase tracking-[0.08em] text-white transition hover:bg-[#B8945B] sm:text-sm">
                {{ auth('affiliate')->check() ? 'Return to Dashboard' : 'Affiliate Login' }}
            </a>
        </div>
    </section>
</x-layouts.affiliate>

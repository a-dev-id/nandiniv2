<x-layouts.affiliate title="Profile | Nandini Partner Circle">
    <section class="bg-slate-50 px-5 py-12 sm:px-8 sm:py-16 lg:px-12 lg:py-20">
        <div class="mx-auto max-w-4xl">
            <a href="{{ route('affiliate.dashboard') }}" class="text-xs font-medium uppercase tracking-[0.08em] text-[#8B6B35] underline-offset-4 hover:underline sm:text-sm">&larr; Dashboard</a>

            <h1 class="mt-8 text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">Profile</h1>

            <dl class="mt-8 divide-y divide-slate-200 border-y border-slate-200 bg-white px-5 sm:px-8">
                @foreach ([
                    'Name' => $affiliate->name,
                    'Email' => $affiliate->email,
                    'Phone / WhatsApp' => $affiliate->phone_whatsapp,
                    'Instagram' => $affiliate->instagram,
                    'Facebook' => $affiliate->facebook,
                    'TikTok' => $affiliate->tiktok,
                    'X' => $affiliate->x,
                    'Threads' => $affiliate->threads,
                    'Status' => $affiliate->status->label(),
                ] as $label => $value)
                    @if (filled($value))
                        <div class="grid gap-2 py-5 sm:grid-cols-[180px_1fr] sm:gap-6">
                            <dt class="text-xs font-medium uppercase tracking-[0.08em] text-slate-500 sm:text-sm">{{ $label }}</dt>
                            <dd class="break-words text-xs leading-relaxed text-gray-600 sm:text-sm">{{ $value }}</dd>
                        </div>
                    @endif
                @endforeach
            </dl>
        </div>
    </section>
</x-layouts.affiliate>

@push('meta')
<title>Marketing Materials | Nandini Partner Circle</title>
<meta name="robots" content="noindex,nofollow">
@endpush

<x-layouts.affiliate>
    <section class="min-h-[70vh] px-5 py-12 sm:px-8 sm:py-16 lg:px-10">
        <div class="mx-auto w-full max-w-6xl">
            <p class="text-xs font-medium uppercase tracking-[0.12em] text-[#A88444] sm:text-sm">Partner resources</p>
            <h1 class="mt-3 text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">Marketing Materials</h1>
            @if (! $affiliate->isApproved())
                <div class="mt-8 border-l-4 border-amber-400 bg-white px-5 py-6 sm:px-7">
                    <h2 class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">Materials are not available yet</h2>
                    <p class="mt-3 text-xs leading-relaxed text-gray-600 sm:text-sm">Marketing materials become available after an affiliate account is approved and active.</p>
                </div>
            @else
                @php
                    $filters = [null => 'All', 'image' => 'Images', 'video' => 'Videos', 'document' => 'Documents'];
                @endphp
                <nav class="mt-6 flex flex-wrap gap-2" aria-label="Marketing material filters">
                    @foreach ($filters as $value => $label)
                        <a href="{{ route('affiliate.marketing-materials.index', $value ? ['type' => $value] : []) }}" class="border px-4 py-2 text-xs font-medium uppercase tracking-[0.08em] transition {{ $selectedType === $value ? 'border-[#A88444] bg-[#A88444] text-white' : 'border-slate-300 bg-white text-slate-700 hover:border-[#A88444] hover:text-[#8B6B35]' }}">{{ $label }}</a>
                    @endforeach
                </nav>

                @if ($assets->isEmpty())
                    <div class="mt-8 border border-slate-200 bg-white px-5 py-7 sm:px-7">
                        <h2 class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">No materials in this view</h2>
                        <p class="mt-3 text-xs leading-relaxed text-gray-600 sm:text-sm">New approved resources will appear here when they become available.</p>
                    </div>
                @else
                    <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($assets as $asset)
                            <article class="flex h-full flex-col border border-slate-200 bg-white">
                                @if ($asset->thumbnail_path || in_array($asset->file_extension, ['jpg', 'jpeg', 'png', 'webp'], true))
                                    <img src="{{ route('affiliate.marketing-materials.preview', $asset) }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy">
                                @else
                                    <div class="flex aspect-[4/3] items-center justify-center bg-slate-100 px-5 text-center text-xs font-medium uppercase tracking-[0.1em] text-slate-500">{{ $asset->asset_type->label() }}</div>
                                @endif
                                <div class="flex flex-1 flex-col px-5 py-6">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-xs font-medium uppercase tracking-[0.1em] text-[#A88444]">{{ $asset->asset_type->label() }}</span>
                                        @if ($asset->is_featured)<span class="bg-amber-50 px-2 py-1 text-[0.68rem] font-medium uppercase tracking-[0.08em] text-amber-800">Featured</span>@endif
                                    </div>
                                    <h2 class="mt-3 text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">{{ $asset->title }}</h2>
                                    @if ($asset->description)<p class="mt-3 line-clamp-4 text-xs leading-relaxed text-gray-600 sm:text-sm">{{ $asset->description }}</p>@endif
                                    <div class="mt-auto pt-6">
                                        @if ($asset->file_path)
                                            <a href="{{ route('affiliate.marketing-materials.download', $asset) }}" class="inline-flex min-h-11 items-center justify-center border border-[#A88444] bg-[#A88444] px-5 py-2 text-xs font-medium uppercase tracking-[0.08em] text-white transition hover:bg-[#B8945B]">Download</a>
                                        @else
                                            <a href="{{ $asset->external_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-11 items-center justify-center border border-[#A88444] bg-[#A88444] px-5 py-2 text-xs font-medium uppercase tracking-[0.08em] text-white transition hover:bg-[#B8945B]">Open</a>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </section>
</x-layouts.affiliate>

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
                        <a href="{{ route('affiliate.marketing-materials.index', $value ? ['type' => $value] : []) }}" class="rounded-full border px-5 py-2 text-xs font-medium uppercase tracking-[0.08em] transition {{ $selectedType === $value ? 'border-[#A88444] bg-[#A88444] text-white' : 'border-slate-300 bg-white text-slate-700 hover:border-[#A88444] hover:bg-amber-50 hover:text-[#8B6B35]' }}">{{ $label }}</a>
                    @endforeach
                </nav>

                @if ($assets->isEmpty())
                    <div class="mt-8 border border-slate-200 bg-white px-5 py-7 sm:px-7">
                        <h2 class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">No materials in this view</h2>
                        <p class="mt-3 text-xs leading-relaxed text-gray-600 sm:text-sm">New approved resources will appear here when they become available.</p>
                    </div>
                @else
                    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5">
                        @foreach ($assets as $asset)
                            @php
                                $assetUrl = $asset->file_path
                                    ? route('affiliate.marketing-materials.download', $asset)
                                    : $asset->external_url;
                                $opensNewWindow = ! $asset->file_path;
                            @endphp
                            <article class="rounded-xl border border-slate-200 bg-slate-100 transition hover:border-slate-300 hover:shadow-md">
                                <a href="{{ $assetUrl }}" @if ($opensNewWindow) target="_blank" rel="noopener noreferrer" @endif class="flex min-w-0 items-center gap-3 px-4 py-3" aria-label="{{ $asset->file_path ? 'Download' : 'Open' }} {{ $asset->title }}">
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center text-slate-600">
                                        @if ($asset->asset_type === \App\Enums\AffiliateMarketingAssetType::Video)
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4 4.75A2.75 2.75 0 0 1 6.75 2h7.5A2.75 2.75 0 0 1 17 4.75v2.79l3.27-2.18A1.1 1.1 0 0 1 22 6.28v11.44a1.1 1.1 0 0 1-1.73.92L17 16.46v2.79A2.75 2.75 0 0 1 14.25 22h-7.5A2.75 2.75 0 0 1 4 19.25V4.75Z"/></svg>
                                        @elseif ($asset->asset_type === \App\Enums\AffiliateMarketingAssetType::Document)
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6 2h7.2L19 7.8V20a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Zm6 1.8V9h5.2L12 3.8Z"/></svg>
                                        @else
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm1 14h12l-3.8-5-3 3.6-2.1-2.4L6 17Zm9.5-7.5a1.75 1.75 0 1 0 0-3.5 1.75 1.75 0 0 0 0 3.5Z"/></svg>
                                        @endif
                                    </span>
                                    <h2 class="min-w-0 flex-1 truncate text-sm font-medium text-slate-800" title="{{ $asset->title }}">{{ $asset->title }}</h2>
                                </a>
                            </article>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </section>
</x-layouts.affiliate>

@props([
    'page',
    'spas' => null,
    'showPackages' => false,
])

@php
$hasHero = $page->hero_image || $page->hero_mobile_image;
$sections = $page->sections ?? collect();
$leadingSections = $showPackages ? $sections->take(max(0, $sections->count() - 1)) : $sections;
$trailingSections = $showPackages ? $sections->take(-1) : collect();
$publishedSpas = collect($spas ?? []);
@endphp

<div id="spa">
    @if ($hasHero)
    <div class="relative bg-slate-900 text-white">
        <x-heroes.image-hero :page="$page" />
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/65 via-black/20 to-black/20"></div>
        <div class="absolute inset-x-0 bottom-0 z-10 px-6 pb-10 text-center sm:pb-14 md:px-10 lg:pb-20">
            <span class="mx-auto mb-5 block h-px w-14 bg-[var(--spa-accent)]"></span>
            <h1 class="text-3xl uppercase leading-tight text-white sm:text-4xl lg:text-5xl">{{ $page->title }}</h1>
            @if ($page->subtitle)
            <p class="mx-auto mt-4 max-w-3xl text-sm leading-7 text-white/90 sm:text-base">{{ $page->subtitle }}</p>
            @endif
            @if ($page->excerpt)
            <p class="mx-auto mt-3 max-w-2xl text-xs leading-6 text-white/80 sm:text-sm">{{ $page->excerpt }}</p>
            @endif
        </div>
    </div>
    @else
    <section class="px-6 pb-12 pt-32 text-center sm:pb-16 lg:pt-40">
        <div class="mx-auto max-w-4xl">
            <h1 class="text-3xl uppercase leading-tight text-slate-700 sm:text-4xl lg:text-5xl">{{ $page->title }}</h1>
            @if ($page->subtitle)
            <p class="mx-auto mt-5 max-w-3xl text-sm leading-7 text-slate-600 sm:text-base">{{ $page->subtitle }}</p>
            @endif
            @if ($page->excerpt)
            <p class="mx-auto mt-4 max-w-2xl text-xs leading-6 text-gray-600 sm:text-sm">{{ $page->excerpt }}</p>
            @endif
        </div>
    </section>
    @endif
</div>

@if ($page->description)
<section class="px-6 py-14 text-center md:py-20">
    <div class="mx-auto max-w-4xl text-xs leading-relaxed text-gray-600 sm:text-sm [&_p]:mb-6 [&_p:last-child]:mb-0">{!! $page->description !!}</div>
</section>
@endif

<div id="wellness">
    @foreach ($leadingSections as $section)
        <div @if ($showPackages && $loop->last) id="packages" class="scroll-mt-24" @endif>
            <x-spa-site.section :section="$section" :page="$page" />
        </div>
    @endforeach
</div>

@if ($showPackages && $publishedSpas->isNotEmpty())
<x-spa-site.package-grid :items="$publishedSpas" />
@endif

@foreach ($trailingSections as $section)
    <x-spa-site.section :section="$section" :page="$page" />
@endforeach

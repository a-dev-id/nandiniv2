@push('meta')
<title>{{ $page->meta_title ?: $page->title }}</title>
<meta name="description" content="{{ $page->meta_description ?? '' }}">

@if (! empty($page->meta_keywords))
<meta name="keywords" content="{{ $page->meta_keywords }}">
@endif

<meta name="author" content="Nandini Jungle by Hanging Gardens">
<link rel="canonical" href="{{ url()->current() }}">

<meta property="og:type" content="website">
<meta property="og:title" content="{{ $page->meta_title ?: $page->title }}">
<meta property="og:description" content="{{ $page->meta_description ?? '' }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="Nandini Jungle by Hanging Gardens">

@if (! empty($page->hero_image))
<meta property="og:image" content="{{ asset('storage/' . $page->hero_image) }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta name="twitter:image" content="{{ asset('storage/' . $page->hero_image) }}">
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $page->meta_title ?: $page->title }}">
<meta name="twitter:description" content="{{ $page->meta_description ?? '' }}">
@endpush

@php
$user = auth()->user();

$memberName = $user?->name ?: 'Inner Circle Member';
$memberEmail = $user?->email ?: '-';

$memberInitial = strtoupper(mb_substr($memberName, 0, 1));

$memberId = str_pad((string) ($user?->id ?? 0), 8, '0', STR_PAD_LEFT);
$createdDate = $user?->created_at
? $user->created_at->format('d F Y')
: '-';

$memberPoints = 785;
$memberTier = 'Gold';
$pointsToNextTier = 416;
@endphp

<x-layouts.app>
    <x-heroes.image-hero :page="$page" />

    {{-- MEMBER DETAIL --}}
    <section class="bg-white py-14 md:py-20">
        <div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">

            {{-- SECTION TITLE --}}
            <div class="text-center">
                <h1 class="font-serif text-[24px] uppercase leading-tight tracking-[0.22em] text-slate-900 md:text-[30px]">
                    Member Detail
                </h1>

                <p class="mt-2 text-[11px] leading-5 text-slate-500">
                    Access your exclusive member privileges.
                </p>
            </div>

            {{-- CONTENT --}}
            <div class="mt-10 grid grid-cols-1 items-center gap-8 md:grid-cols-12 md:gap-10">

                {{-- PROFILE PHOTO --}}
                <div class="md:col-span-3">
                    <div class="mx-auto flex aspect-[3/4] w-44 items-center justify-center overflow-hidden bg-[#F7F7F7] shadow-md md:mx-0">
                        <span class="font-serif text-[72px] uppercase tracking-[0.08em] text-[#A67C3D]">
                            {{ $memberInitial }}
                        </span>
                    </div>
                </div>

                {{-- MEMBER INFO --}}
                <div class="text-center md:col-span-4 md:text-left">
                    <h2 class="font-serif text-[24px] uppercase tracking-[0.18em] text-slate-900 md:text-[28px]">
                        {{ $memberName }}
                    </h2>

                    <div class="mt-6 space-y-2 text-[13px] leading-6 text-slate-700">
                        <div class="grid grid-cols-[90px_1fr] gap-2">
                            <span>Member ID</span>
                            <span>: {{ $memberId }}</span>
                        </div>

                        <div class="grid grid-cols-[90px_1fr] gap-2">
                            <span>Email</span>
                            <span>: {{ $memberEmail }}</span>
                        </div>

                        <div class="grid grid-cols-[90px_1fr] gap-2">
                            <span>Create</span>
                            <span>: {{ $createdDate }}</span>
                        </div>

                        <div class="grid grid-cols-[90px_1fr] gap-2">
                            <span>Location</span>
                            <span>: -</span>
                        </div>
                    </div>
                </div>

                {{-- MEMBER CARD --}}
                <div class="md:col-span-5">
                    <div class="mx-auto max-w-sm">

                        {{-- GOLD CARD --}}
                        <div class="relative overflow-hidden rounded-2xl bg-[#DDAF58] px-7 py-6 text-white shadow-lg">
                            <div class="absolute inset-0 opacity-25">
                                <div class="absolute -left-10 -top-10 h-40 w-40 rounded-full border border-white/70"></div>
                                <div class="absolute left-10 top-8 h-48 w-48 rounded-full border border-white/50"></div>
                                <div class="absolute right-8 top-6 h-28 w-28 rounded-full border border-white/40"></div>
                            </div>

                            <div class="relative flex items-center justify-between gap-6">
                                <div>
                                    <p class="text-[20px] font-light tracking-[0.08em]">
                                        {{ number_format($memberPoints) }} Point
                                    </p>
                                </div>

                                <div class="text-right">
                                    <p class="text-[18px] font-semibold uppercase tracking-[0.22em]">
                                        {{ $memberTier }}
                                    </p>

                                    <p class="mt-1 text-[9px] uppercase tracking-[0.18em] text-white/80">
                                        Inner Circle
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- NEXT TIER --}}
                        <div class="mt-4 flex items-center gap-4">
                            <div class="flex-1 border border-slate-300 bg-white px-4 py-3 text-center">
                                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-800">
                                    Another {{ number_format($pointsToNextTier) }} points
                                </p>

                                <p class="mt-1 text-[8px] uppercase tracking-[0.12em] text-slate-500">
                                    To reach Platinum Member
                                </p>
                            </div>

                            <div class="h-12 w-20 rounded-md bg-[#38546B] shadow-md"></div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>
</x-layouts.app>
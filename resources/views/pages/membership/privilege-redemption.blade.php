@push('meta')
@php
$metaTitle = $page->meta_title ?: $page->title;

$metaDescription = $page->meta_description
?: \Illuminate\Support\Str::limit(strip_tags($page->description ?: $page->excerpt ?: ''), 160, '');

$metaImage = $page->hero_image ?: $page->hero_mobile_image ?: null;
@endphp

<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">

<meta name="author" content="Nandini Jungle by Hanging Gardens">
<link rel="canonical" href="{{ url()->current() }}">

<meta property="og:type" content="website">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="Nandini Jungle by Hanging Gardens">

@if ($metaImage)
<meta property="og:image" content="{{ asset('storage/' . $metaImage) }}">
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
@endpush

<x-layouts.app>
    <x-heroes.membership-hero :page="$page" :show-content="false" :show-overlay="false" />

    <style>
        .membership-reward-carousel-section .slick-list {
            padding-bottom: 46px !important;
            padding-top: 4px !important;
        }

        .membership-reward-carousel-section .slick-track {
            display: flex !important;
        }

        .membership-reward-carousel-section .slick-slide {
            height: auto !important;
        }

        .membership-reward-carousel-section .slick-slide>div {
            height: 100%;
        }

        .membership-reward-carousel-section .reward-card-article {
            height: 100%;
            padding-bottom: 8px;
        }

        .membership-reward-carousel-section .reward-card {
            height: 100%;
            min-height: 560px;
        }

        .membership-reward-carousel-section .reward-card-body {
            min-height: 300px;
        }

        @media (max-width: 767px) {
            .membership-reward-carousel-section .reward-card {
                min-height: auto;
            }

            .membership-reward-carousel-section .reward-card-body {
                min-height: auto;
            }
        }

    </style>

    @php
    $currentMember = auth('member')->user();

    $rewardGroups = $rewards
    ->filter(fn ($reward) => $reward->category)
    ->groupBy(fn ($reward) => $reward->category->id);
    @endphp

    @if (session('success') || session('error') || $errors->any())
    <section class="bg-white px-6 py-14 md:px-12 md:py-20 lg:px-[70px]">
        <div class="mx-auto w-full">
            @if (session('success'))
            <div class="border border-green-700 bg-green-50 px-5 py-4 text-xs text-green-900 sm:text-sm">
                {{ session('success') }}
            </div>
            @endif

            @if (session('error'))
            <div class="border border-red-700 bg-red-50 px-5 py-4 text-xs text-red-900 sm:text-sm">
                {{ session('error') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="border border-red-700 bg-red-50 px-5 py-4 text-xs text-red-900 sm:text-sm">
                {{ $errors->first() }}
            </div>
            @endif
        </div>
    </section>
    @endif

    @foreach ($rewardGroups as $categoryRewards)
    @php
    $category = $categoryRewards->first()->category;

    $categoryTitle = trim((string) ($category->name ?? 'Rewards'));

    $categoryDescription = trim((string) (
    $category->description
    ?? $category->excerpt
    ?? ''
    ));
    @endphp

    <section class="py-14 md:py-20 bg-white membership-reward-carousel-section" data-reward-carousel-section>
        <div class="mx-auto w-full px-6 md:px-12 lg:px-[70px]">
            <div class="mb-8 md:mb-10 text-center">
                <h2 class="text-lg leading-snug uppercase text-slate-700 font-medium mb-3 sm:text-xl">
                    {{ $categoryTitle }}
                </h2>

                @if ($categoryDescription)
                <p class="mt-2 text-xs leading-relaxed text-gray-600 max-w-2xl sm:max-w-3xl md:max-w-5xl mx-auto sm:text-sm">
                    {{ $categoryDescription }}
                </p>
                @endif
            </div>
        </div>

        <div class="mx-auto w-full px-6 md:px-12 lg:px-[70px] relative">
            <div class="reward-carousel-items">
                @foreach ($categoryRewards as $reward)
                @php
                $image = $reward->card_image
                ?? $reward->hero_image
                ?? $reward->image
                ?? null;

                $alt = $reward->card_image_alt
                ?? $reward->hero_image_alt
                ?? $reward->image_alt
                ?? $reward->title;

                $points = $reward->points_required
                ?? $reward->points
                ?? $reward->point_cost
                ?? 0;

                $points = (int) $points;

                $pointsLabel = trim((string) ($reward->points_label ?? ''));

                $rewardDescription = trim((string) (
                $reward->description
                ?? $reward->excerpt
                ?? ''
                ));

                $rewardDescriptionHasHtml = $rewardDescription
                && $rewardDescription !== strip_tags($rewardDescription);

                $memberCanRedeem = $currentMember
                && $reward->is_active
                && $points > 0
                && (int) $currentMember->points >= $points;

                $redeemLoginUrl = \Illuminate\Support\Facades\Route::has('membership.login')
                ? route('membership.login')
                : '#';

                $redeemPostUrl = \Illuminate\Support\Facades\Route::has('membership.rewards.redeem')
                ? route('membership.rewards.redeem', $reward)
                : '#';
                @endphp

                <article class="reward-card-article px-3 w-full flex">
                    <div class="reward-card bg-white shadow-xl flex flex-col w-full group">
                        <div class="md:aspect-[4/3] aspect-[4/3] overflow-hidden bg-slate-100">
                            @if ($image)
                            <img src="{{ asset('storage/' . $image) }}" alt="{{ $alt }}" class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" loading="lazy" />
                            @endif
                        </div>

                        <div class="reward-card-body p-7 flex flex-col grow">
                            <h3 class="text-base text-slate-700 uppercase leading-snug font-medium mb-3 sm:text-lg">
                                {{ $reward->title }}
                            </h3>

                            @if ($rewardDescription)
                            <div class="mt-2 text-gray-600 text-xs leading-relaxed [&_p]:mb-3 [&_p:last-child]:mb-0 [&_strong]:font-semibold [&_ul]:list-disc [&_ul]:pl-5 [&_li]:mb-1 sm:text-sm">
                                @if ($rewardDescriptionHasHtml)
                                {!! $rewardDescription !!}
                                @else
                                {!! nl2br(e($rewardDescription)) !!}
                                @endif
                            </div>
                            @endif

                            <div class="mt-auto pt-12">
                                <div class="flex items-center justify-between gap-5">
                                    <p class="text-xs uppercase text-slate-950 sm:text-sm">
                                        @if ($pointsLabel)
                                        {{ $pointsLabel }}
                                        @else
                                        {{ number_format((float) $points, 0) }} Points
                                        @endif
                                    </p>

                                    @if ($currentMember)
                                    @if ($memberCanRedeem)
                                    <button type="button" data-reward-redeem-button data-redeem-action="{{ $redeemPostUrl }}" data-reward-title="{{ e($reward->title) }}" data-reward-points="{{ number_format((float) $points, 0) }}" class="inline-flex min-w-[125px] items-center justify-center border border-[#A88444] bg-[#A88444] px-6 py-3 text-xs uppercase text-white transition hover:border-[#B8945B] hover:bg-[#B8945B] sm:text-sm">
                                        Redeem
                                    </button>
                                    @else
                                    <button type="button" disabled class="inline-flex min-w-[115px] items-center justify-center border border-slate-300 bg-slate-100 px-4 py-2.5 text-xs uppercase text-slate-400 cursor-not-allowed tracking-[0.08em] font-medium sm:text-sm">
                                        Not Enough
                                    </button>
                                    @endif
                                    @else
                                    <a href="{{ $redeemLoginUrl }}" class="inline-flex min-w-[115px] items-center justify-center border border-[#A88444] bg-[#A88444] px-4 py-2.5 text-xs uppercase text-white transition hover:border-[#B8945B] hover:bg-[#B8945B] tracking-[0.08em] font-medium sm:text-sm">
                                        Redeem
                                    </a>
                                    @endif
                                </div>

                                @if ($currentMember && ! $memberCanRedeem)
                                <p class="mt-2 text-xs leading-relaxed text-slate-500 sm:text-sm">
                                    You need {{ number_format(max($points - (int) $currentMember->points, 0), 0) }} more points to redeem this reward.
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>

            <button type="button" class="reward-carousel-prev fold-carousel-arrow fold-image-carousel-arrow absolute left-3 md:left-8 lg:left-[45px] top-1/2 -translate-y-1/2 w-10 h-10 md:w-12 md:h-12 bg-[#A88444] text-white flex items-center justify-center z-10 transition hover:bg-[#A88444] tracking-[0.08em] font-medium" aria-label="Previous">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"></path>
                </svg>
            </button>

            <button type="button" class="reward-carousel-next fold-carousel-arrow fold-image-carousel-arrow absolute right-3 md:right-8 lg:right-[45px] top-1/2 -translate-y-1/2 w-10 h-10 md:w-12 md:h-12 bg-[#A88444] text-white flex items-center justify-center z-10 transition hover:bg-[#A88444] tracking-[0.08em] font-medium" aria-label="Next">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"></path>
                </svg>
            </button>
        </div>
    </section>
    @endforeach

    <script>
        function initRewardCarousels(attempt = 0) {
            if (!window.jQuery || !jQuery.fn || !jQuery.fn.slick) {
                if (attempt < 30) {
                    setTimeout(function () {
                        initRewardCarousels(attempt + 1);
                    }, 150);
                }

                return;
            }

            jQuery('[data-reward-carousel-section]').each(function () {
                const $section = jQuery(this);
                const $carousel = $section.find('.reward-carousel-items');

                if ($carousel.hasClass('slick-initialized')) {
                    return;
                }

                $carousel.slick({
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    arrows: true,
                    dots: true,
                    infinite: true,
                    prevArrow: $section.find('.reward-carousel-prev'),
                    nextArrow: $section.find('.reward-carousel-next'),
                    responsive: [
                        {
                            breakpoint: 1024,
                            settings: {
                                slidesToShow: 2,
                            },
                        },
                        {
                            breakpoint: 768,
                            settings: {
                                slidesToShow: 1,
                            },
                        },
                    ],
                });
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            initRewardCarousels();
        });

        window.addEventListener('load', function () {
            initRewardCarousels();
        });
    </script>
</x-layouts.app>

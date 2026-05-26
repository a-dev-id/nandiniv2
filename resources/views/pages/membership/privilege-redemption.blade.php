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
    $rewardGroups = $rewards
    ->filter(fn ($reward) => $reward->category)
    ->groupBy(fn ($reward) => $reward->category->id);
    @endphp

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
                <h2 class="text-3xl sm:text-4xl md:text-5xl leading-snug tracking-[0.18em] md:tracking-[0.25em] uppercase text-slate-950 font-medium">
                    {{ $categoryTitle }}
                </h2>

                @if ($categoryDescription)
                <p class="mt-2 text-base md:text-lg leading-relaxed text-slate-800">
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

                $pointsLabel = trim((string) ($reward->points_label ?? ''));

                $rewardDescription = trim((string) (
                $reward->description
                ?? $reward->excerpt
                ?? ''
                ));

                $rewardDescriptionHasHtml = $rewardDescription
                && $rewardDescription !== strip_tags($rewardDescription);

                $redeemUrl = \Illuminate\Support\Facades\Route::has('membership.login')
                ? route('membership.login')
                : '#';
                @endphp

                <article class="reward-card-article px-3 w-full flex">
                    <div class="reward-card bg-white shadow-xl flex flex-col w-full group">
                        <div class="md:aspect-[4/3] aspect-square overflow-hidden bg-slate-100">
                            @if ($image)
                            <img src="{{ asset('storage/' . $image) }}" alt="{{ $alt }}" class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" loading="lazy" />
                            @endif
                        </div>

                        <div class="reward-card-body p-7 flex flex-col grow">
                            <h3 class="text-slate-950 uppercase tracking-[0.18em] text-xl md:text-2xl leading-snug font-medium">
                                {{ $reward->title }}
                            </h3>

                            @if ($rewardDescription)
                            <div class="
                                mt-5
                                text-slate-900
                                text-[15px]
                                leading-relaxed
                                [&_p]:mb-3
                                [&_p:last-child]:mb-0
                                [&_strong]:font-semibold
                                [&_ul]:list-disc
                                [&_ul]:pl-5
                                [&_ul]:mt-3
                                [&_li]:mb-1
                            ">
                                @if ($rewardDescriptionHasHtml)
                                {!! $rewardDescription !!}
                                @else
                                {!! nl2br(e($rewardDescription)) !!}
                                @endif
                            </div>
                            @endif

                            <div class="mt-auto pt-12 flex items-center justify-between gap-5">
                                <p class="text-sm uppercase text-slate-950">
                                    @if ($pointsLabel)
                                    {{ $pointsLabel }}
                                    @else
                                    {{ number_format((float) $points, 0) }} Points
                                    @endif
                                </p>

                                <a href="{{ $redeemUrl }}" class="inline-flex min-w-[125px] items-center justify-center border border-slate-950 px-6 py-3 text-sm uppercase text-slate-950 hover:bg-slate-950 hover:text-white transition">
                                    Redeem
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>

            <button type="button" class="reward-carousel-prev absolute left-3 md:left-8 lg:left-[45px] top-1/2 -translate-y-1/2 w-10 h-10 md:w-12 md:h-12 bg-black text-white flex items-center justify-center z-10" aria-label="Previous">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"></path>
                </svg>
            </button>

            <button type="button" class="reward-carousel-next absolute right-3 md:right-8 lg:right-[45px] top-1/2 -translate-y-1/2 w-10 h-10 md:w-12 md:h-12 bg-black text-white flex items-center justify-center z-10" aria-label="Next">
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
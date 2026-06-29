@push('meta')
@php
$metaTitle = $page->meta_title ?: $page->title;
$metaDescription = $page->meta_description ?? '';
$metaImage = $page->hero_image ?: $page->hero_mobile_image ?: null;
@endphp

<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">

@if (! empty($page->meta_keywords))
<meta name="keywords" content="{{ $page->meta_keywords }}">
@endif

<meta name="author" content="Nandini Jungle by Hanging Gardens">
<link rel="canonical" href="{{ url()->current() }}">

<meta property="og:type" content="website">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="Nandini Jungle by Hanging Gardens">

@if ($metaImage)
<meta property="og:image" content="{{ asset('storage/' . $metaImage) }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta name="twitter:image" content="{{ asset('storage/' . $metaImage) }}">
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
@endpush

@php
$member = auth('member')->user();

$memberName = $member?->name ?: $member?->full_name ?: 'Inner Circle Member';
$memberEmail = $member?->email ?: '-';
$memberInitial = strtoupper(mb_substr($memberName, 0, 1));

$createdDate = $member?->created_at
? $member->created_at->format('d F Y')
: '-';

$validUntilDate = $member?->membership_expires_at
? $member->membership_expires_at->format('Y/m/d')
: '-';

$location = $member?->country ?: '-';

$memberPoints = (int) ($member?->points ?? 0);
$pointTransactionImage = asset('images/membership/dollar.png');

$formatPointLabel = function ($points, string $case = 'title'): string {
$numericPoints = (float) $points;
$label = abs($numericPoints) === 1.0 ? 'Point' : 'Points';

if ($case === 'upper') {
$label = strtoupper($label);
} elseif ($case === 'lower') {
$label = strtolower($label);
}

return number_format($numericPoints, 0) . ' ' . $label;
};

$memberTier = strtolower((string) ($member?->tier ?? \App\Models\Member::getTierByPoints($memberPoints)));

$validTiers = ['bronze', 'silver', 'gold', 'platinum'];

if (! in_array($memberTier, $validTiers, true)) {
$memberTier = 'bronze';
}

$cardMap = [
'bronze' => asset('images/membership/dana-blank2.jpg'),
'silver' => asset('images/membership/upaya-blank2.jpg'),
'gold' => asset('images/membership/dhyana-blank2.jpg'),
'platinum' => asset('images/membership/jnana-blank2.jpg'),
];

$tierNameMap = [
'bronze' => 'Dana',
'silver' => 'Upaya',
'gold' => 'Dhyana',
'platinum' => 'Jnana',
];

$tierLabelMap = [
'bronze' => 'Dana',
'silver' => 'Upaya',
'gold' => 'Dhyana',
'platinum' => 'Jnana',
];

$tierBookingCodeMap = [
'bronze' => 'DANA',
'silver' => 'UPAYA',
'gold' => 'DHYANA',
'platinum' => 'JNANA',
];

$memberBookingCode = $tierBookingCodeMap[$memberTier] ?? 'DANA';
$bookingBaseUrl = 'https://nandinijunglebyhanginggardens.reserve-online.net/';
$memberBookingUrl = $bookingBaseUrl . '?' . http_build_query([
'checkin' => 'today',
'nights' => 2,
'voucher' => $memberBookingCode,
]);

$nextTierMap = [
'bronze' => 'silver',
'silver' => 'gold',
'gold' => 'platinum',
'platinum' => null,
];

$nextTierMinimumPoints = [
'silver' => 401,
'gold' => 801,
'platinum' => 1201,
];

$currentCardImage = $cardMap[$memberTier] ?? $cardMap['bronze'];
$memberCardDownloadName = \Illuminate\Support\Str::slug($memberName . '-' . ($tierLabelMap[$memberTier] ?? 'membership') . '-card') . '.png';

$nextTier = $nextTierMap[$memberTier] ?? null;
$nextCardImage = $nextTier ? ($cardMap[$nextTier] ?? null) : null;

$pointsToNextTier = null;

if ($nextTier && isset($nextTierMinimumPoints[$nextTier])) {
$pointsToNextTier = max(0, $nextTierMinimumPoints[$nextTier] - $memberPoints);
}

$profilePhoto = $member?->profile_photo ?? $member?->photo ?? null;

$profilePhotoUrl = $profilePhoto
? (str_starts_with($profilePhoto, 'http') ? $profilePhoto : asset('storage/' . $profilePhoto))
: null;

$resolveImage = function (?string $raw): ?string {
$raw = trim((string) $raw);

if ($raw === '') {
return null;
}

if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
return $raw;
}

if (str_starts_with($raw, '/storage/')) {
return $raw;
}

if (str_starts_with($raw, 'storage/')) {
return '/' . $raw;
}

if (str_starts_with($raw, 'images/')) {
return asset($raw);
}

if (str_starts_with($raw, '/')) {
return $raw;
}

return asset('storage/' . $raw);
};

$getHistoryValue = function ($item, array $keys, $default = null) {
foreach ($keys as $key) {
$value = data_get($item, $key);

if ($value !== null && $value !== '') {
return $value;
}
}

return $default;
};

$formatHistoryDate = function ($date): string {
if (empty($date)) {
return '-';
}

try {
if ($date instanceof \Carbon\CarbonInterface) {
return strtoupper($date->format('d M Y'));
}

return strtoupper(\Carbon\Carbon::parse($date)->format('d M Y'));
} catch (\Throwable $e) {
return strtoupper((string) $date);
}
};

$activityHistories = collect(
$activityHistories
?? $membershipHistories
?? $pointHistories
?? $rewardHistories
?? []
);

$activeRedemptions = collect();

try {
if ($member) {
$mergedHistories = collect();
$usedRedemptions = collect();

if (method_exists($member, 'rewardRedemptions')) {
$rewardRedemptions = $member->rewardRedemptions()
->with('reward')
->latest()
->take(50)
->get();

$activeRedemptions = $rewardRedemptions
->filter(fn ($redemption) => $redemption->status === \App\Models\MemberRewardRedemption::STATUS_PENDING)
->values();

$usedRedemptions = $rewardRedemptions
->filter(fn ($redemption) => $redemption->status === \App\Models\MemberRewardRedemption::STATUS_USED || $redemption->used_at)
->values();
}

if ($activityHistories->isEmpty()) {
$mergedHistories = $mergedHistories->merge($usedRedemptions);

if (method_exists($member, 'pointTransactions')) {
$pointTransactions = $member->pointTransactions()
->where('type', '!=', 'redeem')
->latest()
->take(30)
->get();

$mergedHistories = $mergedHistories->merge($pointTransactions);
}

if (method_exists($member, 'rewardTransactions')) {
$rewardTransactions = $member->rewardTransactions()
->latest()
->take(30)
->get();

$mergedHistories = $mergedHistories->merge($rewardTransactions);
}

$activityHistories = $mergedHistories
->sortByDesc(function ($item) {
try {
$historyDate = data_get($item, 'used_at') ?: data_get($item, 'created_at');

return $historyDate
? \Carbon\Carbon::parse($historyDate)->timestamp
: 0;
} catch (\Throwable $e) {
return 0;
}
})
->take(30)
->values();
}
}
} catch (\Throwable $e) {
$activityHistories = collect();
$activeRedemptions = collect();
}

$historyDisplayLimit = 3;

$dashboardRewards = collect($rewards ?? []);

if ($dashboardRewards->isEmpty()) {
try {
$dashboardRewards = \App\Models\Reward::query()
->with('category')
->where('is_active', true)
->orderBy('points_required')
->orderBy('sort_order')
->limit(9)
->get();
} catch (\Throwable $e) {
$dashboardRewards = collect();
}
} else {
$dashboardRewards = $dashboardRewards
->sortBy(fn ($reward) => (int) ($reward->points_required ?? $reward->points ?? $reward->point_cost ?? 0))
->take(9)
->values();
}

$dashboardAccommodations = collect($accommodations ?? []);

if ($dashboardAccommodations->isEmpty()) {
try {
$dashboardAccommodations = \App\Models\Accommodation::query()
->where('is_active', true)
->get();
} catch (\Throwable $e) {
$dashboardAccommodations = collect();
}
}

$normalizeRoomKey = function (?string $value): string {
return preg_replace('/[^a-z0-9]+/', '', strtolower((string) $value)) ?? '';
};

$getBookingRoomImage = function ($booking) use ($dashboardAccommodations, $resolveImage, $normalizeRoomKey): ?string {
$roomName = $normalizeRoomKey($booking->room_name ?? null);
$roomType = $normalizeRoomKey($booking->room_type ?? null);

$accommodation = $dashboardAccommodations->first(function ($item) use ($roomName, $roomType, $normalizeRoomKey) {
$title = $normalizeRoomKey($item->title ?? null);
$villaCode = $normalizeRoomKey($item->villa_code ?? null);

return ($roomName !== '' && $title !== '' && ($roomName === $title || str_contains($roomName, $title) || str_contains($title, $roomName)))
|| ($roomType !== '' && $villaCode !== '' && $roomType === $villaCode);
});

if (! $accommodation) {
return null;
}

return $resolveImage($accommodation->card_image ?: $accommodation->hero_image ?: null);
};

$syncedBookings = collect();

try {
if ($member && \Illuminate\Support\Facades\Schema::hasTable('synced_webhotelier_bookings')) {
$syncedBookings = $member->syncedBookings()->latest('check_in')->latest()->get();
}
} catch (\Throwable $e) {
$syncedBookings = collect();
}

$today = \Carbon\Carbon::today();

$confirmedBookingStatuses = ['confirmed', 'confirm', 'booked', 'reservation_confirmed'];

$confirmedBookings = $syncedBookings
->filter(function ($booking) use ($confirmedBookingStatuses) {
$status = strtolower(trim(str_replace(['-', ' '], '_', (string) $booking->status)));

return in_array($status, $confirmedBookingStatuses, true);
})
->values();

$checkedOutBookings = $confirmedBookings
->filter(fn ($booking) => $booking->check_out && \Carbon\Carbon::parse($booking->check_out)->lt($today))
->values();

$currentBookings = $confirmedBookings
->reject(fn ($booking) => $booking->check_out && \Carbon\Carbon::parse($booking->check_out)->lt($today))
->values();

$bookingHistories = $checkedOutBookings->map(function ($booking) {
$room = $booking->room_name ?: $booking->room_type ?: 'Room';
$dateRange = trim(($booking->check_in?->format('d M Y') ?? '-') . ' - ' . ($booking->check_out?->format('d M Y') ?? '-'));

return [
'title' => 'Booking ' . ($booking->booking_number ?: ''),
'description' => trim($room . ' | ' . $dateRange),
'status' => 'checked_out',
'date' => $booking->check_out,
'points' => 0,
'code' => $booking->booking_number,
'image' => $getBookingRoomImage($booking),
];
});

$activityHistories = $activityHistories
->merge($bookingHistories)
->sortByDesc(function ($item) {
try {
$historyDate = data_get($item, 'date')
?: data_get($item, 'used_at')
?: data_get($item, 'created_at');

return $historyDate
? \Carbon\Carbon::parse($historyDate)->timestamp
: 0;
} catch (\Throwable $e) {
return 0;
}
})
->values();

$hasMoreHistories = $activityHistories->count() > $historyDisplayLimit;
@endphp

<x-layouts.app>
    <x-heroes.image-hero :page="$page" />

    <style>
        .dashboard-reward-carousel-section .slick-list,
        .dashboard-accommodation-carousel-section .slick-list {
            padding-top: 4px !important;
            padding-bottom: 46px !important;
        }

        .dashboard-reward-carousel-section .slick-track,
        .dashboard-accommodation-carousel-section .slick-track {
            display: flex !important;
        }

        .dashboard-reward-carousel-section .slick-slide,
        .dashboard-accommodation-carousel-section .slick-slide {
            height: auto !important;
        }

        .dashboard-reward-carousel-section .slick-slide>div,
        .dashboard-accommodation-carousel-section .slick-slide>div {
            height: 100%;
        }

        .dashboard-reward-carousel-section .dashboard-reward-card-article,
        .dashboard-accommodation-carousel-section .dashboard-accommodation-card-article {
            height: 100%;
            padding-bottom: 8px;
        }

        .dashboard-reward-carousel-section .dashboard-reward-card,
        .dashboard-accommodation-carousel-section .dashboard-accommodation-card {
            height: 100%;
            min-height: 540px;
        }

        .dashboard-reward-carousel-section .dashboard-reward-card-body,
        .dashboard-accommodation-carousel-section .dashboard-accommodation-card-body {
            min-height: 285px;
        }

        @media (max-width: 767px) {

            .dashboard-reward-carousel-section .dashboard-reward-card,
            .dashboard-accommodation-carousel-section .dashboard-accommodation-card {
                min-height: auto;
            }

            .dashboard-reward-carousel-section .dashboard-reward-card-body,
            .dashboard-accommodation-carousel-section .dashboard-accommodation-card-body {
                min-height: auto;
            }
        }

    </style>

    {{-- MEMBER DETAIL --}}
    <section class="bg-white px-6 py-14 md:py-20">
        <div class="mx-auto w-full max-w-6xl">

            <div class="mx-auto max-w-3xl text-center">
                <h1 class="text-xl leading-snug uppercase text-slate-700 font-medium mb-3 sm:text-2xl">
                    Member Profile
                </h1>

                <p class="mt-2 text-xs leading-relaxed text-gray-600 max-w-2xl sm:max-w-3xl md:max-w-5xl mx-auto sm:text-sm">
                    Access your exclusive member privileges.
                </p>
            </div>

            <div class="mt-8 md:mt-10 grid grid-cols-1 items-center gap-8 lg:grid-cols-12 lg:gap-8">

                <div class="lg:col-span-3">
                    <div class="mx-auto flex aspect-[4/5] w-full max-w-[170px] items-center justify-center overflow-hidden rounded-[16px] bg-[#F7F7F7] shadow-md lg:mx-0">
                        @if ($profilePhotoUrl)
                        <img src="{{ $profilePhotoUrl }}" alt="{{ $memberName }}" class="h-full w-full object-cover" loading="lazy">
                        @else
                        <span class="text-4xl font-medium uppercase text-[#A88444] sm:text-5xl">
                            {{ $memberInitial }}
                        </span>
                        @endif
                    </div>
                </div>

                <div class="text-center lg:col-span-4 lg:text-left">
                    <h2 class="text-lg leading-snug uppercase text-slate-700 font-medium mb-3 sm:text-xl">
                        {{ $memberName }}
                    </h2>

                    <div class="mt-2 mx-auto max-w-[340px] space-y-2 text-[11px] sm:text-[14px] leading-6 text-gray-600 lg:mx-0">
                        <div class="grid grid-cols-[88px_1fr] gap-1 text-left">
                            <span>Email</span>
                            <span class="break-all">: {{ $memberEmail }}</span>
                        </div>

                        <div class="grid grid-cols-[88px_1fr] gap-1 text-left">
                            <span>Join on</span>
                            <span>: {{ $createdDate }}</span>
                        </div>

                        <div class="grid grid-cols-[88px_1fr] gap-1 text-left">
                            <span>Location</span>
                            <span>: {{ $location }}</span>
                        </div>
                    </div>

                    <div class="mt-2 flex justify-center lg:justify-start">
                        <a href="{{ \Illuminate\Support\Facades\Route::has('membership.profile.edit') ? route('membership.profile.edit') : '#' }}" class="inline-flex min-w-[145px] items-center justify-center border border-[#A88444] bg-[#A88444] px-4 py-2.5 text-xs uppercase text-white transition hover:border-[#B8945B] hover:bg-[#B8945B] tracking-[0.08em] font-medium sm:text-sm">
                            Edit Profile
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="mx-auto w-full max-w-[390px]">

                        <button type="button" class="group block w-full overflow-hidden rounded-[20px] shadow-lg transition focus:outline-none focus:ring-2 focus:ring-[#A88444]/40" aria-label="View membership card" data-member-card-open>
                            <div class="relative">
                                <img src="{{ $currentCardImage }}" alt="{{ $tierNameMap[$memberTier] ?? 'Membership Card' }} {{ $tierLabelMap[$memberTier] ?? '' }} Card" class="block w-full transition duration-500 group-hover:scale-[1.015] cursor-pointer" loading="lazy" data-member-card-image>

                                <div class="pointer-events-none absolute inset-0 text-white">

                                    <div class="absolute rounded-[3px] border border-white/70 bg-black/35 px-2.5 py-1.5 shadow-sm backdrop-blur-[1px]" style="left: 8%; bottom: 14%;">
                                        <p class="text-[7px] sm:text-[10px] md:text-[11px] font-bold uppercase leading-none text-white drop-shadow">
                                            {{ $formatPointLabel($memberPoints, 'upper') }}
                                        </p>
                                    </div>

                                    <div class="absolute top-[42%] right-[8%] w-[64%] text-right">
                                        <p class="whitespace-normal break-words text-[12px] sm:text-[16px] md:text-[18px] font-bold uppercase leading-tight text-white/95 drop-shadow">
                                            {{ $memberName }}
                                        </p>
                                    </div>

                                    <div class="absolute bottom-[23%] right-[8%] max-w-[48%] text-right">
                                        <p class="text-[8px] sm:text-[12px] md:text-[13px] font-semibold uppercase leading-none drop-shadow-md">
                                            {{ $tierLabelMap[$memberTier] ?? 'Dana' }}
                                        </p>
                                    </div>

                                    <div class="absolute bottom-[8.5%] right-[8%] text-right">
                                        <p class="text-[5.5px] sm:text-[6.5px] md:text-[7.5px] font-bold uppercase leading-tight text-white/95 drop-shadow">
                                            Valid Till
                                        </p>

                                        <p class="mt-0.5 text-[5.5px] sm:text-[6.5px] md:text-[7.5px] font-bold uppercase leading-tight text-white/95 drop-shadow">
                                            {{ $validUntilDate }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </button>

                        <div class="mt-2 grid grid-cols-1 items-center gap-3 sm:grid-cols-[1fr_110px]">
                            @if ($nextTier && $pointsToNextTier !== null)
                            <div class="rounded-lg bg-[#F1F1F1] px-5 py-3 text-center">
                                <p class="text-[10px] sm:text-[13px] uppercase leading-snug text-slate-700">
                                    Another <span class="font-bold">{{ $formatPointLabel($pointsToNextTier) }}</span>
                                </p>

                                <p class="mt-1 text-[7px] sm:text-[9px] uppercase text-slate-700">
                                    To reach {{ $tierLabelMap[$nextTier] }} Member
                                </p>
                            </div>

                            @if ($nextCardImage)
                            <div class="mx-auto w-[110px] overflow-hidden rounded-lg shadow-md sm:mx-0">
                                <img src="{{ $nextCardImage }}" alt="{{ $tierLabelMap[$nextTier] }} Membership Card" class="block w-full" loading="lazy">
                            </div>
                            @endif
                            @else
                            <div class="rounded-lg bg-[#F1F1F1] px-5 py-3 text-center sm:col-span-2">
                                <p class="text-[10px] sm:text-[13px] uppercase leading-snug text-slate-700">
                                    You are now a <span class="font-bold">Jnana Member</span>
                                </p>

                                <p class="mt-1 text-[7px] sm:text-[9px] uppercase text-slate-700">
                                    You have reached the highest membership tier.
                                </p>
                            </div>
                            @endif
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <div class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/70 px-4 py-8" data-member-card-modal aria-hidden="true" inert>
        <button type="button" class="absolute inset-0 h-full w-full cursor-default" aria-label="Close membership card preview" data-member-card-close></button>

        <div class="relative w-full max-w-[640px]">
            <div class="mb-4 flex justify-end gap-3">
                <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/70 bg-white text-[#A88444] shadow-lg transition hover:bg-[#A88444] hover:text-white focus:outline-none focus:ring-2 focus:ring-white/70 tracking-[0.08em] font-medium" aria-label="Download membership card" title="Download membership card" data-member-card-download data-card-image="{{ $currentCardImage }}" data-member-name="{{ e($memberName) }}" data-member-points="{{ e($formatPointLabel($memberPoints, 'upper')) }}" data-member-tier="{{ e($tierLabelMap[$memberTier] ?? 'Dana') }}" data-valid-until="{{ e($validUntilDate) }}" data-download-name="{{ e($memberCardDownloadName) }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <path d="M7 10l5 5 5-5"></path>
                        <path d="M12 15V3"></path>
                    </svg>
                </button>

                <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/70 bg-white text-slate-700 shadow-lg transition hover:bg-slate-950 hover:text-white focus:outline-none focus:ring-2 focus:ring-white/70 tracking-[0.08em] font-medium" aria-label="Close membership card preview" title="Close" data-member-card-close>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M18 6 6 18"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="relative overflow-hidden rounded-[20px] shadow-2xl">
                <img src="{{ $currentCardImage }}" alt="{{ $tierNameMap[$memberTier] ?? 'Membership Card' }} {{ $tierLabelMap[$memberTier] ?? '' }} Card" class="block w-full" loading="lazy">

                <div class="pointer-events-none absolute inset-0 text-white">
                    <div class="absolute rounded-[3px] border border-white/70 bg-black/35 px-4 py-2.5 shadow-sm backdrop-blur-[1px]" style="left: 8%; bottom: 14%;">
                        <p class="text-[11px] text-xs md:text-[16px] font-bold uppercase leading-none text-white drop-shadow sm:text-[13px] sm:text-sm">
                            {{ $formatPointLabel($memberPoints, 'upper') }}
                        </p>
                    </div>

                    <div class="absolute top-[42%] right-[8%] w-[64%] text-right">
                        <p class="whitespace-normal break-words text-[18px] sm:text-[25px] md:text-[32px] font-bold uppercase leading-tight text-white/95 drop-shadow">
                            {{ $memberName }}
                        </p>
                    </div>

                    <div class="absolute bottom-[23%] right-[8%] max-w-[48%] text-right">
                        <p class="text-[11px] sm:text-[14px] text-xs font-semibold uppercase leading-none drop-shadow-md">
                            {{ $tierLabelMap[$memberTier] ?? 'Dana' }}
                        </p>
                    </div>

                    <div class="absolute bottom-[8.5%] right-[8%] text-right">
                        <p class="text-[7px] sm:text-[11px] md:text-[12px] font-bold uppercase leading-tight text-white/95 drop-shadow">
                            Valid Till
                        </p>

                        <p class="mt-1 text-[7px] sm:text-[11px] md:text-[12px] font-bold uppercase leading-tight text-white/95 drop-shadow">
                            {{ $validUntilDate }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="bg-white px-6 pb-0">
        <div class="mx-auto w-full max-w-6xl">
            <div class="flex flex-nowrap justify-center gap-1.5 border-b border-slate-200 pb-4 sm:gap-2" data-membership-dashboard-tabs>
                <button type="button" class="min-w-0 flex-1 border border-[#A88444] bg-[#A88444] px-2 py-2.5 text-[8px] font-medium uppercase text-white transition sm:flex-none sm:px-5 sm:text-[12px] tracking-[0.08em]" data-dashboard-tab="bookings">
                    My Booking
                </button>

                <button type="button" class="min-w-0 flex-1 border border-slate-300 bg-white px-2 py-2.5 text-[8px] font-medium uppercase text-slate-700 transition hover:border-[#A88444] hover:text-[#A88444] sm:flex-none sm:px-5 sm:text-[12px] tracking-[0.08em]" data-dashboard-tab="redeem">
                    Point Redeem
                </button>

                <button type="button" class="min-w-0 flex-1 border border-slate-300 bg-white px-2 py-2.5 text-[8px] font-medium uppercase text-slate-700 transition hover:border-[#A88444] hover:text-[#A88444] sm:flex-none sm:px-5 sm:text-[12px] tracking-[0.08em]" data-dashboard-tab="history">
                    History
                </button>
            </div>

            <div class="pt-8" data-dashboard-tab-panel="bookings">
                @if ($currentBookings->isNotEmpty())
                <div class="overflow-x-auto border border-slate-200 bg-white">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-xs sm:text-sm">
                        <thead class="bg-[#F7F7F7] text-[9px] uppercase text-slate-600 sm:text-[11px]">
                            <tr>
                                <th class="px-4 py-3">Booking Number</th>
                                <th class="px-4 py-3">Image</th>
                                <th class="px-4 py-3">Room Name</th>
                                <th class="px-4 py-3">Check-in</th>
                                <th class="px-4 py-3">Check-out</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Currency</th>
                                <th class="px-4 py-3 text-right">Total</th>
                                <th class="px-4 py-3 text-right">Estimated Points</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @foreach ($currentBookings as $booking)
                            @php
                            $roomImage = $getBookingRoomImage($booking);
                            @endphp
                            <tr>
                                <td class="px-4 py-4 font-bold text-slate-700">{{ $booking->booking_number }}</td>
                                <td class="px-4 py-4">
                                    @if ($roomImage)
                                    <img src="{{ $roomImage }}" alt="{{ $booking->room_name ?: 'Room' }}" class="h-14 w-20 object-cover" loading="lazy">
                                    @else
                                    <div class="flex h-14 w-20 items-center justify-center bg-[#F7F7F7] text-[8px] font-bold uppercase text-[#916B2C] sm:text-[10px]">
                                        Room
                                    </div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 font-semibold text-slate-700">{{ $booking->room_name ?: '-' }}</td>
                                <td class="px-4 py-4">{{ $booking->check_in?->format('d M Y') ?? '-' }}</td>
                                <td class="px-4 py-4">{{ $booking->check_out?->format('d M Y') ?? '-' }}</td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-[9px] font-bold uppercase text-slate-700 sm:text-[11px]">
                                        {{ $booking->status ?: '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">{{ $booking->currency ?: '-' }}</td>
                                <td class="px-4 py-4 text-right">{{ $booking->booking_total !== null ? number_format((float) $booking->booking_total, 0) : '-' }}</td>
                                <td class="px-4 py-4 text-right">
                                    {{ $booking->booking_total !== null ? number_format(\App\Models\Member::calculatePointsFromConsumption((float) $booking->booking_total), 0) : '-' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="bg-[#F7F7F7] px-6 py-12 text-center">
                    <p class="text-xs uppercase text-slate-700 sm:text-sm">
                        No booking found yet.
                    </p>
                </div>
                @endif
            </div>

            <div class="hidden pt-8" data-dashboard-tab-panel="redeem">
                <div class="overflow-x-auto border border-slate-200 bg-white">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-xs sm:text-sm">
                        <thead class="bg-[#F7F7F7] text-[9px] uppercase text-slate-700 sm:text-[11px]">
                            <tr>
                                <th class="px-4 py-4">Reward</th>
                                <th class="px-4 py-4">Code</th>
                                <th class="px-4 py-4">Redeemed On</th>
                                <th class="px-4 py-4">Valid Until</th>
                                <th class="px-4 py-4 text-right">Points Used</th>
                                <th class="px-4 py-4">Status</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse ($activeRedemptions as $redemption)
                            @php
                            $redemptionTitle = $redemption->reward_name ?: ($redemption->reward?->title ?? $redemption->reward?->name ?? 'Reward');
                            $redemptionImage = $resolveImage($redemption->reward?->image ?? null);
                            @endphp
                            <tr>
                                <td class="px-4 py-4">
                                    <div class="flex min-w-[240px] items-center gap-3">
                                        @if ($redemptionImage)
                                        <img src="{{ $redemptionImage }}" alt="{{ $redemptionTitle }}" class="h-14 w-20 object-cover" loading="lazy">
                                        @else
                                        <div class="flex h-14 w-20 items-center justify-center bg-[#F7F7F7] text-lg font-medium uppercase text-[#916B2C] sm:text-xl">
                                            {{ strtoupper(mb_substr($redemptionTitle, 0, 1)) }}
                                        </div>
                                        @endif

                                        <span class="font-bold text-slate-700">{{ $redemptionTitle }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4">{{ $redemption->redemption_code ?: '-' }}</td>
                                <td class="px-4 py-4">{{ $redemption->created_at?->format('d M Y') ?? '-' }}</td>
                                <td class="px-4 py-4">{{ $redemption->expires_at?->format('d M Y') ?? '-' }}</td>
                                <td class="px-4 py-4 text-right">{{ number_format((int) $redemption->points_used, 0) }}</td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex rounded-full bg-[#A88444]/10 px-3 py-1 text-[9px] font-bold uppercase text-[#916B2C] sm:text-[11px]">
                                        {{ $redemption->status_label ?? 'Pending' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center">
                                    <p class="text-xs uppercase text-slate-700 sm:text-sm">
                                        No active redemptions yet.
                                    </p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <div class="hidden" data-dashboard-tab-panel="history">
        {{-- ACTIVITY HISTORY --}}
        <section class="bg-white px-6 pt-8 pb-0">
            <div class="mx-auto w-full max-w-6xl">

                <div class="overflow-x-auto border border-slate-200 bg-white">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-xs sm:text-sm">
                        <thead class="bg-[#F7F7F7] text-[9px] uppercase text-slate-700 sm:text-[11px]">
                            <tr>
                                <th class="px-4 py-4">Reward</th>
                                <th class="px-4 py-4">Code</th>
                                <th class="px-4 py-4">Redeemed/Received On</th>
                                <th class="px-4 py-4">Valid Until</th>
                                <th class="px-4 py-4 text-right">Points Used</th>
                                <th class="px-4 py-4">Status</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse ($activityHistories as $historyIndex => $activity)
                            @php
                            if (is_object($activity) && method_exists($activity, 'loadMissing')) {
                            try {
                            $activity->loadMissing('reward');
                            } catch (\Throwable $e) {
                            //
                            }
                            }

                            $isRewardRedemption = is_a($activity, \App\Models\MemberRewardRedemption::class);
                            $isPointTransaction = is_a($activity, \App\Models\MemberPointTransaction::class);

                            $title = $getHistoryValue($activity, [
                            'reward_name',
                            'title',
                            'name',
                            'reward.title',
                            'reward.name',
                            'offer.title',
                            'offer.name',
                            'description_title',
                            ], null);

                            $statusRaw = strtolower((string) $getHistoryValue($activity, [
                            'status',
                            'type',
                            'transaction_type',
                            'activity_type',
                            ], ''));

                            if (! $title && $isPointTransaction) {
                            $title = match ($statusRaw) {
                            'earn' => 'Points Added',
                            'adjustment' => 'Point Adjustment',
                            'redeem' => 'Points Redeemed',
                            default => 'Point Transaction',
                            };
                            }

                            $historyDescription = trim((string) $getHistoryValue($activity, [
                            'description',
                            'details',
                            'note',
                            ], ''));

                            $title = $title ?: 'Membership Activity';

                            $redemptionCode = $getHistoryValue($activity, [
                            'redemption_code',
                            'redeem_code',
                            'code',
                            ], null);

                            $validUntilRaw = $getHistoryValue($activity, [
                            'expires_at',
                            'valid_until',
                            'valid_date',
                            'expired_at',
                            'reward.expires_at',
                            ], null);

                            $validUntil = $formatHistoryDate($validUntilRaw);
                            $pointsRaw = $getHistoryValue($activity, [
                            'points',
                            'point',
                            'points_change',
                            'point_change',
                            'points_used',
                            'amount',
                            'reward.points',
                            'reward.points_required',
                            'offer.points',
                            ], 0);

                            $pointsNumber = (int) preg_replace('/[^-\d]/', '', (string) $pointsRaw);

                            if ($isRewardRedemption) {
                            $pointsNumber = -abs((int) $getHistoryValue($activity, ['points_used'], $pointsNumber));
                            }

                            if ($statusRaw === '') {
                            $statusRaw = $pointsNumber < 0 ? 'used' : 'completed' ; } if ($statusRaw==='earn' ) { $statusRaw='earned' ; } if ($isRewardRedemption && $statusRaw==='pending' ) { $statusRaw='redeemed' ; } $statusLabel=strtoupper(str_replace(['_', '-' ], ' ' , $statusRaw)); $statusClass=match ($statusRaw) { 'pending' , 'redeemed' , 'redeem'=> 'bg-[#F3EDE4] text-[#916B2C]',
                                'used', 'checked_out', 'completed' => 'bg-slate-100 text-slate-700',
                                'cancelled', 'expired' => 'bg-red-50 text-red-700',
                                default => 'bg-[#F3EDE4] text-[#916B2C]',
                                };

                                $date = $formatHistoryDate($getHistoryValue($activity, [
                                'redeemed_at',
                                'created_at',
                                'date',
                                'activity_date',
                                'transaction_date',
                                'used_at',
                                'purchased_at',
                                ], null));

                                $pointsUsedDisplay = abs($pointsNumber) > 0 ? number_format(abs($pointsNumber), 0) : '-';
                                $historyImage = $isPointTransaction
                                ? $pointTransactionImage
                                : $resolveImage($getHistoryValue($activity, [
                                'image',
                                'reward.image',
                                'reward.card_image',
                                'reward.hero_image',
                                ], null));
                                @endphp

                                <tr class="{{ $historyIndex >= $historyDisplayLimit ? 'hidden' : '' }}" @if ($historyIndex>= $historyDisplayLimit) data-history-extra @endif>
                                    <td class="px-4 py-5">
                                        <div class="flex min-w-[240px] items-center gap-3">
                                            @if ($historyImage)
                                            <img src="{{ $historyImage }}" alt="{{ $title }}" class="h-14 w-16 object-cover" loading="lazy">
                                            @else
                                            <div class="flex h-14 w-20 items-center justify-center bg-[#F7F7F7] text-lg font-medium uppercase text-[#916B2C] sm:text-xl">
                                                {{ strtoupper(mb_substr($title, 0, 1)) }}
                                            </div>
                                            @endif

                                            <div>
                                                <p class="font-bold text-slate-950">{{ $title }}</p>

                                                @if ($historyDescription !== '')
                                                <p class="mt-1 text-xs leading-relaxed text-slate-500 sm:text-sm">
                                                    {{ $historyDescription }}
                                                </p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-5 text-slate-700">{{ $redemptionCode ?: '-' }}</td>
                                    <td class="px-4 py-5 text-slate-700">{{ $date }}</td>
                                    <td class="px-4 py-5 text-slate-700">{{ $validUntil }}</td>
                                    <td class="px-4 py-5 text-right text-slate-700">{{ $pointsUsedDisplay }}</td>
                                    <td class="px-4 py-5">
                                        <span class="{{ $statusClass }} inline-flex rounded-full px-3 py-1 text-[9px] font-bold uppercase sm:text-[11px]">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center">
                                        <p class="text-xs uppercase text-slate-700 sm:text-sm">
                                            No completed history yet.
                                        </p>
                                    </td>
                                </tr>
                                @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($hasMoreHistories)
                <div class="mt-10 text-center">
                    <button type="button" data-history-view-more class="inline-flex min-w-[145px] items-center justify-center border border-[#A88444] bg-[#A88444] px-5 py-2.5 text-xs uppercase text-white transition hover:border-[#B8945B] hover:bg-[#B8945B] tracking-[0.08em] font-medium sm:text-sm">
                        View More
                    </button>
                </div>
                @endif
            </div>
        </section>
    </div>

    <section class="w-full bg-white px-0 pt-14 md:pt-20">
        <div class="relative min-h-[380px] overflow-hidden bg-slate-900 sm:min-h-[460px]">
            <img src="{{ asset('images/membership/join-today.webp') }}" alt="Nandini Jungle by Hanging Gardens member stay" class="absolute inset-0 h-full w-full object-cover object-center" loading="lazy">
            <div class="absolute inset-0 bg-black/40"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-black/35 to-black/55"></div>

            <div class="relative mx-auto flex min-h-[380px] w-full max-w-5xl flex-col items-center justify-center px-6 py-16 text-center text-white sm:min-h-[460px] md:py-20">
                <h2 class="text-lg font-medium uppercase leading-snug drop-shadow-[0_2px_10px_rgba(0,0,0,0.75)] mb-3 sm:text-xl">
                    Plan Your Next Jungle Escape
                </h2>

                <p class="mt-2 max-w-2xl text-xs leading-relaxed text-white drop-shadow-[0_2px_8px_rgba(0,0,0,0.75)] sm:text-sm">
                    Book your next stay using your {{ $tierLabelMap[$memberTier] ?? 'Dana' }} member voucher, or explore our latest offers.
                </p>

                <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <a href="{{ $memberBookingUrl }}" target="_blank" rel="noopener" class="inline-flex min-w-[150px] items-center justify-center border border-[#A88444] bg-[#A88444] px-5 py-2.5 text-xs font-medium uppercase text-white shadow-lg shadow-black/20 transition hover:border-[#B8945B] hover:bg-[#B8945B] hover:text-white tracking-[0.08em] sm:text-sm">
                        Book Now
                    </a>

                    <a href="{{ route('offers.index') }}" class="inline-flex min-w-[150px] items-center justify-center border border-white/85 bg-black/25 px-5 py-2.5 text-xs font-medium uppercase text-white shadow-lg shadow-black/20 transition hover:border-[#A88444] hover:bg-[#A88444] hover:text-white tracking-[0.08em] sm:text-sm">
                        See Our Offers
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- RANDOM REWARDS SLIDER --}}
    @if ($dashboardRewards->isNotEmpty())
    <section class="dashboard-reward-carousel-section bg-white px-6 py-14 md:py-20" data-dashboard-reward-carousel-section>
        <div class="mx-auto w-full">
            <div class="mb-8 md:mb-10 text-center">
                <h2 class="text-lg leading-snug uppercase text-slate-700 font-medium mb-3 sm:text-xl">
                    Rewards
                </h2>

                <p class="mt-2 text-xs leading-relaxed text-gray-600 max-w-2xl sm:max-w-3xl md:max-w-5xl mx-auto sm:text-sm">
                    Explore selected rewards available for your Inner Circle points.
                </p>
            </div>

            <div class="relative">
                <div class="dashboard-reward-carousel" data-total="{{ $dashboardRewards->count() }}">
                    @foreach ($dashboardRewards as $reward)
                    @php
                    $rewardTitle = $reward->title ?? $reward->name ?? 'Reward';

                    $imageRaw = $reward->card_image
                    ?? $reward->hero_image
                    ?? $reward->image
                    ?? null;

                    $image = $resolveImage($imageRaw);

                    $alt = $reward->card_image_alt
                    ?? $reward->hero_image_alt
                    ?? $reward->image_alt
                    ?? $rewardTitle;

                    $rewardDescription = trim((string) (
                    $reward->description
                    ?? $reward->excerpt
                    ?? ''
                    ));

                    $points = (int) (
                    $reward->points_required
                    ?? $reward->points
                    ?? $reward->point_cost
                    ?? 0
                    );

                    $pointsLabel = trim((string) ($reward->points_label ?? ''));

                    $memberCanRedeem = $member
                    && $reward->is_active
                    && $points > 0
                    && (int) $member->points >= $points
                    && \Illuminate\Support\Facades\Route::has('membership.rewards.redeem');

                    $redeemPostUrl = \Illuminate\Support\Facades\Route::has('membership.rewards.redeem')
                    ? route('membership.rewards.redeem', $reward)
                    : '#';

                    $pointsNeeded = max($points - (int) ($member?->points ?? 0), 0);
                    @endphp

                    <article class="dashboard-reward-card-article px-3 w-full flex">
                        <div class="dashboard-reward-card group bg-white shadow-xl flex flex-col w-full">
                            <div class="aspect-[4/3] overflow-hidden bg-slate-100">
                                @if ($image)
                                <img src="{{ $image }}" alt="{{ $alt }}" class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" loading="lazy">
                                @else
                                <div class="flex h-full w-full items-center justify-center bg-[#F7F7F7]">
                                    <span class="text-4xl font-medium uppercase text-[#916B2C] sm:text-5xl">
                                        {{ strtoupper(mb_substr($rewardTitle, 0, 1)) }}
                                    </span>
                                </div>
                                @endif
                            </div>

                            <div class="dashboard-reward-card-body p-7 flex flex-col grow">
                                <h3 class="text-base text-slate-700 uppercase leading-snug font-medium mb-3 sm:text-lg">
                                    {{ $rewardTitle }}
                                </h3>

                                @if ($rewardDescription)
                                <p class="mt-2 text-xs leading-relaxed text-gray-600 sm:text-sm">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($rewardDescription), 145) }}
                                </p>
                                @endif

                                <div class="mt-auto pt-12">
                                    <div class="flex items-center justify-between gap-5">
                                        <p class="text-xs uppercase text-slate-950 sm:text-sm">
                                            @if ($pointsLabel)
                                            {{ $pointsLabel }}
                                            @else
                                            {{ $formatPointLabel($points) }}
                                            @endif
                                        </p>

                                        @if ($memberCanRedeem)
                                        <button type="button" data-reward-redeem-button data-redeem-action="{{ $redeemPostUrl }}" data-reward-title="{{ e($rewardTitle) }}" data-reward-points="{{ number_format((float) $points, 0) }}" class="inline-flex min-w-[115px] items-center justify-center border border-[#A88444] bg-[#A88444] px-4 py-2.5 text-xs uppercase text-white transition hover:border-[#B8945B] hover:bg-[#B8945B] tracking-[0.08em] font-medium sm:text-sm">
                                            Redeem
                                        </button>
                                        @else
                                        <button type="button" disabled class="inline-flex min-w-[115px] items-center justify-center border border-slate-300 bg-slate-100 px-4 py-2.5 text-xs uppercase text-slate-400 cursor-not-allowed tracking-[0.08em] font-medium sm:text-sm">
                                            Not Enough
                                        </button>
                                        @endif
                                    </div>

                                    @if (! $memberCanRedeem && $pointsNeeded > 0)
                                    <p class="mt-2 text-xs leading-relaxed text-slate-500 sm:text-sm">
                                        You need {{ number_format($pointsNeeded, 0) }} more {{ $pointsNeeded === 1 ? 'point' : 'points' }}.
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>

                <button type="button" class="dashboard-reward-carousel-prev absolute left-0 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center bg-[#A88444] text-white transition hover:bg-[#A88444] md:h-12 md:w-12 tracking-[0.08em] font-medium" aria-label="Previous reward">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"></path>
                    </svg>
                </button>

                <button type="button" class="dashboard-reward-carousel-next absolute right-0 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center bg-[#A88444] text-white transition hover:bg-[#A88444] md:h-12 md:w-12 tracking-[0.08em] font-medium" aria-label="Next reward">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"></path>
                    </svg>
                </button>
            </div>

            <div class="mt-8 text-center">
                <a href="{{ \Illuminate\Support\Facades\Route::has('membership.privilege-redemption') ? route('membership.privilege-redemption') : '#' }}" class="inline-flex min-w-[145px] items-center justify-center border border-[#A88444] bg-[#A88444] px-5 py-2.5 text-xs uppercase text-white transition hover:border-[#B8945B] hover:bg-[#B8945B] tracking-[0.08em] font-medium sm:text-sm">
                    View More
                </a>
            </div>
        </div>
    </section>
    @endif

    <script>
        function loadMemberCardImage(src) {
            return new Promise(function (resolve, reject) {
                const image = new Image();
                image.onload = function () {
                    resolve(image);
                };
                image.onerror = reject;
                image.src = src;
            });
        }

        function fitMemberCardFontSize(ctx, text, maxWidth, initialSize, fontWeight) {
            let size = initialSize;

            while (size > 10) {
                ctx.font = fontWeight + ' ' + size + 'px Arial, sans-serif';

                if (ctx.measureText(text).width <= maxWidth) {
                    return size;
                }

                size -= 1;
            }

            return size;
        }

        function drawMemberCardText(ctx, text, x, y, maxWidth, fontSize, fontWeight, align) {
            const fittedSize = fitMemberCardFontSize(ctx, text, maxWidth, fontSize, fontWeight);

            ctx.save();
            ctx.font = fontWeight + ' ' + fittedSize + 'px Arial, sans-serif';
            ctx.fillStyle = '#ffffff';
            ctx.textAlign = align;
            ctx.textBaseline = 'alphabetic';
            ctx.shadowColor = 'rgba(0, 0, 0, 0.45)';
            ctx.shadowBlur = Math.max(2, Math.round(fittedSize * 0.12));
            ctx.shadowOffsetX = 0;
            ctx.shadowOffsetY = Math.max(1, Math.round(fittedSize * 0.08));
            ctx.fillText(text, x, y, maxWidth);
            ctx.restore();
        }

        function getMemberCardWrappedLines(ctx, text, maxWidth) {
            const words = text.split(/\s+/).filter(Boolean);
            const lines = [];
            let currentLine = '';

            words.forEach(function (word) {
                const testLine = currentLine ? currentLine + ' ' + word : word;

                if (ctx.measureText(testLine).width <= maxWidth || currentLine === '') {
                    currentLine = testLine;
                    return;
                }

                lines.push(currentLine);
                currentLine = word;
            });

            if (currentLine) {
                lines.push(currentLine);
            }

            return lines;
        }

        function fitMemberCardWrappedFontSize(ctx, text, maxWidth, maxHeight, initialSize, fontWeight, lineHeightRatio) {
            let size = initialSize;

            while (size > 10) {
                ctx.font = fontWeight + ' ' + size + 'px Arial, sans-serif';

                const lines = getMemberCardWrappedLines(ctx, text, maxWidth);
                const lineHeight = size * lineHeightRatio;

                if (lines.length * lineHeight <= maxHeight) {
                    return { size, lines, lineHeight };
                }

                size -= 1;
            }

            ctx.font = fontWeight + ' ' + size + 'px Arial, sans-serif';

            return {
                size,
                lines: getMemberCardWrappedLines(ctx, text, maxWidth),
                lineHeight: size * lineHeightRatio,
            };
        }

        function drawMemberCardWrappedText(ctx, text, x, y, maxWidth, maxHeight, fontSize, fontWeight, align) {
            const fitted = fitMemberCardWrappedFontSize(ctx, text, maxWidth, maxHeight, fontSize, fontWeight, 1.2);

            ctx.save();
            ctx.font = fontWeight + ' ' + fitted.size + 'px Arial, sans-serif';
            ctx.fillStyle = '#ffffff';
            ctx.textAlign = align;
            ctx.textBaseline = 'top';
            ctx.shadowColor = 'rgba(0, 0, 0, 0.45)';
            ctx.shadowBlur = Math.max(2, Math.round(fitted.size * 0.12));
            ctx.shadowOffsetX = 0;
            ctx.shadowOffsetY = Math.max(1, Math.round(fitted.size * 0.08));

            fitted.lines.forEach(function (line, index) {
                ctx.fillText(line, x, y + (index * fitted.lineHeight), maxWidth);
            });

            ctx.restore();
        }

        function initMemberCardModal() {
            const openButton = document.querySelector('[data-member-card-open]');
            const modal = document.querySelector('[data-member-card-modal]');

            if (!openButton || !modal || modal.dataset.initialized === 'true') {
                return;
            }

            const closeButtons = modal.querySelectorAll('[data-member-card-close]');

            function openModal() {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                modal.setAttribute('aria-hidden', 'false');
                modal.removeAttribute('inert');
                document.body.classList.add('overflow-hidden');

                const downloadButton = modal.querySelector('[data-member-card-download]');

                if (downloadButton) {
                    downloadButton.focus({ preventScroll: true });
                }
            }

            function closeModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                modal.setAttribute('aria-hidden', 'true');
                modal.setAttribute('inert', '');
                document.body.classList.remove('overflow-hidden');
                openButton.focus({ preventScroll: true });
            }

            modal.dataset.initialized = 'true';
            openButton.addEventListener('click', openModal);

            closeButtons.forEach(function (button) {
                button.addEventListener('click', closeModal);
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });
        }

        function initMemberCardDownload() {
            const button = document.querySelector('[data-member-card-download]');

            if (!button || button.dataset.initialized === 'true') {
                return;
            }

            button.dataset.initialized = 'true';

            button.addEventListener('click', async function () {
                const originalLabel = button.getAttribute('aria-label') || 'Download membership card';

                try {
                    button.disabled = true;
                    button.setAttribute('aria-label', 'Preparing membership card download');

                    const image = await loadMemberCardImage(button.dataset.cardImage);
                    const width = image.naturalWidth || 780;
                    const height = image.naturalHeight || Math.round(width * 9 / 16);
                    const scale = width / 640;
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');

                    canvas.width = width;
                    canvas.height = height;
                    ctx.drawImage(image, 0, 0, width, height);

                    drawMemberCardText(
                        ctx,
                        (button.dataset.memberTier || '').toUpperCase(),
                        width * 0.92,
                        height * 0.745,
                        width * 0.28,
                        Math.round(24 * scale),
                        '700',
                        'right'
                    );

                    drawMemberCardText(
                        ctx,
                        (button.dataset.memberPoints || '').toUpperCase(),
                        width * 0.92,
                        height * 0.805,
                        width * 0.28,
                        Math.round(18 * scale),
                        '700',
                        'right'
                    );

                    drawMemberCardWrappedText(
                        ctx,
                        (button.dataset.memberName || '').toUpperCase(),
                        width * 0.92,
                        height * 0.42,
                        width * 0.64,
                        height * 0.2,
                        Math.round(32 * scale),
                        '700',
                        'right'
                    );

                    drawMemberCardText(
                        ctx,
                        'VALID TILL',
                        width * 0.92,
                        height * 0.865,
                        width * 0.22,
                        Math.round(12 * scale),
                        '700',
                        'right'
                    );

                    drawMemberCardText(
                        ctx,
                        button.dataset.validUntil || '-',
                        width * 0.92,
                        height * 0.905,
                        width * 0.22,
                        Math.round(12 * scale),
                        '700',
                        'right'
                    );

                    await new Promise(function (resolve, reject) {
                        canvas.toBlob(function (blob) {
                            if (!blob) {
                                reject(new Error('Unable to create membership card image.'));
                                return;
                            }

                            const url = URL.createObjectURL(blob);
                            const link = document.createElement('a');

                            link.href = url;
                            link.download = button.dataset.downloadName || 'membership-card.png';
                            document.body.appendChild(link);
                            link.click();
                            link.remove();

                            setTimeout(function () {
                                URL.revokeObjectURL(url);
                            }, 1000);

                            resolve();
                        }, 'image/png');
                    });
                } catch (error) {
                    console.error(error);
                    alert('Unable to download the membership card. Please try again.');
                } finally {
                    button.disabled = false;
                    button.setAttribute('aria-label', originalLabel);
                }
            });
        }

        function initDashboardRewardCarousel(attempt = 0) {
            if (!window.jQuery || !jQuery.fn || !jQuery.fn.slick) {
                if (attempt < 30) {
                    setTimeout(function () {
                        initDashboardRewardCarousel(attempt + 1);
                    }, 150);
                }

                return;
            }

            const $carousel = jQuery('.dashboard-reward-carousel');

            if (!$carousel.length || $carousel.hasClass('slick-initialized')) {
                return;
            }

            const total = parseInt($carousel.attr('data-total') || '0', 10);

            if (total <= 0) {
                return;
            }

            const desktopSlides = Math.min(total, 3);
            const tabletSlides = Math.min(total, 2);
            const mobileSlides = 1;

            $carousel.slick({
                slidesToShow: desktopSlides,
                slidesToScroll: 1,
                arrows: total > 1,
                infinite: total > desktopSlides,
                prevArrow: jQuery('.dashboard-reward-carousel-prev'),
                nextArrow: jQuery('.dashboard-reward-carousel-next'),
                responsive: [
                    {
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: tabletSlides,
                            infinite: total > tabletSlides,
                        },
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: mobileSlides,
                            infinite: total > mobileSlides,
                        },
                    },
                ],
            });

            if (total <= 1) {
                jQuery('.dashboard-reward-carousel-prev, .dashboard-reward-carousel-next').addClass('hidden');
            }
        }

        function initDashboardAccommodationCarousel(attempt = 0) {
            if (!window.jQuery || !jQuery.fn || !jQuery.fn.slick) {
                if (attempt < 30) {
                    setTimeout(function () {
                        initDashboardAccommodationCarousel(attempt + 1);
                    }, 150);
                }

                return;
            }

            const $carousel = jQuery('.dashboard-accommodation-carousel');

            if (!$carousel.length || $carousel.hasClass('slick-initialized')) {
                return;
            }

            const total = parseInt($carousel.attr('data-total') || '0', 10);

            if (total <= 0) {
                return;
            }

            const desktopSlides = Math.min(total, 3);
            const tabletSlides = Math.min(total, 2);
            const mobileSlides = 1;

            $carousel.slick({
                slidesToShow: desktopSlides,
                slidesToScroll: 1,
                arrows: total > 1,
                infinite: total > desktopSlides,
                prevArrow: jQuery('.dashboard-accommodation-carousel-prev'),
                nextArrow: jQuery('.dashboard-accommodation-carousel-next'),
                responsive: [
                    {
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: tabletSlides,
                            infinite: total > tabletSlides,
                        },
                    },
                    {
                        breakpoint: 640,
                        settings: {
                            slidesToShow: mobileSlides,
                            infinite: total > mobileSlides,
                        },
                    },
                ],
            });

            if (total <= 1) {
                jQuery('.dashboard-accommodation-carousel-prev, .dashboard-accommodation-carousel-next').addClass('hidden');
            }
        }

        function initHistoryViewMore() {
            const button = document.querySelector('[data-history-view-more]');

            if (!button || button.dataset.initialized === 'true') {
                return;
            }

            button.dataset.initialized = 'true';

            button.addEventListener('click', function () {
                document.querySelectorAll('[data-history-extra]').forEach(function (item) {
                    item.classList.remove('hidden');
                });

                button.classList.add('hidden');
            });
        }

        function initMembershipDashboardTabs() {
            const tabs = Array.from(document.querySelectorAll('[data-dashboard-tab]'));
            const panels = Array.from(document.querySelectorAll('[data-dashboard-tab-panel]'));

            if (!tabs.length || !panels.length) {
                return;
            }

            function activate(tabName) {
                tabs.forEach(function (tab) {
                    const isActive = tab.dataset.dashboardTab === tabName;

                    tab.classList.toggle('bg-[#A88444]', isActive);
                    tab.classList.toggle('border-[#A88444]', isActive);
                    tab.classList.toggle('text-white', isActive);
                    tab.classList.toggle('bg-white', !isActive);
                    tab.classList.toggle('border-slate-300', !isActive);
                    tab.classList.toggle('text-slate-700', !isActive);
                    tab.classList.toggle('hover:text-white', isActive);
                    tab.classList.toggle('hover:text-[#A88444]', !isActive);
                });

                panels.forEach(function (panel) {
                    panel.classList.toggle('hidden', panel.dataset.dashboardTabPanel !== tabName);
                });

            }

            tabs.forEach(function (tab) {
                if (tab.dataset.initialized === 'true') {
                    return;
                }

                tab.dataset.initialized = 'true';
                tab.addEventListener('click', function () {
                    activate(tab.dataset.dashboardTab);
                });
            });

            activate('bookings');
        }

        document.addEventListener('DOMContentLoaded', function () {
            initDashboardRewardCarousel();
            initDashboardAccommodationCarousel();
            initHistoryViewMore();
            initMemberCardModal();
            initMemberCardDownload();
            initMembershipDashboardTabs();
        });

        window.addEventListener('load', function () {
            initDashboardRewardCarousel();
            initDashboardAccommodationCarousel();
            initHistoryViewMore();
            initMemberCardModal();
            initMemberCardDownload();
            initMembershipDashboardTabs();
        });
    </script>
</x-layouts.app>

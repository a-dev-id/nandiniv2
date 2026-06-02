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

$memberTier = strtolower((string) ($member?->tier ?? \App\Models\Member::getTierByPoints($memberPoints)));

$validTiers = ['bronze', 'silver', 'gold', 'platinum'];

if (! in_array($memberTier, $validTiers, true)) {
$memberTier = 'bronze';
}

$cardMap = [
'bronze' => asset('images/membership/dana-blank.jpg'),
'silver' => asset('images/membership/upaya-blank.jpg'),
'gold' => asset('images/membership/dhyana-blank.jpg'),
'platinum' => asset('images/membership/jnana-blank.jpg'),
];

$tierNameMap = [
'bronze' => 'Dana',
'silver' => 'Upaya',
'gold' => 'Dhyana',
'platinum' => 'Jnana',
];

$tierLabelMap = [
'bronze' => 'Bronze',
'silver' => 'Silver',
'gold' => 'Gold',
'platinum' => 'Platinum',
];

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

try {
if ($activityHistories->isEmpty() && $member) {
$mergedHistories = collect();

if (method_exists($member, 'rewardRedemptions')) {
$rewardRedemptions = $member->rewardRedemptions()
->with('reward')
->latest()
->take(30)
->get();

$mergedHistories = $mergedHistories->merge($rewardRedemptions);
}

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
return $item->created_at
? \Carbon\Carbon::parse($item->created_at)->timestamp
: 0;
} catch (\Throwable $e) {
return 0;
}
})
->take(30)
->values();
}
} catch (\Throwable $e) {
$activityHistories = collect();
}

if ($activityHistories->isEmpty() && app()->environment('local')) {
$activityHistories = collect([
[
'title' => 'Manual Point Top Up',
'description' => 'Manual point top up for testing.',
'status' => 'earned',
'date' => now(),
'points' => 500,
'image' => null,
],
[
'title' => 'Balinese Blessing Purification',
'description' => "Surrender to the embrace of Bali's healing waters with a sacred purification ritual led by a Balinese priest.",
'status' => 'redeemed',
'date' => '2026-01-27',
'points' => -400,
'image' => null,
'redemption_code' => 'RDM-TEST-CODE',
'expires_at' => '2026-02-26',
],
]);
}

$historyDisplayLimit = 3;
$hasMoreHistories = $activityHistories->count() > $historyDisplayLimit;

$dashboardRewards = collect($rewards ?? []);

if ($dashboardRewards->isEmpty()) {
try {
$dashboardRewards = \App\Models\Reward::query()
->with('category')
->where('is_active', true)
->inRandomOrder()
->limit(9)
->get();
} catch (\Throwable $e) {
$dashboardRewards = collect();
}
} else {
$dashboardRewards = $dashboardRewards->shuffle()->take(9)->values();
}
@endphp

<x-layouts.app>
    <x-heroes.image-hero :page="$page" />

    <style>
        .dashboard-reward-carousel-section .slick-list {
            padding-top: 4px !important;
            padding-bottom: 46px !important;
        }

        .dashboard-reward-carousel-section .slick-track {
            display: flex !important;
        }

        .dashboard-reward-carousel-section .slick-slide {
            height: auto !important;
        }

        .dashboard-reward-carousel-section .slick-slide>div {
            height: 100%;
        }

        .dashboard-reward-carousel-section .dashboard-reward-card-article {
            height: 100%;
            padding-bottom: 8px;
        }

        .dashboard-reward-carousel-section .dashboard-reward-card {
            height: 100%;
            min-height: 540px;
        }

        .dashboard-reward-carousel-section .dashboard-reward-card-body {
            min-height: 285px;
        }

        @media (max-width: 767px) {
            .dashboard-reward-carousel-section .dashboard-reward-card {
                min-height: auto;
            }

            .dashboard-reward-carousel-section .dashboard-reward-card-body {
                min-height: auto;
            }
        }

    </style>

    {{-- MEMBER DETAIL --}}
    <section class="bg-white px-6 py-14 md:py-20">
        <div class="mx-auto w-full max-w-6xl">

            <div class="mx-auto max-w-3xl text-center">
                <h1 class="text-4xl leading-snug tracking-[0.15em] md:tracking-[0.25em] uppercase text-slate-800 mb-6 md:mb-8 font-medium">
                    Member Detail
                </h1>

                <p class="mt-3 text-[15px] sm:text-base leading-relaxed text-gray-600 max-w-2xl sm:max-w-3xl md:max-w-5xl mx-auto">
                    Access your exclusive member privileges.
                </p>
            </div>

            <div class="mt-8 md:mt-10 grid grid-cols-1 items-center gap-8 lg:grid-cols-12 lg:gap-8">

                <div class="lg:col-span-3">
                    <div class="mx-auto flex aspect-[4/5] w-full max-w-[170px] items-center justify-center overflow-hidden rounded-[16px] bg-[#F7F7F7] shadow-md lg:mx-0">
                        @if ($profilePhotoUrl)
                        <img src="{{ $profilePhotoUrl }}" alt="{{ $memberName }}" class="h-full w-full object-cover" loading="lazy">
                        @else
                        <span class="text-5xl font-medium uppercase tracking-[0.08em] text-[#A67C3D]">
                            {{ $memberInitial }}
                        </span>
                        @endif
                    </div>
                </div>

                <div class="text-center lg:col-span-4 lg:text-left">
                    <h2 class="text-2xl sm:text-3xl md:text-3xl leading-snug tracking-[0.15em] md:tracking-[0.20em] uppercase text-slate-800 font-medium">
                        {{ $memberName }}
                    </h2>

                    <div class="mt-5 mx-auto max-w-[340px] space-y-2 text-[13px] sm:text-[14px] leading-6 text-gray-600 lg:mx-0">
                        <div class="grid grid-cols-[88px_1fr] gap-1 text-left">
                            <span>Email</span>
                            <span class="break-all">: {{ $memberEmail }}</span>
                        </div>

                        <div class="grid grid-cols-[88px_1fr] gap-1 text-left">
                            <span>Create</span>
                            <span>: {{ $createdDate }}</span>
                        </div>

                        <div class="grid grid-cols-[88px_1fr] gap-1 text-left">
                            <span>Location</span>
                            <span>: {{ $location }}</span>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-center lg:justify-start">
                        <a href="{{ \Illuminate\Support\Facades\Route::has('membership.profile.edit') ? route('membership.profile.edit') : '#' }}" class="inline-flex min-w-[170px] items-center justify-center border border-[#916B2C] bg-[#916B2C] px-6 py-3 text-xs uppercase tracking-[0.16em] text-white hover:bg-white hover:text-[#916B2C] transition">
                            Edit Profile
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="mx-auto w-full max-w-[390px]">

                        <div class="relative overflow-hidden rounded-[20px] shadow-lg">
                            <img src="{{ $currentCardImage }}" alt="{{ $tierNameMap[$memberTier] ?? 'Membership Card' }} {{ $tierLabelMap[$memberTier] ?? '' }} Card" class="block w-full" loading="lazy">

                            <div class="pointer-events-none absolute inset-0 text-white">

                                <div class="absolute bottom-[14%] left-[8%] max-w-[43%]">
                                    <p class="text-[14px] sm:text-[17px] md:text-[19px] font-bold uppercase leading-none tracking-[0.02em] drop-shadow-md">
                                        {{ number_format($memberPoints) }} POINT
                                    </p>
                                </div>

                                <div class="absolute bottom-[23%] right-[8%] max-w-[48%] text-right">
                                    <p class="text-[16px] sm:text-[19px] md:text-[22px] font-semibold uppercase leading-none tracking-[0.14em] drop-shadow-md">
                                        {{ $tierLabelMap[$memberTier] ?? 'Bronze' }}
                                    </p>
                                </div>

                                <div class="absolute bottom-[8.5%] right-[8%] text-center">
                                    <p class="text-[5.5px] sm:text-[6.5px] md:text-[7.5px] font-bold uppercase leading-tight tracking-[0.18em] text-white/95 drop-shadow">
                                        Valid Till
                                    </p>

                                    <p class="mt-0.5 text-[5.5px] sm:text-[6.5px] md:text-[7.5px] font-bold uppercase leading-tight tracking-[0.14em] text-white/95 drop-shadow">
                                        {{ $validUntilDate }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-1 items-center gap-3 sm:grid-cols-[1fr_110px]">
                            @if ($nextTier && $pointsToNextTier !== null)
                            <div class="rounded-lg bg-[#F1F1F1] px-5 py-3 text-center">
                                <p class="text-[12px] sm:text-[13px] uppercase leading-snug tracking-[0.12em] text-slate-900">
                                    Another <span class="font-bold">{{ number_format($pointsToNextTier) }} Points</span>
                                </p>

                                <p class="mt-1 text-[8px] sm:text-[9px] uppercase tracking-[0.16em] text-slate-700">
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
                                <p class="text-[12px] sm:text-[13px] uppercase leading-snug tracking-[0.12em] text-slate-900">
                                    You are now a <span class="font-bold">Platinum Member</span>
                                </p>

                                <p class="mt-1 text-[8px] sm:text-[9px] uppercase tracking-[0.16em] text-slate-700">
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

    {{-- ACTIVITY HISTORY --}}
    <section class="bg-[#F1F1F1] px-6 py-14 md:py-20">
        <div class="mx-auto w-full max-w-7xl">

            <div class="mb-10 text-center">
                <h2 class="text-2xl sm:text-3xl md:text-3xl leading-snug tracking-[0.15em] md:tracking-[0.22em] uppercase text-slate-800 font-medium">
                    History
                </h2>

                <p class="mt-3 text-[15px] sm:text-base leading-relaxed text-gray-600 max-w-2xl sm:max-w-3xl md:max-w-5xl mx-auto">
                    Your member activity history.
                </p>
            </div>

            <div class="divide-y divide-black/45 border-t border-b border-black/45">
                @foreach ($activityHistories as $historyIndex => $activity)
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

                $title = $title ?: 'Membership Activity';

                $description = $getHistoryValue($activity, [
                'description',
                'excerpt',
                'reward.description',
                'reward.excerpt',
                'offer.description',
                'offer.excerpt',
                'notes',
                'remark',
                ], '');

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
                $hasValidUntil = $validUntil !== '-';

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
                $statusRaw = $pointsNumber < 0 ? 'redeemed' : 'earned' ; } if ($statusRaw==='earn' ) { $statusRaw='earned' ; } if ($isRewardRedemption && $statusRaw==='pending' ) { $statusRaw='redeemed' ; } $statusLabel=strtoupper(str_replace(['_', '-' ], ' ' , $statusRaw)); $statusClass=match ($statusRaw) { 'pending' , 'redeemed' , 'redeem' , 'used' , 'deducted'=> 'bg-[#FF3B3B] text-white',
                    'earned', 'earn', 'purchased', 'purchase', 'paid', 'completed' => 'bg-[#32C85A] text-white',
                    'adjustment' => 'bg-[#916B2C] text-white',
                    default => 'bg-[#916B2C] text-white',
                    };

                    $date = $formatHistoryDate($getHistoryValue($activity, [
                    'date',
                    'activity_date',
                    'transaction_date',
                    'redeemed_at',
                    'used_at',
                    'purchased_at',
                    'created_at',
                    ], null));

                    $pointDisplay = ($pointsNumber > 0 ? '+' : ($pointsNumber < 0 ? '-' : '' )) . number_format(abs($pointsNumber)) . ' POINT' ; $image=$resolveImage($getHistoryValue($activity, [ 'image' , 'photo' , 'thumbnail' , 'card_image' , 'hero_image' , 'reward.image' , 'reward.photo' , 'reward.thumbnail' , 'reward.card_image' , 'reward.hero_image' , 'offer.image' , 'offer.photo' , 'offer.thumbnail' , 'offer.card_image' , 'offer.hero_image' , ], null)); @endphp <article class="grid grid-cols-1 gap-5 py-7 md:grid-cols-[170px_1fr] md:gap-12 md:py-8 {{ $historyIndex >= $historyDisplayLimit ? 'hidden' : '' }}" @if ($historyIndex>= $historyDisplayLimit)
                        data-history-extra
                        @endif
                        >
                        <div class="w-full">
                            @if ($image)
                            <img src="{{ $image }}" alt="{{ $title }}" class="aspect-square w-full max-w-[170px] object-cover" loading="lazy">
                            @else
                            <div class="flex aspect-square w-full max-w-[170px] items-center justify-center bg-white">
                                <span class="text-4xl font-medium uppercase tracking-[0.12em] text-[#916B2C]">
                                    {{ strtoupper(mb_substr($title, 0, 1)) }}
                                </span>
                            </div>
                            @endif
                        </div>

                        <div class="flex min-w-0 flex-col justify-center">
                            <h3 class="text-xl sm:text-2xl md:text-2xl leading-snug tracking-[0.15em] uppercase text-slate-800 font-medium">
                                {{ $title }}
                            </h3>

                            @if ($description)
                            <p class="mt-3 text-[15px] sm:text-base leading-relaxed text-gray-600 max-w-2xl sm:max-w-3xl md:max-w-5xl">
                                {{ \Illuminate\Support\Str::limit(strip_tags($description), 180) }}
                            </p>
                            @endif

                            @if ($redemptionCode || $hasValidUntil)
                            <div class="mt-4 flex flex-wrap gap-3">
                                @if ($redemptionCode)
                                <div class="inline-flex w-fit border border-black/40 bg-white px-4 py-2">
                                    <p class="text-[12px] sm:text-[13px] font-bold uppercase leading-none tracking-[0.14em] text-slate-800">
                                        Code: {{ $redemptionCode }}
                                    </p>
                                </div>
                                @endif

                                @if ($hasValidUntil)
                                <div class="inline-flex w-fit border border-[#916B2C]/60 bg-white px-4 py-2">
                                    <p class="text-[12px] sm:text-[13px] font-bold uppercase leading-none tracking-[0.14em] text-[#916B2C]">
                                        Valid Until: {{ $validUntil }}
                                    </p>
                                </div>
                                @endif
                            </div>
                            @endif

                            <div class="mt-7 grid grid-cols-1 items-center gap-4 text-[14px] sm:grid-cols-[150px_160px_1fr] md:mt-9">
                                <div>
                                    <span class="{{ $statusClass }} inline-flex min-w-[138px] items-center justify-center rounded-full px-5 py-2 text-[13px] font-bold leading-none tracking-[0.16em]">
                                        {{ $statusLabel }}
                                    </span>
                                </div>

                                <p class="text-[15px] font-bold uppercase leading-none tracking-[0.18em] text-slate-800">
                                    {{ $date }}
                                </p>

                                <p class="text-[15px] font-bold uppercase leading-none tracking-[0.18em] text-slate-800">
                                    {{ $pointDisplay }}
                                </p>
                            </div>
                        </div>
                        </article>
                        @endforeach
            </div>

            @if ($hasMoreHistories)
            <div class="mt-10 text-center">
                <button type="button" data-history-view-more class="inline-flex min-w-[170px] items-center justify-center border border-[#916B2C] bg-[#916B2C] px-7 py-4 text-sm uppercase tracking-[0.14em] text-white hover:bg-white hover:text-[#916B2C] transition">
                    View More
                </button>
            </div>
            @endif
        </div>
    </section>

    {{-- RANDOM REWARDS SLIDER --}}
    @if ($dashboardRewards->isNotEmpty())
    <section class="dashboard-reward-carousel-section bg-white px-6 py-14 md:py-20" data-dashboard-reward-carousel-section>
        <div class="mx-auto w-full">
            <div class="mb-8 md:mb-10 text-center">
                <h2 class="text-2xl sm:text-3xl md:text-3xl leading-snug tracking-[0.15em] md:tracking-[0.22em] uppercase text-slate-800 font-medium">
                    Rewards
                </h2>

                <p class="mt-3 text-[15px] sm:text-base leading-relaxed text-gray-600 max-w-2xl sm:max-w-3xl md:max-w-5xl mx-auto">
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
                                    <span class="text-5xl font-medium uppercase tracking-[0.12em] text-[#916B2C]">
                                        {{ strtoupper(mb_substr($rewardTitle, 0, 1)) }}
                                    </span>
                                </div>
                                @endif
                            </div>

                            <div class="dashboard-reward-card-body p-7 flex flex-col grow">
                                <h3 class="text-slate-800 uppercase tracking-[0.15em] text-xl sm:text-2xl md:text-2xl leading-snug font-medium">
                                    {{ $rewardTitle }}
                                </h3>

                                @if ($rewardDescription)
                                <p class="mt-5 text-[15px] sm:text-base leading-relaxed text-gray-600">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($rewardDescription), 145) }}
                                </p>
                                @endif

                                <div class="mt-auto pt-12">
                                    <div class="flex items-center justify-between gap-5">
                                        <p class="text-sm uppercase text-slate-950">
                                            @if ($pointsLabel)
                                            {{ $pointsLabel }}
                                            @else
                                            {{ number_format((float) $points, 0) }} Points
                                            @endif
                                        </p>

                                        @if ($memberCanRedeem)
                                        <form method="POST" action="{{ $redeemPostUrl }}">
                                            @csrf

                                            <button type="submit" class="inline-flex min-w-[125px] items-center justify-center border border-slate-950 px-6 py-3 text-sm uppercase text-slate-950 hover:bg-slate-950 hover:text-white transition">
                                                Redeem
                                            </button>
                                        </form>
                                        @else
                                        <button type="button" disabled class="inline-flex min-w-[125px] items-center justify-center border border-slate-300 bg-slate-100 px-6 py-3 text-sm uppercase text-slate-400 cursor-not-allowed">
                                            Not Enough
                                        </button>
                                        @endif
                                    </div>

                                    @if (! $memberCanRedeem && $pointsNeeded > 0)
                                    <p class="mt-3 text-xs leading-relaxed text-slate-500">
                                        You need {{ number_format($pointsNeeded, 0) }} more points.
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>

                <button type="button" class="dashboard-reward-carousel-prev absolute left-0 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center bg-black text-white md:h-12 md:w-12" aria-label="Previous reward">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"></path>
                    </svg>
                </button>

                <button type="button" class="dashboard-reward-carousel-next absolute right-0 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center bg-black text-white md:h-12 md:w-12" aria-label="Next reward">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"></path>
                    </svg>
                </button>
            </div>
        </div>
    </section>
    @endif

    <script>
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

        document.addEventListener('DOMContentLoaded', function () {
            initDashboardRewardCarousel();
            initHistoryViewMore();
        });

        window.addEventListener('load', function () {
            initDashboardRewardCarousel();
            initHistoryViewMore();
        });
    </script>
</x-layouts.app>

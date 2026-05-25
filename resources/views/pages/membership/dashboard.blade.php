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

$memberId = str_pad((string) ($member?->id ?? 0), 8, '0', STR_PAD_LEFT);

$createdDate = $member?->created_at
? $member->created_at->format('d F Y')
: '-';

$validUntilDate = $member?->membership_expires_at
? $member->membership_expires_at->format('Y/m/d')
: '-';

$location = $member?->country ?: '-';

$memberPoints = (int) ($member?->points ?? 0);

/*
|--------------------------------------------------------------------------
| Important:
| Tier is calculated directly from points.
| This makes testing easier because changing points will update the card tier.
|--------------------------------------------------------------------------
*/
$memberTier = strtolower((string) \App\Models\Member::getTierByPoints($memberPoints));

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

/*
|--------------------------------------------------------------------------
| Activity History
|--------------------------------------------------------------------------
| This will use controller variables if available:
| $activityHistories, $membershipHistories, $pointHistories, or $rewardHistories.
|
| If none exist, it will show demo rows so the section layout is visible.
|--------------------------------------------------------------------------
*/
$activityHistories = collect(
$activityHistories
?? $membershipHistories
?? $pointHistories
?? $rewardHistories
?? []
);

try {
if ($activityHistories->isEmpty() && $member && method_exists($member, 'pointTransactions')) {
$activityHistories = $member->pointTransactions()->latest()->take(10)->get();
}

if ($activityHistories->isEmpty() && $member && method_exists($member, 'rewardTransactions')) {
$activityHistories = $member->rewardTransactions()->latest()->take(10)->get();
}

if ($activityHistories->isEmpty() && $member && method_exists($member, 'rewardRedemptions')) {
$activityHistories = $member->rewardRedemptions()->latest()->take(10)->get();
}
} catch (\Throwable $e) {
$activityHistories = collect();
}

if ($activityHistories->isEmpty()) {
$activityHistories = collect([
[
'title' => 'Balinese Blessing Purification',
'description' => "Surrender to the embrace of Bali's healing waters with a sacred purification ritual led by a Balinese priest.",
'status' => 'redeemed',
'date' => '2026-01-27',
'points' => -400,
'image' => null,
],
[
'title' => 'Stay Longer Save More',
'description' => 'Stay a minimum of 5 nights and enjoy exclusive savings designed for extended escapes.',
'status' => 'purchased',
'date' => '2026-01-18',
'points' => 400,
'image' => null,
],
]);
}
@endphp

<x-layouts.app>
    <x-heroes.image-hero :page="$page" />

    {{-- MEMBER DETAIL --}}
    <section class="bg-white px-6 py-10 md:py-14 lg:py-16">
        <div class="mx-auto w-full max-w-6xl">

            {{-- SECTION TITLE --}}
            <div class="mx-auto max-w-3xl text-center">
                <h1 class="text-2xl sm:text-3xl md:text-4xl leading-snug tracking-[0.14em] md:tracking-[0.20em] uppercase text-slate-800 font-medium">
                    Member Detail
                </h1>

                <p class="mt-3 text-[14px] sm:text-[15px] leading-relaxed text-gray-600">
                    Access your exclusive member privileges.
                </p>
            </div>

            {{-- CONTENT --}}
            <div class="mt-8 md:mt-10 grid grid-cols-1 items-center gap-8 lg:grid-cols-12 lg:gap-8">

                {{-- PROFILE PHOTO --}}
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

                {{-- MEMBER INFO --}}
                <div class="text-center lg:col-span-4 lg:text-left">
                    <h2 class="text-lg sm:text-xl lg:text-[22px] leading-snug tracking-[0.20em] uppercase text-slate-800 font-medium">
                        {{ $memberName }}
                    </h2>

                    <div class="mt-5 mx-auto max-w-[340px] space-y-2 text-[13px] sm:text-[14px] leading-6 text-slate-700 lg:mx-0">
                        <div class="grid grid-cols-[88px_1fr] gap-1 text-left">
                            <span>Member ID</span>
                            <span>: {{ $memberId }}</span>
                        </div>

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
                </div>

                {{-- MEMBER CARD --}}
                <div class="lg:col-span-5">
                    <div class="mx-auto w-full max-w-[390px]">

                        {{-- CURRENT CARD --}}
                        <div class="relative overflow-hidden rounded-[20px] shadow-lg">
                            <img src="{{ $currentCardImage }}" alt="{{ $tierNameMap[$memberTier] ?? 'Membership Card' }} {{ $tierLabelMap[$memberTier] ?? '' }} Card" class="block w-full" loading="lazy">

                            <div class="pointer-events-none absolute inset-0 text-white">

                                {{-- POINTS --}}
                                <div class="absolute bottom-[14%] left-[8%] max-w-[43%]">
                                    <p class="text-[14px] sm:text-[17px] md:text-[19px] font-bold uppercase leading-none tracking-[0.02em] drop-shadow-md">
                                        {{ number_format($memberPoints) }} POINT
                                    </p>
                                </div>

                                {{-- TIER --}}
                                <div class="absolute bottom-[23%] right-[8%] max-w-[48%] text-right">
                                    <p class="text-[16px] sm:text-[19px] md:text-[22px] font-semibold uppercase leading-none tracking-[0.14em] drop-shadow-md">
                                        {{ $tierLabelMap[$memberTier] ?? 'Bronze' }}
                                    </p>
                                </div>

                                {{-- MEMBER ID --}}
                                <div class="absolute bottom-[8.5%] right-[26%] text-center">
                                    <p class="text-[5.5px] sm:text-[6.5px] md:text-[7.5px] font-bold uppercase leading-tight tracking-[0.18em] text-white/95 drop-shadow">
                                        Member ID
                                    </p>

                                    <p class="mt-0.5 text-[5.5px] sm:text-[6.5px] md:text-[7.5px] font-bold uppercase leading-tight tracking-[0.14em] text-white/95 drop-shadow">
                                        {{ $memberId }}
                                    </p>
                                </div>

                                {{-- VALID TILL --}}
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

                        {{-- NEXT TIER --}}
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
    <section class="bg-[#F1F1F1] px-6 py-10 md:py-12 lg:py-14">
        <div class="mx-auto w-full max-w-5xl">

            {{-- HISTORY HEADER --}}
            <div class="mb-10 text-center">
                <h2 class="text-3xl sm:text-4xl md:text-[44px] leading-none tracking-[0.18em] uppercase text-black font-medium">
                    History
                </h2>

                <p class="mt-3 text-[14px] sm:text-[15px] leading-relaxed text-black">
                    Your member activity history.
                </p>
            </div>

            <div class="divide-y divide-black/45 border-t border-b border-black/45">
                @foreach ($activityHistories as $activity)
                @php
                $title = $getHistoryValue($activity, [
                'title',
                'name',
                'reward.title',
                'reward.name',
                'offer.title',
                'offer.name',
                'description_title',
                ], 'Membership Activity');

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

                $statusRaw = strtolower((string) $getHistoryValue($activity, [
                'status',
                'type',
                'transaction_type',
                'activity_type',
                ], ''));

                $pointsRaw = $getHistoryValue($activity, [
                'points',
                'point',
                'points_change',
                'point_change',
                'amount',
                'reward.points',
                'offer.points',
                ], 0);

                $pointsNumber = (int) preg_replace('/[^-\d]/', '', (string) $pointsRaw);

                if ($statusRaw === '') {
                $statusRaw = $pointsNumber < 0 ? 'redeemed' : 'purchased' ; } $statusLabel=strtoupper(str_replace(['_', '-' ], ' ' , $statusRaw)); $statusClass=match ($statusRaw) { 'redeemed' , 'redeem' , 'used' , 'deducted'=> 'bg-[#FF3B3B] text-white',
                    'purchased', 'purchase', 'earned', 'paid', 'completed' => 'bg-[#32C85A] text-white',
                    default => 'bg-[#916B2C] text-white',
                    };

                    $date = $formatHistoryDate($getHistoryValue($activity, [
                    'date',
                    'activity_date',
                    'transaction_date',
                    'redeemed_at',
                    'purchased_at',
                    'created_at',
                    ], null));

                    $pointDisplay = ($pointsNumber > 0 ? '+' : ($pointsNumber < 0 ? '-' : '' )) . number_format(abs($pointsNumber)) . ' POINT' ; $image=$resolveImage($getHistoryValue($activity, [ 'image' , 'photo' , 'thumbnail' , 'card_image' , 'reward.image' , 'reward.photo' , 'reward.thumbnail' , 'reward.card_image' , 'offer.image' , 'offer.photo' , 'offer.thumbnail' , 'offer.card_image' , ], null)); @endphp <article class="grid grid-cols-1 gap-5 py-7 md:grid-cols-[250px_1fr] md:gap-12 md:py-8">
                        <div class="w-full">
                            @if ($image)
                            <img src="{{ $image }}" alt="{{ $title }}" class="h-[155px] w-full object-cover md:h-[135px]" loading="lazy">
                            @else
                            <div class="flex h-[155px] w-full items-center justify-center bg-white md:h-[135px]">
                                <span class="text-4xl font-medium uppercase tracking-[0.12em] text-[#916B2C]">
                                    {{ strtoupper(mb_substr($title, 0, 1)) }}
                                </span>
                            </div>
                            @endif
                        </div>

                        <div class="flex min-w-0 flex-col justify-center">
                            <h3 class="text-xl sm:text-2xl leading-snug tracking-[0.16em] uppercase text-black font-medium">
                                {{ $title }}
                            </h3>

                            @if ($description)
                            <p class="mt-3 text-[14px] sm:text-[15px] leading-relaxed text-black">
                                {{ strip_tags($description) }}
                            </p>
                            @endif

                            <div class="mt-7 grid grid-cols-1 items-center gap-4 text-[14px] sm:grid-cols-[150px_160px_1fr] md:mt-9">
                                <div>
                                    <span class="{{ $statusClass }} inline-flex min-w-[138px] items-center justify-center rounded-full px-5 py-2 text-[13px] font-bold leading-none tracking-[0.16em]">
                                        {{ $statusLabel }}
                                    </span>
                                </div>

                                <p class="text-[15px] font-bold uppercase leading-none tracking-[0.18em] text-black">
                                    {{ $date }}
                                </p>

                                <p class="text-[15px] font-bold uppercase leading-none tracking-[0.18em] text-black">
                                    {{ $pointDisplay }}
                                </p>
                            </div>
                        </div>
                        </article>
                        @endforeach
            </div>
        </div>
    </section>
</x-layouts.app>
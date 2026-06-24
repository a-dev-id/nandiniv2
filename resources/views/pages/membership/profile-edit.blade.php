@push('meta')
@php
$metaTitle = $page?->meta_title ?: ($page?->title ?: 'Edit Profile | Nandini Jungle Inner Circle');

$metaDescription = $page?->meta_description
?: \Illuminate\Support\Str::limit(strip_tags($page?->description ?: 'Update your Inner Circle member profile.'), 160, '');

$metaImage = $page?->hero_image ?: $page?->hero_mobile_image ?: null;
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
    @php
    $firstName = old('first_name', $member->first_name);
    $lastName = old('last_name', $member->last_name);
    $dateOfBirth = old('date_of_birth', $member->date_of_birth ? $member->date_of_birth->format('Y-m-d') : '');
    $country = old('country', $member->country);
    $address = old('address', $member->address);
    $countries = \App\Support\InquiryOptions::countries();
    $phoneCodes = \App\Support\InquiryOptions::phoneCodes();
    $storedPhoneNumber = trim((string) $member->phone_number);
    $detectedPhoneCode = '+62';
    $detectedPhone = $storedPhoneNumber;

    foreach (collect($phoneCodes)->sortByDesc(fn ($phoneCode) => strlen($phoneCode['code'])) as $phoneCode) {
    if (str_starts_with($storedPhoneNumber, $phoneCode['code'])) {
    $detectedPhoneCode = $phoneCode['code'];
    $detectedPhone = trim(preg_replace('/^' . preg_quote($phoneCode['code'], '/') . '[\s-]*/', '', $storedPhoneNumber) ?? $storedPhoneNumber);
    break;
    }
    }

    $selectedPhoneCode = old('phone_code', $detectedPhoneCode);
    $phoneNumber = old('phone', $detectedPhone);
    $uniquePhoneCodes = collect($phoneCodes)
    ->unique('code')
    ->values()
    ->all();

    if ($country && ! array_key_exists($country, $countries)) {
    $countries = [$country => $country] + $countries;
    }

    $countryPhoneCodes = collect($phoneCodes)
    ->unique('country')
    ->mapWithKeys(fn ($phoneCode) => [$phoneCode['country'] => $phoneCode['code']])
    ->all();

    $profilePhoto = $member->profile_photo ?? $member->photo ?? null;

    $profilePhotoUrl = $profilePhoto
    ? (str_starts_with($profilePhoto, 'http') ? $profilePhoto : asset('storage/' . $profilePhoto))
    : null;
    @endphp

    <section class="w-full bg-[#F7F7F7] py-20 md:py-28 lg:py-32">
        <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto w-full max-w-[720px] overflow-hidden bg-white px-5 py-8 shadow-xl sm:px-8 md:px-10 md:py-10 lg:px-12">
                <div class="mb-8 text-center">
                    <p class="text-xs sm:text-sm uppercase text-[#A67C3D]">
                        Nandini Inner Circle
                    </p>

                    <h1 class="text-xl mt-4 leading-snug uppercase text-slate-700 font-medium mb-3 sm:text-2xl">
                        Edit Profile
                    </h1>

                    <p class="mx-auto mt-2 max-w-md text-xs leading-7 text-slate-600 sm:text-sm">
                        Update your member information below.
                    </p>
                </div>

                @if (session('success'))
                <div class="mb-6 border border-green-200 bg-green-50 px-4 py-3 text-[12px] leading-6 text-green-700 sm:text-[14px]">
                    {{ session('success') }}
                </div>
                @endif

                @if ($errors->any())
                <div class="mb-6 border border-red-200 bg-red-50 px-4 py-3 text-[12px] leading-6 text-red-700 sm:text-[14px]">
                    <p class="font-semibold">
                        Please check the following:
                    </p>

                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('membership.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="flex flex-col items-center gap-4 text-center">
                        <div class="flex aspect-[4/5] w-full max-w-[150px] items-center justify-center overflow-hidden bg-[#F7F7F7]">
                            @if ($profilePhotoUrl)
                            <img src="{{ $profilePhotoUrl }}" alt="{{ $member->full_name }}" class="h-full w-full object-cover">
                            @else
                            <span class="text-4xl font-medium uppercase text-[#A67C3D] sm:text-5xl">
                                {{ strtoupper(mb_substr($member->full_name ?: $member->name ?: 'M', 0, 1)) }}
                            </span>
                            @endif
                        </div>

                        <div class="w-full">
                            <label for="profile_photo" class="mb-3 block text-xs sm:text-sm uppercase text-slate-500">
                                Profile Photo
                            </label>

                            <input id="profile_photo" type="file" name="profile_photo" accept="image/jpeg,image/png,image/webp" class="w-full border border-slate-300 bg-white px-4 py-3 text-[12px] leading-7 text-slate-700 outline-none transition file:mr-4 file:border-0 file:bg-[#A67C3D] file:px-4 file:py-2 file:text-[9px] file:font-bold file:uppercase file:text-white focus:border-[#A67C3D] sm:text-[14px] sm:file:text-[11px]">

                            <p class="mt-2 text-[11px] leading-6 text-slate-500 sm:text-[13px]">
                                JPG, PNG, or WEBP. Maximum 2MB.
                            </p>
                        </div>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label for="first_name" class="mb-3 block text-xs sm:text-sm uppercase text-slate-500">
                                First Name
                            </label>

                            <input id="first_name" type="text" name="first_name" value="{{ $firstName }}" class="w-full border border-slate-300 bg-white px-4 py-3 text-xs leading-7 text-slate-700 outline-none transition focus:border-[#A67C3D] sm:text-sm">
                        </div>

                        <div>
                            <label for="last_name" class="mb-3 block text-xs sm:text-sm uppercase text-slate-500">
                                Last Name
                            </label>

                            <input id="last_name" type="text" name="last_name" value="{{ $lastName }}" class="w-full border border-slate-300 bg-white px-4 py-3 text-xs leading-7 text-slate-700 outline-none transition focus:border-[#A67C3D] sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="mb-3 block text-xs sm:text-sm uppercase text-slate-500">
                            Email Address
                        </label>

                        <input id="email" type="email" value="{{ $member->email }}" disabled class="w-full cursor-not-allowed border border-slate-200 bg-slate-100 px-4 py-3 text-xs leading-7 text-slate-500 sm:text-sm">

                        <p class="mt-2 text-[11px] leading-6 text-slate-500 sm:text-[13px]">
                            Email cannot be changed from this page.
                        </p>
                    </div>

                    <div>
                        <label for="country" class="mb-3 block text-xs sm:text-sm uppercase text-slate-500">
                            Country
                        </label>

                        <select id="country" name="country" data-country-select class="block w-full max-w-full min-w-0 border border-slate-300 bg-white px-4 py-3 text-xs leading-7 text-slate-700 outline-none transition focus:border-[#A67C3D] sm:text-sm">
                            <option value="">Select Country</option>
                            @foreach ($countries as $value => $label)
                            <option value="{{ $value }}" @selected($country===$value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid min-w-0 gap-6 md:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]">
                        <div class="min-w-0">
                            <label for="phone" class="mb-3 block text-xs sm:text-sm uppercase text-slate-500">
                                Phone / WhatsApp Number
                            </label>

                            <div class="flex w-full max-w-full min-w-0 overflow-hidden border border-slate-300 bg-white transition focus-within:border-[#A67C3D]">
                                <select name="phone_code" data-phone-code-select aria-label="Country phone code" class="w-[94px] shrink-0 border-0 border-r border-slate-300 bg-white px-3 py-3 text-xs leading-7 text-slate-700 outline-none sm:w-[104px] sm:text-sm">
                                    @foreach ($uniquePhoneCodes as $phoneCode)
                                    <option value="{{ $phoneCode['code'] }}" @selected($selectedPhoneCode===$phoneCode['code'])>{{ $phoneCode['code'] }}</option>
                                    @endforeach
                                </select>

                                <input id="phone" type="tel" name="phone" value="{{ $phoneNumber }}" autocomplete="tel" placeholder="812 3456 7890" class="min-w-0 flex-1 border-0 bg-white px-3 py-3 text-xs leading-7 text-slate-700 outline-none sm:px-4 sm:text-sm">
                            </div>
                        </div>

                        <div class="min-w-0">
                            <label for="date_of_birth" class="mb-3 block text-xs sm:text-sm uppercase text-slate-500">
                                Date of Birth
                            </label>

                            <input id="date_of_birth" type="date" name="date_of_birth" value="{{ $dateOfBirth }}" class="block w-full max-w-full min-w-0 border border-slate-300 bg-white px-4 py-3 text-xs leading-7 text-slate-700 outline-none transition focus:border-[#A67C3D] sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <label for="address" class="mb-3 block text-xs sm:text-sm uppercase text-slate-500">
                            Address
                        </label>

                        <textarea id="address" name="address" rows="4" class="w-full border border-slate-300 bg-white px-4 py-3 text-xs leading-7 text-slate-700 outline-none transition focus:border-[#A67C3D] sm:text-sm">{{ $address }}</textarea>
                    </div>

                    <div class="border-t border-slate-200 pt-6">
                        <h2 class="text-lg uppercase text-slate-700 mb-3 sm:text-xl">
                            Change Password
                        </h2>

                        <p class="mt-2 text-[11px] leading-6 text-slate-500 sm:text-[13px]">
                            Leave these fields empty if you do not want to change your password.
                        </p>
                    </div>

                    <div>
                        <label for="current_password" class="mb-3 block text-xs sm:text-sm uppercase text-slate-500">
                            Current Password
                        </label>

                        <input id="current_password" type="password" name="current_password" autocomplete="current-password" class="w-full border border-slate-300 bg-white px-4 py-3 text-xs leading-7 text-slate-700 outline-none transition focus:border-[#A67C3D] sm:text-sm">

                        @error('current_password')
                        <p class="mt-2 text-[12px] leading-6 text-red-600 sm:text-[14px]">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label for="password" class="mb-3 block text-xs sm:text-sm uppercase text-slate-500">
                                New Password
                            </label>

                            <input id="password" type="password" name="password" autocomplete="new-password" class="w-full border border-slate-300 bg-white px-4 py-3 text-xs leading-7 text-slate-700 outline-none transition focus:border-[#A67C3D] sm:text-sm">

                            @error('password')
                            <p class="mt-2 text-[12px] leading-6 text-red-600 sm:text-[14px]">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="mb-3 block text-xs sm:text-sm uppercase text-slate-500">
                                Confirm New Password
                            </label>

                            <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" class="w-full border border-slate-300 bg-white px-4 py-3 text-xs leading-7 text-slate-700 outline-none transition focus:border-[#A67C3D] sm:text-sm">
                        </div>
                    </div>

                    <button type="submit" class="inline-flex w-full items-center justify-center bg-[#A67C3D] px-5 py-2.5 text-xs font-medium uppercase text-white transition hover:bg-[#B8945B] tracking-[0.08em] sm:text-sm">
                        Save Profile
                    </button>

                    <div class="pt-2 text-center text-xs leading-7 text-slate-700 sm:text-sm">
                        <a href="{{ route('membership.dashboard') }}" class="font-medium uppercase text-[#A67C3D] transition hover:text-[#8F6B34] tracking-[0.08em]">
                            Back to Dashboard
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const countrySelect = document.querySelector('[data-country-select]');
            const phoneCodeSelect = document.querySelector('[data-phone-code-select]');
            const countryPhoneCodes = @json($countryPhoneCodes);

            if (!countrySelect || !phoneCodeSelect) {
                return;
            }

            function syncPhoneCode() {
                const phoneCode = countryPhoneCodes[countrySelect.value];

                if (!phoneCode) {
                    return;
                }

                phoneCodeSelect.value = phoneCode;
            }

            countrySelect.addEventListener('change', syncPhoneCode);
            syncPhoneCode();
        });
    </script>
</x-layouts.app>
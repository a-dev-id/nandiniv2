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
    @if ($page)
    <x-heroes.image-hero :page="$page" />
    <x-sections.page-description :page="$page" />
    @else
    <section class="bg-white px-6 pt-16 md:px-12 lg:px-[70px]">
        <div class="mx-auto w-full max-w-4xl text-center">
            <p class="text-sm uppercase tracking-[0.25em] text-[#916B2C]">
                Inner Circle
            </p>

            <h1 class="mt-4 text-3xl md:text-5xl uppercase tracking-[0.18em] text-slate-950 font-medium leading-tight">
                Edit Profile
            </h1>

            <p class="mt-5 text-base leading-relaxed text-slate-600">
                Update your member information below.
            </p>
        </div>
    </section>
    @endif

    <section class="bg-white px-6 pb-16 md:px-12 lg:px-[70px]">
        <div class="mx-auto w-full max-w-4xl">
            @if (session('success'))
            <div class="border border-green-700 bg-green-50 px-5 py-4 text-sm text-green-900">
                {{ session('success') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="border border-red-700 bg-red-50 px-5 py-4 text-sm text-red-900">
                {{ $errors->first() }}
            </div>
            @endif

            @php
            $memberName = old('name', $member->name);
            $firstName = old('first_name', $member->first_name);
            $lastName = old('last_name', $member->last_name);
            $phoneNumber = old('phone_number', $member->phone_number);
            $dateOfBirth = old('date_of_birth', $member->date_of_birth ? $member->date_of_birth->format('Y-m-d') : '');
            $country = old('country', $member->country);
            $address = old('address', $member->address);

            $profilePhoto = $member->profile_photo ?? $member->photo ?? null;

            $profilePhotoUrl = $profilePhoto
            ? (str_starts_with($profilePhoto, 'http') ? $profilePhoto : asset('storage/' . $profilePhoto))
            : null;
            @endphp

            <form method="POST" action="{{ route('membership.profile.update') }}" enctype="multipart/form-data" class="mt-10 border border-slate-200 bg-slate-50 px-6 py-8 md:px-8">
                @csrf
                @method('PUT')

                <div class="grid gap-8 md:grid-cols-[220px_1fr]">
                    <div>
                        <p class="text-xs uppercase tracking-[0.16em] text-slate-500">
                            Profile Photo
                        </p>

                        <div class="mt-4 flex aspect-[4/5] w-full max-w-[180px] items-center justify-center overflow-hidden bg-white border border-slate-200">
                            @if ($profilePhotoUrl)
                            <img src="{{ $profilePhotoUrl }}" alt="{{ $member->full_name }}" class="h-full w-full object-cover">
                            @else
                            <span class="text-5xl font-medium uppercase tracking-[0.08em] text-[#916B2C]">
                                {{ strtoupper(mb_substr($member->full_name ?: $member->name ?: 'M', 0, 1)) }}
                            </span>
                            @endif
                        </div>

                        <input type="file" name="profile_photo" accept="image/jpeg,image/png,image/webp" class="mt-4 block w-full text-sm text-slate-700 file:mr-4 file:border-0 file:bg-[#916B2C] file:px-4 file:py-2 file:text-sm file:uppercase file:tracking-[0.12em] file:text-white">

                        <p class="mt-3 text-xs leading-relaxed text-slate-500">
                            JPG, PNG, or WEBP. Maximum 2MB.
                        </p>
                    </div>

                    <div class="grid gap-5">
                        <div>
                            <label for="name" class="block text-xs uppercase tracking-[0.16em] text-slate-600">
                                Display Name
                            </label>

                            <input id="name" type="text" name="name" value="{{ $memberName }}" class="mt-2 w-full border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#916B2C] focus:outline-none">
                        </div>

                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label for="first_name" class="block text-xs uppercase tracking-[0.16em] text-slate-600">
                                    First Name
                                </label>

                                <input id="first_name" type="text" name="first_name" value="{{ $firstName }}" class="mt-2 w-full border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#916B2C] focus:outline-none">
                            </div>

                            <div>
                                <label for="last_name" class="block text-xs uppercase tracking-[0.16em] text-slate-600">
                                    Last Name
                                </label>

                                <input id="last_name" type="text" name="last_name" value="{{ $lastName }}" class="mt-2 w-full border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#916B2C] focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-xs uppercase tracking-[0.16em] text-slate-600">
                                Email
                            </label>

                            <input id="email" type="email" value="{{ $member->email }}" disabled class="mt-2 w-full border border-slate-200 bg-slate-100 px-4 py-3 text-sm text-slate-500 cursor-not-allowed">

                            <p class="mt-2 text-xs text-slate-500">
                                Email cannot be changed from this page.
                            </p>
                        </div>

                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label for="phone_number" class="block text-xs uppercase tracking-[0.16em] text-slate-600">
                                    Phone Number
                                </label>

                                <input id="phone_number" type="text" name="phone_number" value="{{ $phoneNumber }}" class="mt-2 w-full border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#916B2C] focus:outline-none">
                            </div>

                            <div>
                                <label for="date_of_birth" class="block text-xs uppercase tracking-[0.16em] text-slate-600">
                                    Date of Birth
                                </label>

                                <input id="date_of_birth" type="date" name="date_of_birth" value="{{ $dateOfBirth }}" class="mt-2 w-full border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#916B2C] focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label for="country" class="block text-xs uppercase tracking-[0.16em] text-slate-600">
                                Country
                            </label>

                            <input id="country" type="text" name="country" value="{{ $country }}" class="mt-2 w-full border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#916B2C] focus:outline-none">
                        </div>

                        <div>
                            <label for="address" class="block text-xs uppercase tracking-[0.16em] text-slate-600">
                                Address
                            </label>

                            <textarea id="address" name="address" rows="4" class="mt-2 w-full border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#916B2C] focus:outline-none">{{ $address }}</textarea>
                        </div>

                        <div class="mt-4 flex flex-col gap-4 sm:flex-row">
                            <button type="submit" class="inline-flex min-w-[170px] items-center justify-center border border-[#916B2C] bg-[#916B2C] px-7 py-4 text-sm uppercase tracking-[0.14em] text-white hover:bg-white hover:text-[#916B2C] transition">
                                Save Profile
                            </button>

                            <a href="{{ route('membership.dashboard') }}" class="inline-flex min-w-[170px] items-center justify-center border border-slate-950 px-7 py-4 text-sm uppercase tracking-[0.14em] text-slate-950 hover:bg-slate-950 hover:text-white transition">
                                Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</x-layouts.app>
{{-- resources/views/pages/membership/auth/join.blade.php --}}

@php
$page = $page ?? null;

$metaTitle = $page?->meta_title ?: 'Register | Nandini Inner Circle Membership';

$metaDescription = $page?->meta_description
?: 'Create your Nandini Inner Circle account and start your membership journey with Nandini Jungle by Hanging Gardens.';

$imageAlt = $page?->hero_image_alt
?: $page?->hero_mobile_image_alt
?: 'Nandini Inner Circle Register';

$title = $page?->title ?: 'Register';
$subtitle = $page?->subtitle ?: 'Nandini Inner Circle';

$description = $page?->description
?: $page?->excerpt
?: 'Create your membership account, access your rewards, and enjoy exclusive benefits with Nandini Inner Circle.';

$descriptionHasHtml = is_string($description) && $description !== strip_tags($description);

$countries = [
'Australia',
'Austria',
'Belgium',
'Brazil',
'Canada',
'China',
'Denmark',
'Finland',
'France',
'Germany',
'Hong Kong',
'India',
'Indonesia',
'Ireland',
'Italy',
'Japan',
'Kuwait',
'Malaysia',
'Mexico',
'Netherlands',
'New Zealand',
'Norway',
'Other',
'Philippines',
'Qatar',
'Russia',
'Saudi Arabia',
'Singapore',
'South Africa',
'South Korea',
'Spain',
'Sweden',
'Switzerland',
'Taiwan',
'Thailand',
'Turkey',
'United Arab Emirates',
'United Kingdom',
'United States',
'Vietnam',
];
@endphp

@push('meta')
<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
@endpush

<x-layouts.app>
    {{-- HERO --}}
    @if ($page)
    <x-heroes.image-hero :page="$page" :alt-text="$imageAlt" />
    @endif

    {{-- REGISTER SECTION --}}
    <section class="w-full bg-[#F7F7F7] py-16 md:py-24 lg:py-28">
        <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto grid max-w-6xl grid-cols-1 items-start gap-10 lg:grid-cols-12 lg:gap-14">

                {{-- LEFT CONTENT --}}
                <div class="lg:col-span-5">
                    <div class="flex h-full flex-col justify-center px-4 sm:px-8 md:px-10 lg:px-0">
                        <div class="text-center lg:text-left">

                            @if ($subtitle)
                            <p class="text-xs sm:text-sm uppercase tracking-[0.22em] text-[#A67C3D]">
                                {{ $subtitle }}
                            </p>
                            @endif

                            <h1 class="mt-4 text-4xl leading-snug tracking-[0.15em] md:tracking-[0.25em] uppercase text-slate-800 mb-6 md:mb-8 font-medium">
                                {{ $title }}
                            </h1>

                            @if ($description)
                            <div class="text-[15px] sm:text-base leading-relaxed text-gray-600 max-w-2xl sm:max-w-3xl md:max-w-5xl mx-auto [&_p]:mb-2 [&_p:last-child]:mb-0">
                                @if ($descriptionHasHtml)
                                {!! $description !!}
                                @else
                                {!! nl2br(e($description)) !!}
                                @endif
                            </div>
                            @endif

                            <div class="mt-8 text-[15px] leading-7 text-slate-700">
                                Already a member?

                                <a href="{{ route('membership.login') }}" class="font-bold uppercase tracking-[0.18em] text-[#A67C3D] transition hover:text-[#8F6B34]">
                                    Sign In
                                </a>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- FORM CARD --}}
                <div class="lg:col-span-7">
                    <div class="mx-auto w-full max-w-[720px] bg-white px-6 py-8 shadow-xl sm:px-8 md:px-10 md:py-10 lg:px-12">

                        <form id="membership-register-form" method="POST" action="{{ Route::has('membership.register.submit') ? route('membership.register.submit') : route('membership.register') }}" class="space-y-6">
                            @csrf

                            <p class="text-[13px] leading-6 text-slate-500">
                                <span class="text-red-600">*</span> Required fields
                            </p>

                            {{-- Name --}}
                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <div>
                                    <label for="first_name" class="mb-3 block text-xs sm:text-sm uppercase tracking-[0.18em] text-slate-500">
                                        First Name <span class="text-red-600">*</span>
                                    </label>

                                    <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required autocomplete="given-name" class="w-full border border-slate-300 bg-white px-4 py-3 text-[15px] leading-7 text-slate-700 outline-none transition focus:border-[#A67C3D]">

                                    @error('first_name')
                                    <p class="mt-2 text-[14px] leading-6 text-red-600">
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="last_name" class="mb-3 block text-xs sm:text-sm uppercase tracking-[0.18em] text-slate-500">
                                        Last Name <span class="text-red-600">*</span>
                                    </label>

                                    <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required autocomplete="family-name" class="w-full border border-slate-300 bg-white px-4 py-3 text-[15px] leading-7 text-slate-700 outline-none transition focus:border-[#A67C3D]">

                                    @error('last_name')
                                    <p class="mt-2 text-[14px] leading-6 text-red-600">
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Phone and Country --}}
                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <div>
                                    <label for="phone_number" class="mb-3 block text-xs sm:text-sm uppercase tracking-[0.18em] text-slate-500">
                                        Phone Number / WhatsApp
                                    </label>

                                    <input id="phone_number" type="text" name="phone_number" value="{{ old('phone_number') }}" autocomplete="tel" class="w-full border border-slate-300 bg-white px-4 py-3 text-[15px] leading-7 text-slate-700 outline-none transition focus:border-[#A67C3D]">

                                    @error('phone_number')
                                    <p class="mt-2 text-[14px] leading-6 text-red-600">
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="country" class="mb-3 block text-xs sm:text-sm uppercase tracking-[0.18em] text-slate-500">
                                        Country <span class="text-red-600">*</span>
                                    </label>

                                    <input id="country" type="text" name="country" value="{{ old('country') }}" list="country-list" required autocomplete="country-name" placeholder="Search country..." class="w-full border border-slate-300 bg-white px-4 py-3 text-[15px] leading-7 text-slate-700 outline-none transition focus:border-[#A67C3D]">

                                    <datalist id="country-list">
                                        @foreach ($countries as $country)
                                        <option value="{{ $country }}"></option>
                                        @endforeach
                                    </datalist>

                                    @error('country')
                                    <p class="mt-2 text-[14px] leading-6 text-red-600">
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="mb-3 block text-xs sm:text-sm uppercase tracking-[0.18em] text-slate-500">
                                    Email Address <span class="text-red-600">*</span>
                                </label>

                                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="w-full border border-slate-300 bg-white px-4 py-3 text-[15px] leading-7 text-slate-700 outline-none transition focus:border-[#A67C3D]">

                                @error('email')
                                <p class="mt-2 text-[14px] leading-6 text-red-600">
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            {{-- Address --}}
                            <div>
                                <label for="address" class="mb-3 block text-xs sm:text-sm uppercase tracking-[0.18em] text-slate-500">
                                    Address
                                </label>

                                <textarea id="address" name="address" rows="3" autocomplete="street-address" class="w-full resize-none border border-slate-300 bg-white px-4 py-3 text-[15px] leading-7 text-slate-700 outline-none transition focus:border-[#A67C3D]">{{ old('address') }}</textarea>

                                @error('address')
                                <p class="mt-2 text-[14px] leading-6 text-red-600">
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <div>
                                    <label for="password" class="mb-3 block text-xs sm:text-sm uppercase tracking-[0.18em] text-slate-500">
                                        Password <span class="text-red-600">*</span>
                                    </label>

                                    <div class="relative">
                                        <input id="password" type="password" name="password" required autocomplete="new-password" class="w-full border border-slate-300 bg-white px-4 py-3 pr-14 text-[15px] leading-7 text-slate-700 outline-none transition focus:border-[#A67C3D]">

                                        <button type="button" data-toggle-password="password" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 transition hover:text-[#A67C3D]" aria-label="Show password">
                                            <svg data-eye-open class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>

                                            <svg data-eye-closed class="hidden h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.58 10.58A2 2 0 0 0 12 14a2 2 0 0 0 1.42-.58" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.88 4.74A9.44 9.44 0 0 1 12 4.5c6 0 9.75 7.5 9.75 7.5a17.47 17.47 0 0 1-2.16 3.19" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6.61 6.61C3.78 8.49 2.25 12 2.25 12s3.75 7.5 9.75 7.5a9.7 9.7 0 0 0 4.39-1.04" />
                                            </svg>
                                        </button>
                                    </div>

                                    @error('password')
                                    <p class="mt-2 text-[14px] leading-6 text-red-600">
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="mb-3 block text-xs sm:text-sm uppercase tracking-[0.18em] text-slate-500">
                                        Confirm Password <span class="text-red-600">*</span>
                                    </label>

                                    <div class="relative">
                                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="w-full border border-slate-300 bg-white px-4 py-3 pr-14 text-[15px] leading-7 text-slate-700 outline-none transition focus:border-[#A67C3D]">

                                        <button type="button" data-toggle-password="password_confirmation" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 transition hover:text-[#A67C3D]" aria-label="Show confirm password">
                                            <svg data-eye-open class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>

                                            <svg data-eye-closed class="hidden h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.58 10.58A2 2 0 0 0 12 14a2 2 0 0 0 1.42-.58" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.88 4.74A9.44 9.44 0 0 1 12 4.5c6 0 9.75 7.5 9.75 7.5a17.47 17.47 0 0 1-2.16 3.19" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6.61 6.61C3.78 8.49 2.25 12 2.25 12s3.75 7.5 9.75 7.5a9.7 9.7 0 0 0 4.39-1.04" />
                                            </svg>
                                        </button>
                                    </div>

                                    <p id="password-match-message" class="mt-2 hidden text-[14px] leading-6"></p>
                                </div>
                            </div>

                            {{-- Checkboxes --}}
                            <div class="space-y-3">
                                <label class="flex items-start gap-3 text-[14px] leading-6 text-slate-700">
                                    <input type="checkbox" name="marketing_consent" value="1" @checked(old('marketing_consent')) class="mt-1 h-4 w-4 border-slate-300 text-[#A67C3D] focus:ring-[#A67C3D]">

                                    <span>
                                        I would like to receive personalized communications, including offers, details about promotions, and travel-related products from Nandini Jungle by Hanging Gardens via email.
                                    </span>
                                </label>

                                @error('marketing_consent')
                                <p class="text-[14px] leading-6 text-red-600">
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            {{-- Terms --}}
                            <div class="text-[14px] leading-6 text-slate-700">
                                By signing up, I agree to Nandini Jungle by Hanging Gardens
                                <a href="#" class="font-bold text-slate-900 underline decoration-slate-300 underline-offset-4 transition hover:text-[#A67C3D]">
                                    Terms and Conditions
                                </a>.
                                I also acknowledge Nandini Jungle by Hanging Gardens Privacy Statement located in the
                                <a href="#" class="font-bold text-slate-900 underline decoration-slate-300 underline-offset-4 transition hover:text-[#A67C3D]">
                                    Privacy Center
                                </a>.
                            </div>

                            {{-- Submit --}}
                            <button id="register-submit-button" type="submit" class="inline-flex w-full items-center justify-center bg-[#A67C3D] px-7 py-4 text-[12px] font-bold uppercase tracking-[0.22em] text-white transition hover:bg-[#8F6B34] disabled:cursor-not-allowed disabled:opacity-50 md:w-auto">
                                Register
                            </button>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('membership-register-form');
            const passwordInput = document.getElementById('password');
            const confirmationInput = document.getElementById('password_confirmation');
            const message = document.getElementById('password-match-message');
            const submitButton = document.getElementById('register-submit-button');
            const toggleButtons = document.querySelectorAll('[data-toggle-password]');

            if (!form || !passwordInput || !confirmationInput || !message || !submitButton) {
                return;
            }

            function resetPasswordMatchMessage() {
                message.classList.add('hidden');
                message.textContent = '';
                message.className = 'mt-2 hidden text-[14px] leading-6';

                confirmationInput.style.borderColor = '';
                submitButton.disabled = false;
            }

            function checkPasswordMatch() {
                const password = passwordInput.value;
                const confirmation = confirmationInput.value;

                if (!password || !confirmation) {
                    resetPasswordMatchMessage();
                    return true;
                }

                message.classList.remove('hidden');

                if (password === confirmation) {
                    message.textContent = 'Password matches.';
                    message.className = 'mt-2 text-[14px] leading-6 text-green-600';
                    confirmationInput.style.borderColor = '#16A34A';
                    submitButton.disabled = false;

                    return true;
                }

                message.textContent = 'Password does not match.';
                message.className = 'mt-2 text-[14px] leading-6 text-red-600';
                confirmationInput.style.borderColor = '#DC2626';
                submitButton.disabled = true;

                return false;
            }

            toggleButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const inputId = button.getAttribute('data-toggle-password');
                    const input = document.getElementById(inputId);

                    if (!input) {
                        return;
                    }

                    const eyeOpen = button.querySelector('[data-eye-open]');
                    const eyeClosed = button.querySelector('[data-eye-closed]');
                    const isHidden = input.type === 'password';

                    input.type = isHidden ? 'text' : 'password';

                    if (eyeOpen && eyeClosed) {
                        eyeOpen.classList.toggle('hidden', isHidden);
                        eyeClosed.classList.toggle('hidden', !isHidden);
                    }

                    button.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
                });
            });

            passwordInput.addEventListener('input', checkPasswordMatch);
            confirmationInput.addEventListener('input', checkPasswordMatch);

            form.addEventListener('submit', function (event) {
                if (!checkPasswordMatch()) {
                    event.preventDefault();
                    confirmationInput.focus();
                }
            });
        });
    </script>
</x-layouts.app>

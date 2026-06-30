@push('meta')
<title>Change Password | Nandini Inner Circle Membership</title>
<meta name="description" content="Change your temporary password and secure your Nandini Inner Circle membership account.">
@endpush

<x-layouts.app>
    <section class="w-full bg-[#F7F7F7] py-16 md:py-24 lg:py-28">
        <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-[620px] bg-white px-6 py-8 shadow-xl sm:px-8 md:px-10 md:py-10 lg:px-12">

                <div class="mb-8 text-center">
                    <p class="text-xs sm:text-sm uppercase text-[#A88444]">
                        Nandini Inner Circle
                    </p>

                    <h1 class="text-xl mt-4 leading-snug uppercase text-slate-700 font-medium mb-3 sm:text-2xl">
                        Change Password
                    </h1>

                    <p class="text-xs leading-relaxed text-gray-600 max-w-2xl sm:max-w-3xl md:max-w-5xl mx-auto sm:text-sm">
                        Your account was created automatically from your booking. Please change your temporary password before continuing.
                    </p>
                </div>

                @if (session('status'))
                <div class="mb-6 border border-green-200 bg-green-50 px-4 py-3 text-[12px] leading-6 text-green-700 sm:text-[14px]">
                    {{ session('status') }}
                </div>
                @endif

                <form id="membership-change-password-form" method="POST" action="{{ route('membership.password.update') }}" class="space-y-6">
                    @csrf

                    {{-- Current Password --}}
                    <div>
                        <label for="current_password" class="mb-3 block text-xs sm:text-sm uppercase text-slate-500">
                            Current Password <span class="text-red-600">*</span>
                        </label>

                        <div class="relative">
                            <input id="current_password" type="password" name="current_password" required autocomplete="current-password" class="w-full border border-slate-300 bg-white px-4 py-3 pr-14 text-xs leading-7 text-slate-700 outline-none transition focus:border-[#A88444] sm:text-sm">

                            <button type="button" data-toggle-password="current_password" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 transition hover:text-[#A88444]" aria-label="Show current password">
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

                        @error('current_password')
                        <p class="mt-2 text-[12px] leading-6 text-red-600 sm:text-[14px]">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- New Password --}}
                    <div>
                        <label for="password" class="mb-3 block text-xs sm:text-sm uppercase text-slate-500">
                            New Password <span class="text-red-600">*</span>
                        </label>

                        <div class="relative">
                            <input id="password" type="password" name="password" required autocomplete="new-password" class="w-full border border-slate-300 bg-white px-4 py-3 pr-14 text-xs leading-7 text-slate-700 outline-none transition focus:border-[#A88444] sm:text-sm">

                            <button type="button" data-toggle-password="password" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 transition hover:text-[#A88444]" aria-label="Show new password">
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
                        <p class="mt-2 text-[12px] leading-6 text-red-600 sm:text-[14px]">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label for="password_confirmation" class="mb-3 block text-xs sm:text-sm uppercase text-slate-500">
                            Confirm New Password <span class="text-red-600">*</span>
                        </label>

                        <div class="relative">
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="w-full border border-slate-300 bg-white px-4 py-3 pr-14 text-xs leading-7 text-slate-700 outline-none transition focus:border-[#A88444] sm:text-sm">

                            <button type="button" data-toggle-password="password_confirmation" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 transition hover:text-[#A88444]" aria-label="Show confirm password">
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

                        <p id="password-match-message" class="mt-2 hidden text-[12px] leading-6 sm:text-[14px]"></p>
                    </div>

                    <x-recaptcha />

                    <button id="change-password-submit-button" type="submit" class="inline-flex w-full items-center justify-center bg-[#A88444] px-5 py-2.5 text-xs font-medium uppercase text-white transition hover:bg-[#B8945B] disabled:cursor-not-allowed disabled:opacity-50 tracking-[0.08em] sm:text-sm">
                        Update Password
                    </button>
                </form>

            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('membership-change-password-form');
            const passwordInput = document.getElementById('password');
            const confirmationInput = document.getElementById('password_confirmation');
            const message = document.getElementById('password-match-message');
            const submitButton = document.getElementById('change-password-submit-button');
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

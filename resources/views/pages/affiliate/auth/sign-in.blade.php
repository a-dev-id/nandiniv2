@push('meta')
<title>Affiliate Login | Nandini Partner Circle</title>
<meta name="description" content="Sign in to the Nandini Partner Circle affiliate portal.">
<meta name="robots" content="noindex,nofollow">
@endpush

<x-layouts.app>
    <x-heroes.image-hero
        :image-src="asset('images/membership/join-today.webp')"
        alt-text="Nandini Partner Circle affiliate login"
    />

    <section class="w-full bg-[#F7F7F7] py-16 md:py-24 lg:py-28">
        <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto grid max-w-6xl grid-cols-1 items-start gap-10 lg:grid-cols-12 lg:gap-14">
                <div class="lg:col-span-5">
                    <div class="flex h-full flex-col justify-center px-4 sm:px-8 md:px-10 lg:px-0">
                        <div class="text-center lg:text-left">
                            <p class="text-xs font-medium uppercase tracking-[0.12em] text-[#A88444] sm:text-sm">Nandini Partner Circle</p>
                            <h1 class="mb-3 mt-4 text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">Affiliate Login</h1>
                            <p class="mx-auto max-w-2xl text-xs leading-relaxed text-gray-600 sm:text-sm lg:mx-0">
                                Sign in to the Nandini Partner Circle affiliate portal.
                            </p>
                            @if (config('features.affiliate_registration_enabled'))
                                <div class="mt-4 text-center text-xs leading-7 text-slate-700 sm:text-sm lg:text-left">
                                    Not registered yet?
                                    <a href="{{ route('affiliate.register') }}" class="font-medium uppercase tracking-[0.08em] text-[#A88444] transition hover:text-[#8F6B34]">Join Affiliate</a>
                                </div>
                            @else
                                <p class="mt-4 text-center text-xs leading-7 text-slate-600 sm:text-sm lg:text-left">New Affiliate registration is temporarily unavailable. Existing partners can continue to sign in.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-7">
                    <div class="mx-auto w-full max-w-[560px] bg-white px-6 py-8 shadow-xl sm:px-8 md:px-10 md:py-10 lg:px-12">
                        @if ($errors->any())
                            <div class="mb-6 border border-red-200 bg-red-50 px-4 py-3 text-[12px] leading-6 text-red-700 sm:text-[14px]">
                                <p class="font-semibold">Please check the following:</p>
                                <ul class="mt-2 list-disc space-y-1 pl-5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('affiliate.login.submit') }}" class="space-y-6">
                            @csrf

                            <div>
                                <label for="email" class="mb-3 block text-xs uppercase text-slate-500 sm:text-sm">Email Address</label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" autofocus class="w-full border border-slate-300 bg-white px-4 py-3 text-xs leading-7 text-slate-700 outline-none transition focus:border-[#A88444] sm:text-sm">
                                @error('email')
                                    <p class="mt-2 text-[12px] leading-6 text-red-600 sm:text-[14px]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="mb-3 block text-xs uppercase text-slate-500 sm:text-sm">Password</label>
                                <div class="relative">
                                    <input id="password" name="password" type="password" required autocomplete="current-password" class="w-full border border-slate-300 bg-white px-4 py-3 pr-14 text-xs leading-7 text-slate-700 outline-none transition focus:border-[#A88444] sm:text-sm">
                                    <button type="button" data-toggle-password="password" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 transition hover:text-[#A88444]" aria-label="Show password">
                                        <svg data-eye-open class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                        <svg data-eye-closed class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.58 10.58A2 2 0 0 0 12 14a2 2 0 0 0 1.42-.58"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.88 4.74A9.44 9.44 0 0 1 12 4.5c6 0 9.75 7.5 9.75 7.5a17.47 17.47 0 0 1-2.16 3.19"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6.61 6.61C3.78 8.49 2.25 12 2.25 12s3.75 7.5 9.75 7.5a9.7 9.7 0 0 0 4.39-1.04"/></svg>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-2 text-[12px] leading-6 text-red-600 sm:text-[14px]">{{ $message }}</p>
                                @enderror
                            </div>

                            <label class="flex items-center gap-3 text-xs leading-7 text-slate-700 sm:text-sm">
                                <input name="remember" type="checkbox" value="1" class="h-4 w-4 border-slate-300 text-[#A88444] focus:ring-[#A88444]">
                                <span>Keep me signed in</span>
                            </label>

                            <button type="submit" class="inline-flex w-full items-center justify-center bg-[#A88444] px-5 py-2.5 text-xs font-medium uppercase tracking-[0.08em] text-white transition hover:bg-[#B8945B] sm:text-sm">
                                Sign In
                            </button>

                            <div class="flex items-center gap-4 text-[9px] font-bold uppercase text-slate-400 sm:text-[11px]">
                                <span class="h-px flex-1 bg-slate-200"></span>
                                <span>Or</span>
                                <span class="h-px flex-1 bg-slate-200"></span>
                            </div>

                            <a href="{{ route('affiliate.social.redirect', 'google') }}" class="inline-flex w-full items-center justify-center gap-3 border border-slate-300 bg-white px-4 py-2.5 text-xs font-medium uppercase tracking-[0.08em] text-slate-700 transition hover:border-[#A88444] hover:text-[#A88444] sm:text-sm">
                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09Z" />
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23Z" />
                                    <path fill="#FBBC05" d="M5.84 14.1c-.22-.66-.35-1.36-.35-2.1s.13-1.44.35-2.1V7.06H2.18A10.96 10.96 0 0 0 1 12c0 1.77.42 3.45 1.18 4.94l3.66-2.84Z" />
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06L5.84 9.9C6.71 7.31 9.14 5.38 12 5.38Z" />
                                </svg>
                                Sign In With Google
                            </a>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
                button.addEventListener('click', function () {
                    const input = document.getElementById(button.getAttribute('data-toggle-password'));

                    if (!input) {
                        return;
                    }

                    const eyeOpen = button.querySelector('[data-eye-open]');
                    const eyeClosed = button.querySelector('[data-eye-closed]');
                    const isHidden = input.type === 'password';

                    input.type = isHidden ? 'text' : 'password';
                    eyeOpen?.classList.toggle('hidden', isHidden);
                    eyeClosed?.classList.toggle('hidden', !isHidden);
                    button.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
                });
            });
        });
    </script>
</x-layouts.app>

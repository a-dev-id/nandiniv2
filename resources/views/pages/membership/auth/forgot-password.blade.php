@push('meta')
<title>Forgot Password | Nandini Inner Circle</title>
<meta name="description" content="Request a password reset link for your Nandini Inner Circle membership account.">
@endpush

@php
$imageAlt = $page->hero_image_alt
?: $page->hero_mobile_image_alt
?: $page->title
?: 'Nandini Inner Circle Forgot Password Page';

@endphp

<x-layouts.app>
    <x-heroes.image-hero :page="$page" :alt-text="$imageAlt" />

    <section class="w-full bg-[#F7F7F7] py-20 md:py-28 lg:py-32">
        <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto w-full max-w-[560px] bg-white px-6 py-8 shadow-xl sm:px-8 md:px-10 md:py-10 lg:px-12">
                <div class="mb-8 text-center">
                    <p class="text-xs sm:text-sm uppercase text-[#A88444]">
                        Nandini Inner Circle
                    </p>

                    <h1 class="text-xl mt-4 leading-snug uppercase text-slate-700 font-medium mb-3 sm:text-2xl">
                        Forgot Password
                    </h1>

                    <p class="mx-auto mt-2 max-w-md text-xs leading-7 text-slate-600 sm:text-sm">
                        Enter your membership email address and we will send you a link to reset your password.
                    </p>
                </div>

                @if (session('status'))
                <div class="mb-6 border border-green-200 bg-green-50 px-4 py-3 text-[12px] leading-6 text-green-700 sm:text-[14px]">
                    {{ session('status') }}
                </div>
                @endif

                <form method="POST" action="{{ route('membership.password.email') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="mb-3 block text-xs sm:text-sm uppercase text-slate-500">
                            Email Address
                        </label>

                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" class="w-full border border-slate-300 bg-white px-4 py-3 text-xs leading-7 text-slate-700 outline-none transition focus:border-[#A88444] sm:text-sm">

                        @error('email')
                        <p class="mt-2 text-[12px] leading-6 text-red-600 sm:text-[14px]">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <x-recaptcha />

                    <button type="submit" class="inline-flex w-full items-center justify-center bg-[#A88444] px-5 py-2.5 text-xs font-medium uppercase text-white transition hover:bg-[#B8945B] tracking-[0.08em] sm:text-sm">
                        Send Reset Link
                    </button>

                    <div class="pt-2 text-center text-xs leading-7 text-slate-700 sm:text-sm">
                        Remember your password?

                        <a href="{{ route('membership.login') }}" class="font-medium uppercase text-[#A88444] transition hover:text-[#8F6B34] tracking-[0.08em]">
                            Sign In
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-layouts.app>

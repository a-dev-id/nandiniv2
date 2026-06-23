@push('meta')
<title>Reset Password | Nandini Inner Circle</title>
<meta name="description" content="Set a new password for your Nandini Inner Circle membership account.">
@endpush
@php
$imageAlt = $page->hero_image_alt
?: $page->hero_mobile_image_alt
?: $page->title
?: 'Nandini Inner Circle Reset Password Page';
@endphp

<x-layouts.app>
    <x-heroes.image-hero :page="$page" :alt-text="$imageAlt" />

    <section class="w-full bg-[#F7F7F7] py-20 md:py-28 lg:py-32">
        <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto w-full max-w-[560px] bg-white px-6 py-8 shadow-xl sm:px-8 md:px-10 md:py-10 lg:px-12">
                <div class="mb-8 text-center">
                    <p class="text-sm sm:text-sm uppercase text-[#A67C3D]">
                        Nandini Inner Circle
                    </p>

                    <h1 class="text-2xl mt-4 leading-snug uppercase text-slate-700 font-medium mb-3">
                        Reset Password
                    </h1>
                </div>

                @if ($errors->any())
                <div class="mb-6 border border-red-200 bg-red-50 px-4 py-3 text-[14px] leading-6 text-red-700">
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

                <form method="POST" action="{{ route('membership.password.store') }}" class="space-y-6">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div>
                        <label for="email" class="mb-3 block text-sm sm:text-sm uppercase text-slate-500">
                            Email Address
                        </label>

                        <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autofocus autocomplete="email" class="w-full border border-slate-300 bg-white px-4 py-3 text-sm leading-7 text-slate-700 outline-none transition focus:border-[#A67C3D]">
                    </div>

                    <div>
                        <label for="password" class="mb-3 block text-sm sm:text-sm uppercase text-slate-500">
                            New Password
                        </label>

                        <input id="password" type="password" name="password" required autocomplete="new-password" class="w-full border border-slate-300 bg-white px-4 py-3 text-sm leading-7 text-slate-700 outline-none transition focus:border-[#A67C3D]">
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-3 block text-sm sm:text-sm uppercase text-slate-500">
                            Confirm Password
                        </label>

                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="w-full border border-slate-300 bg-white px-4 py-3 text-sm leading-7 text-slate-700 outline-none transition focus:border-[#A67C3D]">
                    </div>

                    <button type="submit" class="inline-flex w-full items-center justify-center bg-[#A67C3D] px-5 py-2.5 text-sm font-medium uppercase text-white transition hover:bg-[#B8945B] tracking-[0.08em]">
                        Reset Password
                    </button>
                </form>
            </div>
        </div>
    </section>
</x-layouts.app>

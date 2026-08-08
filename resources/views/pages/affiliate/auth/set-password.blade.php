@push('meta')
<title>Set Password | Nandini Partner Circle</title>
<meta name="robots" content="noindex,nofollow">
@endpush

<x-layouts.affiliate>
    <section class="px-5 py-12 sm:px-8 sm:py-16 lg:px-10">
        <div class="mx-auto w-full max-w-xl">
            <p class="text-xs font-medium uppercase tracking-[0.12em] text-[#A88444] sm:text-sm">Nandini Partner Circle</p>
            <h1 class="mt-3 text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">Set your password</h1>
            <p class="mt-5 text-xs leading-relaxed text-gray-600 sm:text-sm">Create a password to access your affiliate portal.</p>
            <form method="POST" action="{{ route('affiliate.password.update') }}" class="mt-8 border border-slate-200 bg-white px-5 py-7 sm:px-8 sm:py-9">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div>
                    <label for="email" class="block text-xs font-medium uppercase tracking-[0.08em] text-slate-600 sm:text-sm">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required autocomplete="email" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#A88444] focus:ring-1 focus:ring-[#A88444]">
                    @error('email')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
                </div>
                <div class="mt-6">
                    <label for="password" class="block text-xs font-medium uppercase tracking-[0.08em] text-slate-600 sm:text-sm">New password</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#A88444] focus:ring-1 focus:ring-[#A88444]">
                    @error('password')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
                </div>
                <div class="mt-6">
                    <label for="password_confirmation" class="block text-xs font-medium uppercase tracking-[0.08em] text-slate-600 sm:text-sm">Confirm new password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm text-slate-900 outline-none focus:border-[#A88444] focus:ring-1 focus:ring-[#A88444]">
                </div>
                <button type="submit" class="mt-8 inline-flex w-full items-center justify-center border border-[#A88444] bg-[#A88444] px-6 py-3 text-xs font-medium uppercase tracking-[0.08em] text-white transition hover:bg-[#B8945B] sm:text-sm">Set Password</button>
            </form>
        </div>
    </section>
</x-layouts.affiliate>

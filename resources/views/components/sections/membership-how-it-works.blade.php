@props([
'title' => 'How It Works?',
'description' => 'Enjoy easy earning and the flexibility to choose how and when you want to be rewarded. Getting rewarded has never been easier!',
])

<section class="bg-white px-6 py-14 md:py-16">
    <div class="mx-auto max-w-[1500px]">
        @if ($title || $description)
        <div class="mx-auto mb-12 max-w-3xl text-center md:max-w-5xl">
            @if ($title)
            <h2 class="mb-6 font-serif text-2xl font-medium uppercase leading-snug tracking-[0.15em] text-slate-800 sm:text-3xl md:mb-8 md:text-4xl md:tracking-[0.25em]">
                {{ $title }}
            </h2>
            @endif

            @if ($description)
            <p class="mx-auto max-w-2xl text-[15px] leading-relaxed text-gray-600 sm:max-w-3xl sm:text-base md:max-w-5xl">
                {{ $description }}
            </p>
            @endif
        </div>
        @endif

        <div class="grid grid-cols-1 gap-10 text-center sm:grid-cols-2 lg:mt-20 lg:grid-cols-4 lg:gap-6">
            {{-- Join For Free --}}
            <div class="flex w-full flex-col items-center px-4">
                <div class="mb-6 text-[#B8945B]">
                    <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 21v-2a4 4 0 0 0-4-4h-4a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                </div>

                <h3 class="font-serif text-xl uppercase tracking-[0.18em] text-slate-950 md:text-2xl lg:whitespace-nowrap">
                    Join For Free
                </h3>

                <p class="mt-4 max-w-[320px] text-[15px] leading-relaxed text-gray-600 sm:text-base">
                    Join the program by signing up through our loyalty website.
                </p>
            </div>

            {{-- Book & Earn --}}
            <div class="flex w-full flex-col items-center px-4">
                <div class="mb-6 text-[#B8945B]">
                    <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                        <path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5z" />
                        <path d="M8 2v15" />
                    </svg>
                </div>

                <h3 class="font-serif text-xl uppercase tracking-[0.18em] text-slate-950 md:text-2xl lg:whitespace-nowrap">
                    Book &amp; Earn
                </h3>

                <p class="mt-4 max-w-[320px] text-[15px] leading-relaxed text-gray-600 sm:text-base">
                    Book directly through our website and earn points instantly.
                </p>
            </div>

            {{-- Stay & Upgrade --}}
            <div class="flex w-full flex-col items-center px-4">
                <div class="mb-6 text-[#B8945B]">
                    <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 19V5" />
                        <path d="M5 12l7-7 7 7" />
                    </svg>
                </div>

                <h3 class="font-serif text-xl uppercase tracking-[0.18em] text-slate-950 md:text-2xl lg:whitespace-nowrap">
                    Stay &amp; Upgrade
                </h3>

                <p class="mt-4 max-w-[320px] text-[15px] leading-relaxed text-gray-600 sm:text-base">
                    Earn qualifying nights and level up to unlock more exclusive rewards.
                </p>
            </div>

            {{-- Redeem & Enjoy --}}
            <div class="flex w-full flex-col items-center px-4">
                <div class="mb-6 text-[#B8945B]">
                    <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 12v10H4V12" />
                        <path d="M2 7h20v5H2z" />
                        <path d="M12 22V7" />
                        <path d="M12 7H7.5A2.5 2.5 0 1 1 10 4.5L12 7z" />
                        <path d="M12 7h4.5A2.5 2.5 0 1 0 14 4.5L12 7z" />
                    </svg>
                </div>

                <h3 class="font-serif text-xl uppercase tracking-[0.18em] text-slate-950 md:text-2xl lg:whitespace-nowrap">
                    Redeem &amp; Enjoy
                </h3>

                <p class="mt-4 max-w-[320px] text-[15px] leading-relaxed text-gray-600 sm:text-base">
                    Use your points for stays and unlock all the perks.
                </p>
            </div>
        </div>
    </div>
</section>
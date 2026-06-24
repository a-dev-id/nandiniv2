@props([
'title' => 'How To Earn Points',
])

<section class="bg-slate-100 px-6 py-14 md:py-20">
    <div class="mx-auto max-w-[1500px]">
        <div class="mx-auto max-w-3xl text-center md:max-w-5xl">
            <h2 class="text-lg font-medium uppercase leading-snug text-slate-700 mb-3 sm:text-xl">
                {{ $title }}
            </h2>
        </div>

        <div class="mx-auto grid max-w-6xl grid-cols-1 gap-12 text-center md:grid-cols-3 md:gap-16">
            {{-- Hotel Stay --}}
            <div class="flex flex-col items-center">
                <div class="mb-6 text-[#B8945B]">
                    <svg class="h-12 w-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 10.5L12 3l9 7.5" />
                        <path d="M5 9v12h14V9" />
                        <path d="M10 21v-7h4v7" />
                    </svg>
                </div>

                <h3 class="text-base uppercase text-slate-700 leading-snug mb-3 sm:text-lg">
                    Hotel Stay
                </h3>

                <p class="mt-2 max-w-[300px] text-xs leading-relaxed text-gray-600 sm:text-sm">
                    Earn point for night spent in Nandini Jungle
                </p>
            </div>

            {{-- Food & Beverages --}}
            <div class="flex flex-col items-center">
                <div class="mb-6 text-[#B8945B]">
                    <svg class="h-12 w-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 5h10v9a5 5 0 0 1-10 0V5z" />
                        <path d="M15 8h2.5a3.5 3.5 0 0 1 0 7H15" />
                        <path d="M4 21h15" />
                    </svg>
                </div>

                <h3 class="text-base uppercase text-slate-700 leading-snug mb-3 sm:text-lg">
                    Food &amp; Beverages
                </h3>

                <p class="mt-2 max-w-[340px] text-xs leading-relaxed text-gray-600 sm:text-sm">
                    Enhance your travel and earn points for activities everyone will love
                </p>
            </div>

            {{-- Spa & Wellness --}}
            <div class="flex flex-col items-center">
                <div class="mb-6 text-[#B8945B]">
                    <svg class="h-12 w-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z" />
                    </svg>
                </div>

                <h3 class="text-base uppercase text-slate-700 leading-snug mb-3 sm:text-lg">
                    Spa &amp; Wellness
                </h3>

                <p class="mt-2 max-w-[300px] text-xs leading-relaxed text-gray-600 sm:text-sm">
                    Earn on the go and while you spend
                </p>
            </div>
        </div>
    </div>
</section>

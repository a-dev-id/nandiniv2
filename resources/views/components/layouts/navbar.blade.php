<div>
    <nav id="mainNavbar" class="fixed inset-x-0 top-0 z-50 bg-black/35 text-white transition-all duration-300">
        <div class="w-full px-4 sm:px-6 md:px-10 2xl:px-14 relative">
            <div id="navInner" class="flex items-center h-20 lg:h-28 transition-all duration-300">

                {{-- LEFT --}}
                <div id="navLeft" class="flex items-center gap-4 lg:gap-6 transition-colors duration-300">
                    <button id="btnMenu" type="button" class="inline-flex items-center gap-3 transition-colors duration-300" aria-label="Open menu">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
                        </svg>
                        <span class="hidden sm:inline text-[16px] font-medium uppercase">Menu</span>
                    </button>

                    <div id="navIcons" class="hidden md:flex items-center gap-5">
                        <a href="tel:+623618983111" class="hover:opacity-100" aria-label="Call">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
                                <path d="M3.51089 2L7.15002 2.13169C7.91653 2.15942 8.59676 2.64346 8.89053 3.3702L9.96656 6.03213C10.217 6.65159 10.1496 7.35837 9.78693 7.91634L8.40831 10.0375C9.22454 11.2096 11.4447 13.9558 13.7955 15.5633L15.5484 14.4845C15.9939 14.2103 16.5273 14.1289 17.0314 14.2581L20.5161 15.1517C21.4429 15.3894 22.0674 16.2782 21.9942 17.2552L21.7705 20.2385C21.6919 21.2854 20.8351 22.1069 19.818 21.9887C6.39245 20.4276 -1.48056 1.99997 3.51089 2Z" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </a>

                        <a href="mailto:reservation@nandinihanginggardens.com" class="hover:opacity-100" aria-label="Email">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 8L8.44992 11.6333C9.73295 12.4886 10.3745 12.9163 11.0678 13.0825C11.6806 13.2293 12.3194 13.2293 12.9322 13.0825C13.6255 12.9163 14.2671 12.4886 15.5501 11.6333L21 8M6.2 19H17.8C18.9201 19 19.4802 19 19.908 18.782C20.2843 18.5903 20.5903 18.2843 20.782 17.908C21 17.4802 21 16.9201 21 15.8V8.2C21 7.0799 21 6.51984 20.782 6.09202C20.5903 5.71569 20.2843 5.40973 19.908 5.21799C19.4802 5 18.9201 5 17.8 5H6.2C5.0799 5 4.51984 5 4.09202 5.21799C3.71569 5.40973 3.40973 5.71569 3.21799 6.09202C3 6.51984 3 7.07989 3 8.2V15.8C3 16.9201 3 17.4802 3.21799 17.908C3.40973 18.2843 3.71569 18.5903 4.09202 18.782C4.51984 19 5.07989 19 6.2 19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- CENTER LOGO --}}
                <a href="{{ route('home') }}" class="absolute left-1/2 -translate-x-1/2 flex items-center justify-center">
                    <img id="navLogo" src="{{ asset('images/logo-njhg.png') }}" class="h-14 sm:h-16 lg:h-24 w-auto brightness-0 invert transition-all duration-300" alt="Nandini Jungle" />
                </a>

                {{-- RIGHT --}}
                <div class="ml-auto flex items-center gap-3 sm:gap-4">
                    {{-- Member: desktop/tablet only --}}
                    <button id="navMemberBtn" type="button" data-open-membership-panel class="hidden sm:inline-flex items-center justify-center border transition duration-300 uppercase tracking-[0.2em]
                               text-[10px] sm:text-[16px] font-medium px-3 sm:px-6 lg:px-8 py-1.5 sm:py-2.5 lg:py-3
                               bg-transparent border-white text-white hover:bg-white hover:border-white hover:text-slate-800">
                        INNER CIRCLE
                    </button>

                    {{-- Book --}}
                    <div class="relative">
                        <button id="navBookBtn" type="button" class="inline-flex items-center justify-center border transition duration-300 uppercase tracking-[0.2em]
                                   text-[10px] sm:text-[16px] font-medium px-3 sm:px-6 lg:px-8 py-1.5 sm:py-2.5 lg:py-3
                                   bg-white border-white text-slate-800 hover:bg-[#B8945B] hover:border-[#B8945B] hover:text-white">
                            <span class="sm:hidden">Book</span>
                            <span class="hidden sm:inline">Book Now</span>
                        </button>

                        {{-- Dropdown --}}
                        <div id="navBookMenu" class="absolute right-0 mt-2 w-52 bg-white border border-white shadow-xl hidden">
                            <a href="https://nandinijunglebyhanginggardens.reserve-online.net/?checkin=today" class="block text-center uppercase tracking-[0.2em] font-medium text-[12px] sm:text-[14px] px-6 py-4 bg-white text-slate-800 hover:bg-[#B8945B] hover:border-[#B8945B] hover:text-white">
                                Book Direct
                            </a>

                            <a href="https://ovs.tour-list.com/DPSearch/?HotelCode=nandinihgs" class="block text-center uppercase tracking-[0.2em] font-medium text-[12px] sm:text-[14px] px-6 py-4 bg-white text-slate-800 hover:bg-[#B8945B] hover:border-[#B8945B] hover:text-white">
                                Room + Flight
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </nav>

    {{-- BACKDROP --}}
    <div id="offcanvasBackdrop" class="fixed inset-0 z-[60] bg-black/50 hidden"></div>

    @php
    $loginUrl = \Illuminate\Support\Facades\Route::has('membership.login')
    ? route('membership.login')
    : url('/membership/sign-in');

    $registerUrl = \Illuminate\Support\Facades\Route::has('membership.register')
    ? route('membership.register')
    : url('/membership/join');

    $dashboardUrl = \Illuminate\Support\Facades\Route::has('membership.dashboard')
    ? route('membership.dashboard')
    : url('/membership/dashboard');

    $logoutUrl = \Illuminate\Support\Facades\Route::has('membership.logout')
    ? route('membership.logout')
    : url('/membership/logout');

    $socialLinks = [
    'instagram' => 'https://www.instagram.com/nandinijungleresort/',
    'facebook' => 'https://www.facebook.com/nandinijungleresort/',
    'youtube' => 'https://www.youtube.com/@NandiniJunglebyHangingGardens',
    'tripadvisor' => 'https://www.tripadvisor.com/Hotel_Review-g21379722-d603743-Reviews-Nandini_Jungle_By_Hanging_Gardens-Buahan_Payangan_Gianyar_Regency_Bali.html',
    ];
    @endphp

    {{-- OFFCANVAS --}}
    <aside id="offcanvasMenu" class="fixed top-0 left-0 z-[70] h-dvh w-[78vw] max-w-[330px] bg-white text-slate-800 shadow-2xl -translate-x-full will-change-transform transition-transform duration-300 ease-out overflow-hidden">
        <div class="h-full flex flex-col">

            {{-- HEADER --}}
            <div class="relative px-7 pt-8 pb-6 shrink-0">
                <button id="btnCloseMenu" type="button" aria-label="Close menu" class="absolute right-5 top-5 text-slate-500 hover:text-slate-800">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" d="M6 6l12 12M18 6L6 18" />
                    </svg>
                </button>

                <div class="flex items-center justify-center">
                    <img src="{{ asset('images/logo-njhg.png') }}" alt="Nandini Jungle by Hanging Gardens" class="w-52 h-auto" loading="lazy" />
                </div>

                <div class="mt-6 h-px bg-slate-300/70"></div>
            </div>

            {{-- LINKS --}}
            <div class="px-7 pb-8 grow overflow-y-auto min-h-0">
                <nav class="space-y-5 text-left">
                    <a href="{{ route('home') }}" class="block text-[16px] leading-6 font-medium uppercase text-left">
                        Home
                    </a>

                    <a href="{{ route('holy-river.index') }}" class="block text-[16px] leading-6 font-medium uppercase text-left">
                        Holy River
                    </a>

                    {{-- Dropdown: Offers & Experiences --}}
                    <div>
                        <button type="button" class="w-full flex items-start justify-between gap-3 text-[16px] leading-6 font-medium uppercase text-left" data-oc-toggle="ocOffers" aria-expanded="false">
                            <span class="leading-6 text-left">Offers &amp; Experiences</span>
                            <svg data-oc-icon class="h-4 w-4 text-slate-500 shrink-0 mt-1 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>

                        <div id="ocOffers" data-oc-panel class="overflow-hidden text-left transition-all duration-300 ease-out" style="max-height: 0px; opacity: 0;">
                            <div class="pt-6 pb-5 ml-7 space-y-5">
                                <a href="{{ route('offers.index') }}" class="block text-[14px] leading-6 uppercase font-medium tracking-wide text-slate-600 hover:text-[#B8945B] text-left">
                                    Offers
                                </a>

                                <a href="{{ route('experiences.index') }}" class="block text-[14px] leading-6 uppercase font-medium tracking-wide text-slate-600 hover:text-[#B8945B] text-left">
                                    Experiences
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Dropdown: Accommodations --}}
                    <div>
                        <button type="button" class="w-full flex items-start justify-between gap-3 text-[16px] leading-6 font-medium uppercase text-left" data-oc-toggle="ocVillas" aria-expanded="false">
                            <span class="leading-6 text-left">Accommodations</span>
                            <svg data-oc-icon class="h-4 w-4 text-slate-500 shrink-0 mt-1 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>

                        <div id="ocVillas" data-oc-panel class="overflow-hidden text-left transition-all duration-300 ease-out" style="max-height: 0px; opacity: 0;">
                            <div class="pt-6 pb-5 ml-7 space-y-5">
                                <a href="{{ route('accommodations.suites') }}" class="block text-[14px] leading-6 uppercase font-medium tracking-wide text-slate-600 hover:text-[#B8945B] text-left">
                                    The Royal Suites
                                </a>

                                <a href="{{ route('accommodations.villas') }}" class="block text-[14px] leading-6 uppercase font-medium tracking-wide text-slate-600 hover:text-[#B8945B] text-left">
                                    Jungle Villas
                                </a>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('little-things.index') }}" class="block text-[16px] leading-6 font-medium uppercase text-left">
                        The Little Things
                    </a>

                    {{-- Dropdown: More --}}
                    <div>
                        <button type="button" class="w-full flex items-start justify-between gap-3 text-[16px] leading-6 font-medium uppercase text-left" data-oc-toggle="ocMore" aria-expanded="false">
                            <span class="leading-6 text-left">More</span>
                            <svg data-oc-icon class="h-4 w-4 text-slate-500 shrink-0 mt-1 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>

                        <div id="ocMore" data-oc-panel class="overflow-hidden text-left transition-all duration-300 ease-out" style="max-height: 0px; opacity: 0;">
                            <div class="pt-6 pb-5 ml-7 space-y-5">
                                <a href="{{ url('/honeymoon') }}" class="block text-[14px] leading-6 uppercase font-medium tracking-wide text-slate-600 hover:text-[#B8945B] text-left">
                                    Honeymoon
                                </a>

                                <a href="{{ route('dining.index') }}" class="block text-[14px] leading-6 uppercase font-medium tracking-wide text-slate-600 hover:text-[#B8945B] text-left">
                                    Dining
                                </a>

                                <a href="{{ route('spa.index') }}" class="block text-[14px] leading-6 uppercase font-medium tracking-wide text-slate-600 hover:text-[#B8945B] text-left">
                                    Spa &amp; Wellness
                                </a>

                                <a href="{{ route('wedding.index') }}" class="block text-[14px] leading-6 uppercase font-medium tracking-wide text-slate-600 hover:text-[#B8945B] text-left">
                                    Wedding
                                </a>

                                <a href="{{ route('about-us.index') }}" class="block text-[14px] leading-6 uppercase font-medium tracking-wide text-slate-600 hover:text-[#B8945B] text-left">
                                    About Us
                                </a>

                                <a href="{{ route('blog.index') }}" class="block text-[14px] leading-6 uppercase font-medium tracking-wide text-slate-600 hover:text-[#B8945B] text-left">
                                    Blog & News
                                </a>

                                <a href="{{ route('awards.index') }}" class="block text-[14px] leading-6 uppercase font-medium tracking-wide text-slate-600 hover:text-[#B8945B] text-left">
                                    Awards
                                </a>

                                <a href="{{ route('contact.index') }}" class="block text-[14px] leading-6 uppercase font-medium tracking-wide text-slate-600 hover:text-[#B8945B] text-left">
                                    Contact
                                </a>

                                <a href="{{ route('gallery.index') }}" class="block text-[14px] leading-6 uppercase font-medium tracking-wide text-slate-600 hover:text-[#B8945B] text-left">
                                    Gallery
                                </a>

                                <a href="{{ route('faq.index') }}" class="block text-[14px] leading-6 uppercase font-medium tracking-wide text-slate-600 hover:text-[#B8945B] text-left">
                                    FAQ
                                </a>

                                <a href="{{ route('sustainability.index') }}" class="block text-[14px] leading-6 uppercase font-medium tracking-wide text-slate-600 hover:text-[#B8945B] text-left">
                                    Sustainability
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Dropdown: Inner Circle - Mobile only --}}
                    <div class="sm:hidden">
                        <button type="button" class="w-full flex items-start justify-between gap-3 text-[16px] leading-6 font-medium uppercase text-left" data-oc-toggle="ocMembership" aria-expanded="false">
                            <span class="leading-6 text-left">Inner Circle</span>
                            <svg data-oc-icon class="h-4 w-4 text-slate-500 shrink-0 mt-1 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>

                        <div id="ocMembership" data-oc-panel class="overflow-hidden text-left transition-all duration-300 ease-out" style="max-height: 0px; opacity: 0;">
                            <div class="pt-6 pb-5 ml-7 space-y-5">
                                <a href="{{ route('membership.index') }}" class="block text-[14px] leading-6 uppercase font-medium tracking-wide text-slate-600 hover:text-[#B8945B] text-left">
                                    About Inner Circle
                                </a>

                                <a href="{{ route('membership.benefits') }}" class="block text-[14px] leading-6 uppercase font-medium tracking-wide text-slate-600 hover:text-[#B8945B] text-left">
                                    Benefits
                                </a>

                                <a href="{{ route('membership.privilege-redemption') }}" class="block text-[14px] leading-6 uppercase font-medium tracking-wide text-slate-600 hover:text-[#B8945B] text-left">
                                    Redemption
                                </a>

                                @auth('member')
                                <a href="{{ $dashboardUrl }}" class="block text-[14px] leading-6 uppercase font-medium tracking-wide text-slate-600 hover:text-[#B8945B] text-left">
                                    Dashboard
                                </a>

                                <form method="POST" action="{{ $logoutUrl }}">
                                    @csrf

                                    <button type="submit" class="block w-full text-[14px] leading-6 uppercase font-medium tracking-wide text-slate-600 hover:text-[#B8945B] text-left">
                                        Logout
                                    </button>
                                </form>
                                @else
                                <a href="{{ $loginUrl }}" class="block text-[14px] leading-6 uppercase font-medium tracking-wide text-slate-600 hover:text-[#B8945B] text-left">
                                    Sign In
                                </a>

                                <a href="{{ $registerUrl }}" class="block text-[14px] leading-6 uppercase font-medium tracking-wide text-slate-600 hover:text-[#B8945B] text-left">
                                    Join Now
                                </a>
                                @endauth
                            </div>
                        </div>
                    </div>

                </nav>
            </div>

            {{-- FOOTER --}}
            <div class="px-7 pb-8 shrink-0">
                <div class="h-px bg-slate-300/70 mb-6"></div>

                <div class="flex items-center gap-4">
                    <a href="{{ $socialLinks['instagram'] }}" target="_blank" rel="noopener" aria-label="Instagram" class="hover:opacity-80">
                        <svg class="w-6 h-6 fill-black" viewBox="0 0 24 24">
                            <path d="M7.75 2C4.574 2 2 4.574 2 7.75v8.5C2 19.426 4.574 22 7.75 22h8.5C19.426 22 22 19.426 22 16.25v-8.5C22 4.574 19.426 2 16.25 2h-8.5zm0 2h8.5C18.321 4 20 5.679 20 7.75v8.5c0 2.071-1.679 3.75-3.75 3.75h-8.5C5.679 20 4 18.321 4 16.25v-8.5C4 5.679 5.679 4 7.75 4zm4.25 2.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zm0 2a3.5 3.5 0 110 7 3.5 3.5 0 010-7zm4.75-.75a1 1 0 100 2 1 1 0 000-2z" />
                        </svg>
                    </a>

                    <a href="{{ $socialLinks['facebook'] }}" target="_blank" rel="noopener" aria-label="Facebook" class="hover:opacity-80">
                        <svg class="w-6 h-6 fill-black" viewBox="0 0 24 24">
                            <path d="M22 12a10 10 0 10-11.5 9.875V15.5H8.5V12h2V9.75C10.5 7.57 11.93 6 14.5 6c1.22 0 2.5.22 2.5.22v2.75H15.6c-1.38 0-1.8.86-1.8 1.74V12h3.06l-.49 3.5H13.8v6.375A10 10 0 0022 12z" />
                        </svg>
                    </a>

                    <a href="{{ $socialLinks['youtube'] }}" target="_blank" rel="noopener" aria-label="YouTube" class="hover:opacity-80">
                        <svg class="w-6 h-6 fill-black" viewBox="0 0 24 24">
                            <path d="M23.498 6.186a2.997 2.997 0 00-2.11-2.12C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.388.566a2.997 2.997 0 00-2.11 2.12C0 8.075 0 12 0 12s0 3.925.502 5.814a2.997 2.997 0 002.11 2.12C4.5 20.5 12 20.5 12 20.5s7.5 0 9.388-.566a2.997 2.997 0 002.11-2.12C24 15.925 24 12 24 12s0-3.925-.502-5.814zM9.75 15.568V8.432L15.818 12 9.75 15.568z" />
                        </svg>
                    </a>

                    <a href="{{ $socialLinks['tripadvisor'] }}" target="_blank" rel="noopener" aria-label="Tripadvisor" class="hover:opacity-80">
                        <svg class="w-6 h-6 fill-black" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 6.009h-2.829C15.211 4.675 12.813 4 10 4s-5.212.675-7.171 2.009H0c.428.42.827 1.34.993 2.04A4.954 4.954 0 0 0 0 11.008c0 2.757 2.243 5 5 5a4.97 4.97 0 0 0 3.423-1.375L10 17l1.577-2.366A4.97 4.97 0 0 0 15 16.01c2.757 0 5-2.243 5-5 0-1.112-.377-2.13-.993-2.96.166-.7.565-1.62.993-2.04zm-15 8.4c-1.875 0-3.4-1.525-3.4-3.4s1.525-3.4 3.4-3.4 3.4 1.525 3.4 3.4-1.525 3.4-3.4 3.4zm5-3.4a5.008 5.008 0 0 0-4.009-4.9C7.195 5.704 8.53 5.5 10 5.5s2.805.204 4.009.61A5.008 5.008 0 0 0 10 11.008zm5 3.4c-1.875 0-3.4-1.525-3.4-3.4s1.525-3.4 3.4-3.4 3.4 1.525 3.4 3.4-1.525 3.4-3.4 3.4zM5 8.86c-1.185 0-2.15.964-2.15 2.15s.965 2.15 2.15 2.15 2.15-.964 2.15-2.15-.965-2.15-2.15-2.15zm0 2.791a.65.65 0 1 1 0-1.3.65.65 0 0 1 0 1.3zm10-2.791c-1.185 0-2.15.964-2.15 2.15s.965 2.15 2.15 2.15 2.15-.964 2.15-2.15-.965-2.15-2.15-2.15zm0 2.791a.65.65 0 1 1 0-1.3.65.65 0 0 1 0 1.3z" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </aside>

    {{-- MEMBERSHIP RIGHT PANEL --}}
    <aside id="membershipPanel" class="fixed top-0 right-0 z-[70] h-dvh w-[86%] max-w-sm bg-white text-slate-800 shadow-2xl translate-x-full will-change-transform transition-transform duration-300 ease-out">
        <div class="h-full flex flex-col">

            {{-- HEADER --}}
            <div class="relative px-7 pt-8 pb-6">
                <button id="btnCloseMembershipPanel" type="button" aria-label="Close membership panel" class="absolute left-5 top-5 text-slate-500 hover:text-slate-800">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" d="M6 6l12 12M18 6L6 18" />
                    </svg>
                </button>

                <div class="flex items-center justify-center">
                    <img src="{{ asset('images/logo-njhg.png') }}" alt="Nandini Jungle by Hanging Gardens" class="w-52 h-auto" loading="lazy" />
                </div>

                <div class="mt-6 h-px bg-slate-300/70"></div>
            </div>

            {{-- LINKS --}}
            <div class="px-7 pb-8 grow overflow-y-auto">
                <nav class="space-y-6 text-left">
                    <a href="{{ route('membership.index') }}" class="block text-[16px] font-medium uppercase text-left">
                        About Inner Circle
                    </a>

                    <a href="{{ route('membership.benefits') }}" class="block text-[16px] font-medium uppercase text-left">
                        Benefits
                    </a>

                    <a href="{{ route('membership.privilege-redemption') }}" class="block text-[16px] font-medium uppercase text-left">
                        Redemption
                    </a>

                    <div class="pt-2">
                        <div class="h-px bg-slate-300/70"></div>
                    </div>

                    @auth('member')
                    <div class="grid grid-cols-1 gap-3 pt-1">
                        <a href="{{ $dashboardUrl }}" class="inline-flex w-full items-center justify-center border border-[#B8945B] bg-[#B8945B] px-6 py-3
                                           text-[13px] font-bold uppercase tracking-[0.22em] text-white transition duration-300
                                           hover:bg-[#a37e45] hover:border-[#a37e45]">
                            Dashboard
                        </a>

                        <form method="POST" action="{{ $logoutUrl }}" class="w-full">
                            @csrf

                            <button type="submit" class="inline-flex w-full items-center justify-center border border-slate-800 bg-transparent px-6 py-3
                                               text-[13px] font-bold uppercase tracking-[0.22em] text-slate-800 transition duration-300
                                               hover:border-[#B8945B] hover:bg-[#B8945B] hover:text-white">
                                Logout
                            </button>
                        </form>
                    </div>
                    @else
                    <div class="grid grid-cols-1 gap-3 pt-1">
                        <a href="{{ $loginUrl }}" class="inline-flex w-full items-center justify-center border border-slate-800 bg-transparent px-6 py-3
                                           text-[13px] font-bold uppercase tracking-[0.22em] text-slate-800 transition duration-300
                                           hover:border-[#B8945B] hover:bg-[#B8945B] hover:text-white">
                            Sign In
                        </a>

                        <a href="{{ $registerUrl }}" class="inline-flex w-full items-center justify-center border border-[#B8945B] bg-[#B8945B] px-6 py-3
                                           text-[13px] font-bold uppercase tracking-[0.22em] text-white transition duration-300
                                           hover:bg-[#a37e45] hover:border-[#a37e45]">
                            Join Now
                        </a>
                    </div>
                    @endauth
                </nav>
            </div>

        </div>
    </aside>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const navMemberBtn = document.getElementById('navMemberBtn');

            const btnMenu = document.getElementById('btnMenu');
            const btnCloseMenu = document.getElementById('btnCloseMenu');
            const offcanvasMenu = document.getElementById('offcanvasMenu');

            const membershipPanel = document.getElementById('membershipPanel');
            const btnCloseMembershipPanel = document.getElementById('btnCloseMembershipPanel');
            const offcanvasBackdrop = document.getElementById('offcanvasBackdrop');
            const openMembershipButtons = document.querySelectorAll('[data-open-membership-panel]');

            function updateMemberButtonOnScroll() {
                if (!navMemberBtn) {
                    return;
                }

                const isScrolled = window.scrollY > 50;

                navMemberBtn.classList.remove(
                    'bg-[#B8945B]',
                    'border-[#B8945B]',
                    'text-white',
                    'hover:bg-[#a37e45]',
                    'hover:border-[#a37e45]',
                    'bg-transparent',
                    'border-white',
                    'hover:bg-white',
                    'hover:border-white',
                    'hover:text-slate-800',
                    'border-slate-950',
                    'text-slate-950',
                    'hover:bg-[#B8945B]',
                    'hover:border-[#B8945B]',
                    'hover:text-white'
                );

                if (isScrolled) {
                    navMemberBtn.classList.add(
                        'bg-transparent',
                        'border-slate-950',
                        'text-slate-950',
                        'hover:bg-[#B8945B]',
                        'hover:border-[#B8945B]',
                        'hover:text-white'
                    );
                } else {
                    navMemberBtn.classList.add(
                        'bg-transparent',
                        'border-white',
                        'text-white',
                        'hover:bg-white',
                        'hover:border-white',
                        'hover:text-slate-800'
                    );
                }
            }

            function showOverlay() {
                if (!offcanvasBackdrop) {
                    return;
                }

                offcanvasBackdrop.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function hideOverlay() {
                if (!offcanvasBackdrop) {
                    return;
                }

                offcanvasBackdrop.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            function isLeftMenuOpen() {
                return offcanvasMenu && !offcanvasMenu.classList.contains('-translate-x-full');
            }

            function isMembershipPanelOpen() {
                return membershipPanel && !membershipPanel.classList.contains('translate-x-full');
            }

            function openLeftMenu(event) {
                if (event) {
                    event.preventDefault();
                }

                if (!offcanvasMenu) {
                    return;
                }

                closeMembershipPanel(false);

                offcanvasMenu.classList.remove('-translate-x-full');
                showOverlay();
            }

            function closeLeftMenu(shouldHideOverlay = true) {
                if (!offcanvasMenu) {
                    return;
                }

                offcanvasMenu.classList.add('-translate-x-full');

                if (shouldHideOverlay && !isMembershipPanelOpen()) {
                    hideOverlay();
                }
            }

            function openMembershipPanel(event) {
                if (event) {
                    event.preventDefault();
                }

                if (!membershipPanel) {
                    return;
                }

                closeLeftMenu(false);

                membershipPanel.classList.remove('translate-x-full');
                showOverlay();
            }

            function closeMembershipPanel(shouldHideOverlay = true) {
                if (!membershipPanel) {
                    return;
                }

                membershipPanel.classList.add('translate-x-full');

                if (shouldHideOverlay && !isLeftMenuOpen()) {
                    hideOverlay();
                }
            }

            function closeDropdown(toggle, panel) {
                panel.dataset.open = 'false';
                panel.style.maxHeight = '0px';
                panel.style.opacity = '0';

                toggle.setAttribute('aria-expanded', 'false');

                const icon = toggle.querySelector('[data-oc-icon]');
                if (icon) {
                    icon.classList.remove('rotate-180');
                }
            }

            function openDropdown(toggle, panel) {
                panel.dataset.open = 'true';
                panel.style.maxHeight = panel.scrollHeight + 'px';
                panel.style.opacity = '1';

                toggle.setAttribute('aria-expanded', 'true');

                const icon = toggle.querySelector('[data-oc-icon]');
                if (icon) {
                    icon.classList.add('rotate-180');
                }
            }

            if (btnMenu) {
                btnMenu.addEventListener('click', openLeftMenu);
            }

            if (btnCloseMenu) {
                btnCloseMenu.addEventListener('click', function () {
                    closeLeftMenu();
                });
            }

            openMembershipButtons.forEach(function (button) {
                button.addEventListener('click', openMembershipPanel);
            });

            if (btnCloseMembershipPanel) {
                btnCloseMembershipPanel.addEventListener('click', function () {
                    closeMembershipPanel();
                });
            }

            if (offcanvasBackdrop) {
                offcanvasBackdrop.addEventListener('click', function () {
                    closeLeftMenu(false);
                    closeMembershipPanel(false);
                    hideOverlay();
                });
            }

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeLeftMenu(false);
                    closeMembershipPanel(false);
                    hideOverlay();
                }
            });

            document.querySelectorAll('[data-oc-toggle]').forEach(function (toggle) {
                toggle.addEventListener('click', function () {
                    const targetId = toggle.getAttribute('data-oc-toggle');
                    const panel = document.getElementById(targetId);

                    if (!panel) {
                        return;
                    }

                    const isOpen = panel.dataset.open === 'true';

                    if (isOpen) {
                        closeDropdown(toggle, panel);
                    } else {
                        openDropdown(toggle, panel);
                    }
                });
            });

            window.addEventListener('resize', function () {
                document.querySelectorAll('[data-oc-panel]').forEach(function (panel) {
                    if (panel.dataset.open === 'true') {
                        panel.style.maxHeight = panel.scrollHeight + 'px';
                    }
                });
            });

            updateMemberButtonOnScroll();
            window.addEventListener('scroll', updateMemberButtonOnScroll);
        });
    </script>
</div>

<footer class="bg-black text-white">
    <div class="mx-auto w-11/12 2xl:w-9/12">

        {{-- TOP --}}
        <div class="py-12">

            {{-- DESKTOP LAYOUT (lg+) --}}
            <div class="hidden lg:grid lg:grid-cols-12 lg:gap-10">

                {{-- LOGO --}}
                <div class="lg:col-span-2 flex items-start">
                    <a href="/" class="inline-flex">
                        <img src="{{ asset('images/logo-njhg.png') }}" alt="Nandini Jungle by Hanging Gardens" class="w-36 lg:w-44 h-auto max-h-52 shrink-0 brightness-0 invert" loading="lazy" />
                    </a>
                </div>

                {{-- ADDRESS --}}
                <div class="lg:col-span-3">
                    <h3 class="text-sm tracking-[0.22em] lg:text-lg uppercase mb-4">Address</h3>

                    <div class="text-sm leading-7 text-white/90">
                        Banjar Susut, Desa Buahan, Payangan, Bali 80571, Indonesia
                    </div>

                    <div class="mt-8">
                        <h3 class="text-sm tracking-[0.22em] lg:text-lg uppercase mb-4">Phone</h3>

                        <div class="text-sm leading-7 text-white/90">
                            <div class="mb-6">
                                <div class="text-white/70">Resort:</div>
                                <a href="tel:+623618983111" class="hover:underline">+62 361 89 83 111</a>
                            </div>

                            <div>
                                <div class="text-white/70">Reservations:</div>
                                <a href="tel:+6281236871170" class="hover:underline">+62 812 3687 1170</a>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <h3 class="text-sm tracking-[0.22em] lg:text-lg uppercase mb-4">Email</h3>
                        <a href="mailto:reservation@nandinibali.com" class="text-sm text-white/90 hover:underline">
                            reservation@nandinibali.com
                        </a>
                    </div>
                </div>

                {{-- ABOUT --}}
                <div class="lg:col-span-2">
                    <h3 class="text-sm tracking-[0.22em] lg:text-lg uppercase mb-4">About</h3>
                    <ul class="space-y-3 text-sm text-white/90">
                        <li><a href="/about" class="hover:underline">About Us</a></li>
                        <li><a href="/blog" class="hover:underline">Blog &amp; News</a></li>
                        <li><a href="/awards" class="hover:underline">Awards</a></li>
                        <li><a href="/gallery" class="hover:underline">Gallery</a></li>
                        <li><a href="/press" class="hover:underline">Press Room</a></li>
                        <li><a href="/contact" class="hover:underline">Contact Us</a></li>
                    </ul>
                </div>

                {{-- OTHERS --}}
                <div class="lg:col-span-2">
                    <h3 class="text-sm tracking-[0.22em] lg:text-lg uppercase mb-4">Others</h3>
                    <ul class="space-y-3 text-sm text-white/90">
                        <li>
                            <a href="/sustainability" class="hover:underline text-green-700 flex items-center gap-1 font-medium">
                                <svg height="16px" width="16px" version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve" fill="#35D39B">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                    <g id="SVGRepo_iconCarrier">
                                        <style type="text/css">
                                            .st0 {
                                                fill: #008236;
                                            }

                                        </style>
                                        <g>
                                            <path class="st0" d="M74.96,407.064c3.061-82.464,41.669-165,106.747-225.632c39.177-36.509,84.732-62.947,135.396-78.57 c0.738-0.232,1.482-0.4,2.228-0.617c-0.697-2.003-1.266-4.07-2.027-6.033C301.112,54.055,270.837,21.384,233.206,0 c-41.725,12.947-80.005,35.812-112.236,67.626c-32.158,31.798-58.1,72.425-73.186,119.83 c-15.062,47.389-17.033,94.834-7.604,138.185C46.655,355.124,58.335,382.844,74.96,407.064z"></path>
                                            <path class="st0" d="M460.504,114.246c-45.033-5.744-91.501-1.546-136.15,12.122c-44.665,13.788-87.824,37.615-125.871,73.074 c-69.901,65.134-104.392,153.439-98.479,236.359c-9.87,7.499-19.845,14.918-30.116,22.24c-10.375,7.283-20.614,14.614-33.392,21.84 L54.707,512c15.134-8.604,25.717-16.272,36.597-23.891c10.712-7.635,20.942-15.246,31.037-22.922 c79.099,27.336,181.792,5.84,260.746-67.722c42.83-39.874,72.338-89.547,85.917-140.228 C482.705,206.604,479.988,155.194,460.504,114.246z M406.778,187.89c-19.974,29.17-50.193,63.972-82.72,98.326 c28.898-4.735,51.996-9.038,63.06-11.16c4.655-0.889,7.17-1.395,7.178-1.395c4.446-0.889,8.765,1.995,9.654,6.434 c0.889,4.447-1.995,8.765-6.434,9.654c-0.08,0.016-39.986,7.988-92.302,16.095c-5.768,5.913-11.561,11.777-17.337,17.553 c-17.61,17.602-35.01,34.386-51.09,49.464c39.554-6.145,80.044-14.372,99.801-18.555c7.852-1.658,12.403-2.668,12.418-2.676 c4.422-0.977,8.804,1.81,9.79,6.233c0.977,4.423-1.81,8.804-6.233,9.79c-0.121,0.016-74.012,16.456-136.062,24.732l-0.969,0.128 c-16.232,14.782-30.124,26.888-40.043,35.003v0.008c-3.5,2.868-8.676,2.347-11.544-1.162c-2.868-3.5-2.347-8.676,1.162-11.544 c10.343-8.461,25.469-21.688,43.183-37.919c0.505-3.557,1.058-8.044,1.634-13.275c0.969-8.782,2.002-19.452,3.02-30.661 c2.044-22.408,4.006-46.948,5.256-62.988c0.833-10.687,1.346-17.578,1.346-17.593c0.336-4.519,4.27-7.908,8.789-7.571 c4.519,0.329,7.908,4.27,7.572,8.789c0,0.032-4.615,62.323-8.669,102.317c-0.136,1.338-0.264,2.508-0.4,3.781 c15.599-14.653,32.456-30.916,49.44-47.901c5.992-5.993,11.993-12.074,17.978-18.203c2.452-9.806,4.246-25.444,5.32-41.116 c1.194-16.961,1.666-34.105,1.859-45.073c0.128-7.307,0.128-11.833,0.128-11.842c0-4.534,3.669-8.204,8.204-8.204 s8.204,3.67,8.204,8.204c-0.008,0.144,0.016,41.741-3.509,75.27c-0.072,0.649-0.153,1.266-0.225,1.906 c31.414-33.312,60.336-66.801,79.003-94.113c2.556-3.741,7.659-4.694,11.401-2.139C408.38,179.044,409.333,184.148,406.778,187.89z "></path>
                                        </g>
                                    </g>
                                </svg>
                                Sustainability
                            </a>
                        </li>
                        <li><a href="/careers" class="hover:underline">Careers</a></li>
                        <li><a href="/faq" class="hover:underline">FAQ</a></li>
                        <li><a href="/gds-code" class="hover:underline">GDS Code</a></li>
                        <li><a href="/bali-jungle-resort-ubud" class="hover:underline">Bali Jungle Resort<br>Ubud</a></li>
                    </ul>
                </div>

                {{-- BADGES --}}
                <div class="lg:col-span-3 flex justify-end">
                    <div class="space-y-5">
                        <div class="flex items-center justify-end gap-6">
                            <img src="{{ asset('images/awards-best-luxury-jungle-retreat.jpeg') }}" class="h-28 w-auto" loading="lazy" alt="Award 2024">
                            <img src="{{ asset('images/awards-best-luxury-jungle-retreat-2025.png') }}" class="h-28 w-auto" loading="lazy" alt="Award 2025">
                            <img src="{{ asset('images/TC_white_winner-gif_L_2025-Circle.gif') }}" class="h-24 w-auto" loading="lazy" alt="Tripadvisor">
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <img src="{{ asset('images/OIP.webp') }}" class="h-10 w-auto" loading="lazy" alt="Blink">
                            <img src="{{ asset('images/ot-design.png') }}" class="h-10 w-auto" loading="lazy" alt="OT Design">
                        </div>
                    </div>
                </div>
            </div>

            {{-- TABLET + MOBILE LAYOUT --}}
            <div class="lg:hidden text-center">

                <div class="space-y-10">
                    <div class="flex items-center justify-center">
                        <a href="/" class="inline-flex">
                            <img src="{{ asset('images/logo-njhg.png') }}" alt="Nandini Jungle by Hanging Gardens" class="w-36 lg:w-44 h-auto max-h-52 shrink-0 brightness-0 invert" loading="lazy" />
                        </a>
                    </div>

                    <div>
                        <h3 class="text-sm tracking-[0.22em] lg:text-lg uppercase mb-4">Address</h3>
                        <div class="text-sm leading-7 text-white/90">
                            Banjar Susut, Desa Buahan,<br>
                            Payangan, Bali 80571,<br>
                            Indonesia
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm tracking-[0.22em] lg:text-lg uppercase mb-4">Phone</h3>

                        <div class="text-sm text-white/90 space-y-6">
                            <div>
                                <div class="text-white/70">Resort:</div>
                                <a href="tel:+623618983111" class="hover:underline">+62 361 89 83 111</a>
                            </div>

                            <div>
                                <div class="text-white/70">Reservations:</div>
                                <a href="tel:+6281236871170" class="hover:underline">+62 812 3687 1170</a>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm tracking-[0.22em] lg:text-lg uppercase mb-4">Email</h3>
                        <a href="mailto:reservation@nandinibali.com" class="text-sm text-white/90 hover:underline break-all">
                            reservation@nandinibali.com
                        </a>
                    </div>
                </div>

                {{-- ABOUT --}}
                <div class="mt-14">
                    <h3 class="text-sm tracking-[0.22em] lg:text-lg uppercase mb-5">About</h3>
                    <div class="flex flex-wrap justify-center gap-x-6 gap-y-3 text-sm text-white/90">
                        <a href="/about" class="hover:underline">About Us</a>
                        <a href="/blog" class="hover:underline">Blog &amp; News</a>
                        <a href="/awards" class="hover:underline">Awards</a>
                        <a href="/gallery" class="hover:underline">Gallery</a>
                        <a href="/press" class="hover:underline">Press Room</a>
                        <a href="/contact" class="hover:underline">Contact Us</a>
                    </div>
                </div>

                {{-- OTHERS --}}
                <div class="mt-12">
                    <h3 class="text-sm tracking-[0.22em] lg:text-lg uppercase mb-5">Others</h3>
                    <div class="flex flex-wrap justify-center gap-x-6 gap-y-3 text-sm text-white/90">
                        <a href="/sustainability" class="hover:underline text-green-700 flex items-center gap-1 font-medium">
                            <svg height="16px" width="16px" version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve" fill="#35D39B">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <style type="text/css">
                                        .st0 {
                                            fill: #008236;
                                        }

                                    </style>
                                    <g>
                                        <path class="st0" d="M74.96,407.064c3.061-82.464,41.669-165,106.747-225.632c39.177-36.509,84.732-62.947,135.396-78.57 c0.738-0.232,1.482-0.4,2.228-0.617c-0.697-2.003-1.266-4.07-2.027-6.033C301.112,54.055,270.837,21.384,233.206,0 c-41.725,12.947-80.005,35.812-112.236,67.626c-32.158,31.798-58.1,72.425-73.186,119.83 c-15.062,47.389-17.033,94.834-7.604,138.185C46.655,355.124,58.335,382.844,74.96,407.064z"></path>
                                        <path class="st0" d="M460.504,114.246c-45.033-5.744-91.501-1.546-136.15,12.122c-44.665,13.788-87.824,37.615-125.871,73.074 c-69.901,65.134-104.392,153.439-98.479,236.359c-9.87,7.499-19.845,14.918-30.116,22.24c-10.375,7.283-20.614,14.614-33.392,21.84 L54.707,512c15.134-8.604,25.717-16.272,36.597-23.891c10.712-7.635,20.942-15.246,31.037-22.922 c79.099,27.336,181.792,5.84,260.746-67.722c42.83-39.874,72.338-89.547,85.917-140.228 C482.705,206.604,479.988,155.194,460.504,114.246z M406.778,187.89c-19.974,29.17-50.193,63.972-82.72,98.326 c28.898-4.735,51.996-9.038,63.06-11.16c4.655-0.889,7.17-1.395,7.178-1.395c4.446-0.889,8.765,1.995,9.654,6.434 c0.889,4.447-1.995,8.765-6.434,9.654c-0.08,0.016-39.986,7.988-92.302,16.095c-5.768,5.913-11.561,11.777-17.337,17.553 c-17.61,17.602-35.01,34.386-51.09,49.464c39.554-6.145,80.044-14.372,99.801-18.555c7.852-1.658,12.403-2.668,12.418-2.676 c4.422-0.977,8.804,1.81,9.79,6.233c0.977,4.423-1.81,8.804-6.233,9.79c-0.121,0.016-74.012,16.456-136.062,24.732l-0.969,0.128 c-16.232,14.782-30.124,26.888-40.043,35.003v0.008c-3.5,2.868-8.676,2.347-11.544-1.162c-2.868-3.5-2.347-8.676,1.162-11.544 c10.343-8.461,25.469-21.688,43.183-37.919c0.505-3.557,1.058-8.044,1.634-13.275c0.969-8.782,2.002-19.452,3.02-30.661 c2.044-22.408,4.006-46.948,5.256-62.988c0.833-10.687,1.346-17.578,1.346-17.593c0.336-4.519,4.27-7.908,8.789-7.571 c4.519,0.329,7.908,4.27,7.572,8.789c0,0.032-4.615,62.323-8.669,102.317c-0.136,1.338-0.264,2.508-0.4,3.781 c15.599-14.653,32.456-30.916,49.44-47.901c5.992-5.993,11.993-12.074,17.978-18.203c2.452-9.806,4.246-25.444,5.32-41.116 c1.194-16.961,1.666-34.105,1.859-45.073c0.128-7.307,0.128-11.833,0.128-11.842c0-4.534,3.669-8.204,8.204-8.204 s8.204,3.67,8.204,8.204c-0.008,0.144,0.016,41.741-3.509,75.27c-0.072,0.649-0.153,1.266-0.225,1.906 c31.414-33.312,60.336-66.801,79.003-94.113c2.556-3.741,7.659-4.694,11.401-2.139C408.38,179.044,409.333,184.148,406.778,187.89z "></path>
                                    </g>
                                </g>
                            </svg>
                            Sustainability
                        </a>
                        <a href="/careers" class="hover:underline">Careers</a>
                        <a href="/faq" class="hover:underline">FAQ</a>
                        <a href="/gds-code" class="hover:underline">GDS Code</a>
                        <a href="/bali-jungle-resort-ubud" class="hover:underline">Bali Jungle Resort Ubud</a>
                    </div>
                </div>

                {{-- AWARDS --}}
                <div class="mt-14">
                    <div class="flex items-center justify-center gap-6">
                        <img src="{{ asset('images/awards-best-luxury-jungle-retreat.jpeg') }}" class="h-16 sm:h-20 w-auto" loading="lazy" alt="Award 2024">
                        <img src="{{ asset('images/awards-best-luxury-jungle-retreat-2025.png') }}" class="h-16 sm:h-20 w-auto" loading="lazy" alt="Award 2025">
                        <img src="{{ asset('images/TC_white_winner-gif_L_2025-Circle.gif') }}" class="h-16 sm:h-20 w-auto" loading="lazy" alt="Tripadvisor">
                    </div>

                    <div class="mt-6 flex items-center justify-center gap-4">
                        <img src="{{ asset('images/OIP.webp') }}" class="h-7 sm:h-8 w-auto" loading="lazy" alt="Blink">
                        <img src="{{ asset('images/ot-design.png') }}" class="h-7 sm:h-8 w-auto" loading="lazy" alt="OT Design">
                    </div>
                </div>
            </div>
        </div>

        {{-- DIVIDER --}}
        <div class="h-px w-full bg-white/20"></div>

        {{-- BOTTOM --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 py-6 text-center md:text-left">
            <div class="flex items-center justify-center md:justify-start gap-5 text-white/90">
                <a href="#" aria-label="Instagram" class="hover:text-white">
                    <svg class="w-6 h-6 fill-white" viewBox="0 0 24 24">
                        <path d="M7.75 2C4.574 2 2 4.574 2 7.75v8.5C2 19.426 4.574 22 7.75 22h8.5C19.426 22 22 19.426 22 16.25v-8.5C22 4.574 19.426 2 16.25 2h-8.5zm0 2h8.5C18.321 4 20 5.679 20 7.75v8.5c0 2.071-1.679 3.75-3.75 3.75h-8.5C5.679 20 4 18.321 4 16.25v-8.5C4 5.679 5.679 4 7.75 4zm4.25 2.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zm0 2a3.5 3.5 0 110 7 3.5 3.5 0 010-7zm4.75-.75a1 1 0 100 2 1 1 0 000-2z" />
                    </svg>
                </a>

                <a href="#" aria-label="Facebook" class="hover:text-white">
                    <svg class="w-6 h-6 fill-white" viewBox="0 0 24 24">
                        <path d="M22 12a10 10 0 10-11.5 9.875V15.5H8.5V12h2V9.75C10.5 7.57 11.93 6 14.5 6c1.22 0 2.5.22 2.5.22v2.75H15.6c-1.38 0-1.8.86-1.8 1.74V12h3.06l-.49 3.5H13.8v6.375A10 10 0 0022 12z" />
                    </svg>
                </a>

                <a href="#" aria-label="YouTube" class="hover:text-white">
                    <svg class="w-6 h-6 fill-white" viewBox="0 0 24 24">
                        <path d="M23.498 6.186a2.997 2.997 0 00-2.11-2.12C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.388.566a2.997 2.997 0 00-2.11 2.12C0 8.075 0 12 0 12s0 3.925.502 5.814a2.997 2.997 0 002.11 2.12C4.5 20.5 12 20.5 12 20.5s7.5 0 9.388-.566a2.997 2.997 0 002.11-2.12C24 15.925 24 12 24 12s0-3.925-.502-5.814zM9.75 15.568V8.432L15.818 12 9.75 15.568z" />
                    </svg>
                </a>

                <a href="#" aria-label="Tripadvisor" class="hover:text-white">
                    <svg class="w-6 h-6 fill-white" viewBox="0 0 20 20">
                        <path d="M20 6.009h-2.829C15.211 4.675 12.813 4 10 4s-5.212.675-7.171 2.009H0c.428.42.827 1.34.993 2.04A4.954 4.954 0 0 0 0 11.008c0 2.757 2.243 5 5 5a4.97 4.97 0 0 0 3.423-1.375L10 17l1.577-2.366A4.97 4.97 0 0 0 15 16.01c2.757 0 5-2.243 5-5 0-1.112-.377-2.13-.993-2.96.166-.7.565-1.62.993-2.04zm-15 8.4c-1.875 0-3.4-1.525-3.4-3.4s1.525-3.4 3.4-3.4 3.4 1.525 3.4 3.4-1.525 3.4-3.4 3.4zm5-3.4a5.008 5.008 0 0 0-4.009-4.9C7.195 5.704 8.53 5.5 10 5.5s2.805.204 4.009.61A5.008 5.008 0 0 0 10 11.008zm5 3.4c-1.875 0-3.4-1.525-3.4-3.4s1.525-3.4 3.4-3.4 3.4 1.525 3.4 3.4-1.525 3.4-3.4 3.4zM5 8.86c-1.185 0-2.15.964-2.15 2.15s.965 2.15 2.15 2.15 2.15-.964 2.15-2.15-.965-2.15-2.15-2.15zm0 2.791a.65.65 0 1 1 0-1.3.65.65 0 0 1 0 1.3zm10-2.791c-1.185 0-2.15.964-2.15 2.15s.965 2.15 2.15 2.15 2.15-.964 2.15-2.15-.965-2.15-2.15-2.15zm0 2.791a.65.65 0 1 1 0-1.3.65.65 0 0 1 0 1.3z" />
                    </svg>
                </a>
            </div>

            <div class="text-sm text-white/80 md:text-right">
                Copyright © {{ date('Y') }} Nandini Jungle by Hanging Gardens.
            </div>
        </div>
    </div>
</footer>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @stack('meta')

    {{-- FAVICON --}}
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
    @stack('css')

    {{-- slick carousel --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css">

    {{-- google font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Unna:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">

    {{-- custom slick carousel --}}
    <style>
        /* Make slick slides equal height */
        .itemcarousel-slick .slick-track {
            display: flex !important;
        }

        .itemcarousel-slick .slick-slide {
            height: auto !important;
            display: flex !important;
        }

        .itemcarousel-slick .slick-slide>div {
            display: flex;
            flex: 1;
        }

    </style>
</head>

<body class="font-sans text-[15px] leading-7 text-slate-700 mx-auto lg:mx-0">
    <x-layouts.navbar />

    {{ $slot }}

    @livewireScripts

    {{-- slick carousel --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

    {{-- navbar scroll --}}
    <script>
        function initNavbarScroll() {
            const navbar = document.getElementById('mainNavbar');
            const logo = document.getElementById('navLogo');
            const inner = document.getElementById('navInner');
            const bookBtn = document.getElementById('navBookBtn');
            const navLeft = document.getElementById('navLeft');
            const navIcons = document.getElementById('navIcons');

            if (!navbar || !logo || !inner) return;

            function handleScroll() {
                const scrolled = window.scrollY > 30;

                if (scrolled) {
                    navbar.classList.remove('bg-black/35', 'backdrop-blur-md', 'text-white');
                    navbar.classList.add('bg-white', 'text-slate-900', 'shadow');

                    inner.classList.remove('lg:h-28');
                    inner.classList.add('lg:h-20');

                    logo.classList.remove('lg:h-24', 'brightness-0', 'invert');
                    logo.classList.add('lg:h-16');

                    if (navLeft) {
                        navLeft.classList.add('text-slate-900');
                        navLeft.classList.remove('text-white');
                    }

                    if (navIcons) {
                        navIcons.classList.add('text-slate-900');
                    }

                    if (bookBtn) {
                        bookBtn.classList.remove('bg-white', 'border-white', 'text-slate-900');
                        bookBtn.classList.add('bg-[#A67C3D]', 'border-[#A67C3D]', 'text-white');
                    }
                } else {
                    navbar.classList.remove('bg-white', 'text-slate-900', 'shadow');
                    navbar.classList.add('bg-black/35', 'backdrop-blur-md', 'text-white');

                    inner.classList.remove('lg:h-20');
                    inner.classList.add('lg:h-28');

                    logo.classList.remove('lg:h-16');
                    logo.classList.add('lg:h-24', 'brightness-0', 'invert');

                    if (navLeft) {
                        navLeft.classList.remove('text-slate-900');
                        navLeft.classList.add('text-white');
                    }

                    if (navIcons) {
                        navIcons.classList.remove('text-slate-900');
                    }

                    if (bookBtn) {
                        bookBtn.classList.remove('bg-[#A67C3D]', 'border-[#A67C3D]', 'text-white');
                        bookBtn.classList.add('bg-white', 'border-white', 'text-slate-900');
                    }
                }
            }

            handleScroll();

            window.removeEventListener('scroll', handleScroll);
            window.addEventListener('scroll', handleScroll, { passive: true });
        }

        document.addEventListener('DOMContentLoaded', initNavbarScroll);
        document.addEventListener('livewire:navigated', initNavbarScroll);
    </script>

    {{-- navbar menu, sidebar dropdown, book dropdown --}}
    <script>
        (function ($) {
            function initNavbarActions() {
                const $backdrop = $('#offcanvasBackdrop');
                const $menu = $('#offcanvasMenu');

                function lockScroll(lock) {
                    $('html, body').toggleClass('overflow-hidden', lock);
                }

                function openMenu() {
                    $backdrop.removeClass('hidden').hide().fadeIn(150);
                    $menu.removeClass('-translate-x-full').addClass('translate-x-0');
                    lockScroll(true);
                }

                function closeMenu() {
                    $backdrop.fadeOut(150, function () {
                        $backdrop.addClass('hidden');
                    });

                    $menu.addClass('-translate-x-full').removeClass('translate-x-0');
                    lockScroll(false);
                }

                $(document).off('click.nandiniMenu', '#btnMenu');
                $(document).on('click.nandiniMenu', '#btnMenu', function (e) {
                    e.preventDefault();
                    openMenu();
                });

                $(document).off('click.nandiniMenu', '#btnCloseMenu, #offcanvasBackdrop');
                $(document).on('click.nandiniMenu', '#btnCloseMenu, #offcanvasBackdrop', function (e) {
                    e.preventDefault();
                    closeMenu();
                });

                $(document).off('keydown.nandiniMenu');
                $(document).on('keydown.nandiniMenu', function (e) {
                    if (e.key === 'Escape') {
                        closeMenu();
                        $('#navBookMenu').fadeOut(120);
                    }
                });

                $(document).off('click.nandiniDropdown', '[data-oc-toggle]');
                $(document).on('click.nandiniDropdown', '[data-oc-toggle]', function (e) {
                    e.preventDefault();

                    const targetSel = $(this).attr('data-oc-toggle');
                    const $target = $(targetSel);

                    if (!$target.length) return;

                    $('[data-oc-toggle]').not(this).each(function () {
                        const otherSel = $(this).attr('data-oc-toggle');
                        const $other = $(otherSel);

                        $other.stop(true, true).slideUp(180, function () {
                            $other.addClass('hidden');
                        });

                        $(this).find('svg').removeClass('rotate-180');
                    });

                    if ($target.hasClass('hidden')) {
                        $target
                            .removeClass('hidden')
                            .hide()
                            .stop(true, true)
                            .slideDown(180);
                    } else {
                        $target.stop(true, true).slideUp(180, function () {
                            $target.addClass('hidden');
                        });
                    }

                    $(this).find('svg').toggleClass('rotate-180');
                });

                $(document).off('click.nandiniBook', '#navBookBtn');
                $(document).on('click.nandiniBook', '#navBookBtn', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $('#navBookMenu').stop(true, true).fadeToggle(120);
                });

                $(document).off('click.nandiniBookOutside');
                $(document).on('click.nandiniBookOutside', function (e) {
                    const clickedInside =
                        $(e.target).closest('#navBookBtn').length ||
                        $(e.target).closest('#navBookMenu').length;

                    if (!clickedInside) {
                        $('#navBookMenu').fadeOut(120);
                    }
                });
            }

            $(document).ready(initNavbarActions);
            document.addEventListener('livewire:navigated', initNavbarActions);
        })(jQuery);
    </script>

    {{-- offer carousel --}}
    <script>
        function initItemCarousel() {
            const $ = window.jQuery;
            if (!$) return;

            const $el = $('.itemcarousel-slick');
            if (!$el.length) return;

            if ($el.hasClass('slick-initialized')) {
                $el.slick('refresh');
                return;
            }

            $el.slick({
                slidesToShow: 3,
                slidesToScroll: 1,
                infinite: true,
                arrows: true,
                prevArrow: $('.itemcarousel-prev'),
                nextArrow: $('.itemcarousel-next'),
                dots: false,
                speed: 450,
                responsive: [
                    { breakpoint: 1024, settings: { slidesToShow: 2 } },
                    { breakpoint: 640, settings: { slidesToShow: 1 } },
                ],
            });
        }

        document.addEventListener('DOMContentLoaded', initItemCarousel);
        document.addEventListener('livewire:navigated', initItemCarousel);
    </script>

    @stack('scripts')
</body>

</html>
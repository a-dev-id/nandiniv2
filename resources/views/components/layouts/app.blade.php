<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @stack('meta')

    {{-- FAVICON --}}
    <link href="https://nandinibali.com/images/favicon-njhg.png" type="image/x-icon" rel="icon">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('css')

    {{-- google font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Unna:wght@400;700&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Unna:wght@400;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Unna:wght@400;700&display=swap" rel="stylesheet">
    </noscript>

    {{-- Google Tag Manager --}}
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });

            var firstScript = d.getElementsByTagName(s)[0];
            var tagManagerScript = d.createElement(s);
            var dataLayerQuery = l !== 'dataLayer' ? '&l=' + l : '';

            tagManagerScript.async = true;
            tagManagerScript.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dataLayerQuery;
            firstScript.parentNode.insertBefore(tagManagerScript, firstScript);
        })(window, document, 'script', 'dataLayer', 'GTM-TW272WHM');
    </script>
    {{-- End Google Tag Manager --}}

    <script>
        (function () {
            var loaded = false;

            var loadMarketingScripts = function () {
                if (loaded) {
                    return;
                }

                loaded = true;

                !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version="2.0";n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,"script","https://connect.facebook.net/en_US/fbevents.js");
                fbq("init", "1805469430060017");
                fbq("track", "PageView");

            };

            var interactionEvents = ["pointerdown", "keydown", "scroll", "touchstart"];

            interactionEvents.forEach(function (eventName) {
                window.addEventListener(eventName, loadMarketingScripts, {
                    once: true,
                    passive: true
                });
            });

            if ("requestIdleCallback" in window) {
                window.requestIdleCallback(loadMarketingScripts, {
                    timeout: 3000
                });
            } else {
                window.setTimeout(loadMarketingScripts, 3000);
            }
        })();
    </script>
</head>

<body class="font-sans mx-auto lg:mx-0">
    {{-- Google Tag Manager (noscript) --}}
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TW272WHM" height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
    {{-- End Google Tag Manager (noscript) --}}

    <x-layouts.navbar />

    <main id="main-content">
        {{ $slot }}
    </main>

    <x-layouts.footer />

    @unless (request()->routeIs('membership.login'))
    @hasSection('inquiry-modal')
    <x-inquiry-modal />
    @endif
    @endunless

    @if (! config('features.disable_membership_feature') && ! request()->routeIs('membership.login'))
    @hasSection('redemption-modal')
    <x-redemption-modal />
    @endif
    @endif

    <x-mini-popup-widget />

    @stack('scripts')
</body>

</html>
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

    <script>
        window.dataLayer = window.dataLayer || [];
        window.gtag = window.gtag || function () {
            window.dataLayer.push(arguments);
        };

        gtag("js", new Date());
        gtag("config", "G-D1DSZB70W6");
        gtag("config", "G-3HTZW0EWMJ");
        gtag("config", "G-1SFWHFG85Q");
        gtag("config", "AW-16624938367");
        gtag("config", "AW-11066683333");
        gtag("config", "AW-16624938367/PU1HCNT5z70ZEP_asfc9", {
            "phone_conversion_number": "+6281236871170"
        });

        gtag("event", "conversion", {
            "send_to": "AW-17673351471/R_ZbCJe8xbIbEK_ip-tB",
            "transaction_id": ""
        });
        gtag("event", "ads_conversion_Contact_Us_1", {});
        gtag("event", "conversion", {
            "send_to": "AW-16624938367/wJ7FCIW8z70ZEP_asfc9"
        });
        gtag("event", "conversion_event_purchase", {});

        window.gtag_report_conversion = function (url) {
            var callback = function () {
                if (typeof url !== "undefined") {
                    window.location = url;
                }
            };

            gtag("event", "conversion", {
                "send_to": "AW-16624938367/wJ7FCIW8z70ZEP_asfc9",
                "event_callback": callback
            });

            return false;
        };

        window.gtagSendEvent = function (url) {
            var callback = function () {
                if (typeof url === "string") {
                    window.location = url;
                }
            };

            gtag("event", "ads_conversion_Contact_Us_1", {
                "event_callback": callback,
                "event_timeout": 2000
            });

            gtag("event", "conversion_event_purchase", {
                "event_callback": callback,
                "event_timeout": 2000
            });

            return false;
        };

        (function () {
            var loaded = false;
            var loadScript = function (src, id) {
                if (id && document.getElementById(id)) {
                    return;
                }

                var script = document.createElement("script");
                script.async = true;
                script.src = src;

                if (id) {
                    script.id = id;
                }

                document.head.appendChild(script);
            };

            var loadMarketingScripts = function () {
                if (loaded) {
                    return;
                }

                loaded = true;

                loadScript("https://www.googletagmanager.com/gtag/js?id=G-D1DSZB70W6", "google-gtag");

                window.dataLayer.push({
                    "gtm.start": new Date().getTime(),
                    "event": "gtm.js"
                });
                loadScript("https://www.googletagmanager.com/gtm.js?id=GTM-NS5J4HS", "gtm-ns5j4hs");
                loadScript("https://www.googletagmanager.com/gtm.js?id=GTM-TW272WHM", "gtm-tw272whm");

                !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version="2.0";n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,"script","https://connect.facebook.net/en_US/fbevents.js");
                fbq("init", "1805469430060017");
                fbq("track", "PageView");

                (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};m[i].l=1*new Date();k=e.createElement(t);a=e.getElementsByTagName(t)[0];k.async=1;k.src=r;a.parentNode.insertBefore(k,a)})(window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");
                ym(98217418, "init", {
                    clickmap: true,
                    trackLinks: true,
                    accurateTrackBounce: true,
                    webvisor: true
                });

                (function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y)})(window, document, "clarity", "script", "nwruvtqlai");
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
    {{-- script old website --}}
    <!-- cookies marketing -->
    {{-- <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MFCM686N" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript> --}}
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NS5J4HS" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

    <!-- Google Tag Manager (noscript) webmaster@nandinibali.com -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TW272WHM" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    {{-- script old website --}}

    <x-layouts.navbar />

    <main id="main-content">
        {{ $slot }}
    </main>

    <x-layouts.footer />

    @unless (request()->routeIs('membership.login'))
    <x-inquiry-modal />
    @endunless

    @if (! config('features.disable_membership_feature') && ! request()->routeIs('membership.login'))
    <x-redemption-modal />
    @endif

    <x-mini-popup-widget />

    @stack('scripts')
</body>

</html>

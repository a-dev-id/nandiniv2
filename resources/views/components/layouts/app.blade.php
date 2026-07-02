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

    {{-- script old website --}}
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-D1DSZB70W6"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-D1DSZB70W6');
    </script>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-3HTZW0EWMJ"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
    
      gtag('config', 'G-3HTZW0EWMJ');
    </script>

    <!-- Event snippet for Purchase conversion page -->
    <script>
        gtag('event', 'conversion', {
            'send_to': 'AW-17673351471/R_ZbCJe8xbIbEK_ip-tB',
            'transaction_id': ''
            // 'new_customer': true /* calculate dynamically, populate with true/false */,
        });
    </script>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-1SFWHFG85Q"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
      
        gtag('config', 'G-1SFWHFG85Q');
    </script>

    <!-- Google Ads Global site tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-16624938367"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'AW-16624938367');
    </script>

    <script>
        function gtag_report_conversion(url) {
          var callback = function () {
            if (typeof(url) != 'undefined') {
              window.location = url;
            }
          };
          gtag('event', 'conversion', {
              'send_to': 'AW-16624938367/wJ7FCIW8z70ZEP_asfc9',
              'event_callback': callback
          });
          return false;
        }
    </script>


    <!-- Facebook Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js'); fbq('init', '1805469430060017'); fbq('track', 'PageView');
    </script><noscript> <img height="1" width="1" src="https://www.facebook.com/tr?id=1805469430060017&ev=PageView&noscript=1" /></noscript><!-- End Facebook Pixel Code -->


    <!-- Meta Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '1805469430060017');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=1805469430060017&ev=PageView&noscript=1" /></noscript>
    <!-- End Meta Pixel Code -->


    <!-- cookies marketing -->
    {{-- <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-MFCM686N');
    </script> --}}
    <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-NS5J4HS');
    </script>

    <script>
        gtag('event', 'ads_conversion_Contact_Us_1', {
                // <event_parameters>
                });
    </script>

    <script>
        function gtagSendEvent(url) {
          var callback = function () {
            if (typeof url === 'string') {
              window.location = url;
            }
          };
          gtag('event', 'ads_conversion_Contact_Us_1', {
            'event_callback': callback,
            'event_timeout': 2000,
            // <event_parameters>
          });
          return false;
        }
    </script>

    <!-- Event snippet for Contact-Us Page conversion page -->
    <script>
        gtag('event', 'conversion', {'send_to': 'AW-16624938367/wJ7FCIW8z70ZEP_asfc9'});
    </script>

    <script>
        gtag('event', 'conversion_event_purchase', {
            // <event_parameters>
            });
    </script>

    <script>
        function gtagSendEvent(url) {
          var callback = function () {
            if (typeof url === 'string') {
              window.location = url;
            }
          };
          gtag('event', 'conversion_event_purchase', {
            'event_callback': callback,
            'event_timeout': 2000,
            // <event_parameters>
          });
          return false;
        }
    </script>

    <script>
        gtag('config', 'AW-16624938367/PU1HCNT5z70ZEP_asfc9', {
            'phone_conversion_number': '+6281236871170'
        });
    </script>

    <!-- Yandex.Metrika counter -->
    <script type="text/javascript">
        (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
       m[i].l=1*new Date();
       for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
       k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
       (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");
    
       ym(98217418, "init", {
            clickmap:true,
            trackLinks:true,
            accurateTrackBounce:true,
            webvisor:true
       });
    </script>
    <!-- /Yandex.Metrika counter -->

    <script type="text/javascript">
        (function(c,l,a,r,i,t,y){
              c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
              t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
              y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
          })(window, document, "clarity", "script", "nwruvtqlai");
    </script>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-11066683333">
    </script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
      
        gtag('config', 'AW-11066683333');
    </script>

    <!-- Google tag (gtag.js) webmaster@nandinibali.com -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-3HTZW0EWMJ"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
    
      gtag('config', 'G-3HTZW0EWMJ');
    </script>

    <!-- Google Tag Manager webmaster@nandinibali.com -->
    <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
      new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
      j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
      'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
      })(window,document,'script','dataLayer','GTM-TW272WHM');
    </script>
    <!-- End Google Tag Manager -->
    {{-- script old website --}}
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

@props(['page'])

@php
$metaTitle = $page->meta_title ?: $page->title;
$metaDescription = $page->meta_description
    ?: \Illuminate\Support\Str::limit(strip_tags($page->excerpt ?: $page->description ?: ''), 160, '');
$metaImage = $page->hero_image ?: $page->hero_mobile_image;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="author" content="Nandini Jungle by Hanging Gardens">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="Nandini Jungle by Hanging Gardens">
    @if ($metaImage)
    <meta property="og:image" content="{{ asset('storage/' . $metaImage) }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    @if ($metaImage)
    <meta name="twitter:image" content="{{ asset('storage/' . $metaImage) }}">
    @endif
    <link href="{{ asset('images/favicon-njhg.png') }}" type="image/x-icon" rel="icon">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Unna:wght@400;700&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Unna:wght@400;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Unna:wght@400;700&display=swap" rel="stylesheet"></noscript>
    <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-TW272WHM');
    </script>
    <script>
        (function(){var loaded=false;var load=function(){if(loaded)return;loaded=true;!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version="2.0";n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,"script","https://connect.facebook.net/en_US/fbevents.js");fbq("init","1805469430060017");fbq("track","PageView")};["pointerdown","keydown","scroll","touchstart"].forEach(function(eventName){window.addEventListener(eventName,load,{once:true,passive:true})});if("requestIdleCallback" in window){window.requestIdleCallback(load,{timeout:3000})}else{window.setTimeout(load,3000)}})();
    </script>
</head>
<body class="mx-auto font-sans text-slate-700 lg:mx-0">
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TW272WHM" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <x-layouts.navbar />
    <main id="main-content" class="spa-site" style="--spa-accent: #791841;">{{ $slot }}</main>
    <x-layouts.footer />
</body>
</html>

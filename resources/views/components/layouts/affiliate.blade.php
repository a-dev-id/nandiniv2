<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @stack('meta')
    <link href="https://nandinibali.com/images/favicon-njhg.png" type="image/x-icon" rel="icon">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Unna:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="flex min-h-screen flex-col bg-slate-50 font-sans text-slate-700">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex min-h-20 w-full max-w-7xl flex-col items-stretch justify-between gap-4 px-5 py-4 sm:flex-row sm:items-center sm:gap-5 sm:px-8 lg:px-10">
            <a href="https://nandinibali.com" class="flex min-w-0 items-center gap-4" aria-label="Visit Nandini Jungle by Hanging Gardens main website">
                <img src="{{ asset('images/logo-njhg.png') }}" alt="Nandini Jungle by Hanging Gardens" class="h-12 w-auto shrink-0" width="250" height="104">
                <span class="hidden border-l border-slate-300 pl-4 text-xs font-medium uppercase tracking-[0.12em] text-slate-700 sm:block">
                    Nandini Partner Circle
                </span>
            </a>

            @if ($navigationAffiliate = auth('affiliate')->user())
            <nav class="flex w-full flex-wrap items-center justify-start gap-x-4 gap-y-3 sm:w-auto sm:justify-end sm:gap-5" aria-label="Affiliate navigation">
                <a href="{{ route('affiliate.dashboard') }}" class="text-xs font-medium uppercase tracking-[0.08em] text-slate-700 transition hover:text-[#A88444] sm:text-sm">
                    Dashboard
                </a>

                @if ($navigationAffiliate->isApproved())
                    <a href="{{ route('affiliate.marketing-materials.index') }}" class="text-xs font-medium uppercase tracking-[0.08em] text-slate-700 transition hover:text-[#A88444] sm:text-sm">Marketing Materials</a>
                    <a href="{{ route('affiliate.payment-details.edit') }}" class="text-xs font-medium uppercase tracking-[0.08em] text-slate-700 transition hover:text-[#A88444] sm:text-sm">Payment Details</a>
                @endif

                <form method="POST" action="{{ route('affiliate.logout') }}">
                    @csrf
                    <button type="submit" class="border border-slate-800 px-3 py-2 text-xs font-medium uppercase tracking-[0.08em] text-slate-700 transition hover:border-[#A88444] hover:bg-[#A88444] hover:text-white sm:px-4 sm:text-sm">
                        Logout
                    </button>
                </form>
            </nav>
            @endif
        </div>
    </header>

    <main class="flex-1">
        {{ $slot }}
    </main>

    <footer class="border-t border-slate-200 bg-white px-5 py-6 text-center text-xs text-slate-500 sm:text-sm">
        Nandini Partner Circle · Nandini Jungle by Hanging Gardens
    </footer>
</body>
</html>

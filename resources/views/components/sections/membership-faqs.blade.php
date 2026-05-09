@props([
'title' => 'FAQs',
'description' => 'Find answers to common questions about joining our membership program',
'contactLabel' => 'Contact',
'contactUrl' => '#',
])

@php
$faqs = [
[
'question' => 'How do I register?',
'answer' => 'Visit our registration page and provide your email address, name, and password. Verify your email through the link we send you. Once confirmed, your account is active and ready to earn points.',
],
[
'question' => 'Is my account information secure?',
'answer' => 'We protect your data with industry-standard encryption and security protocols. Your personal information is never shared with third parties without your consent. Rest assured your account remains private and protected.',
],
[
'question' => 'Can I sign in from multiple devices?',
'answer' => 'Yes, you can access your account from any device using your credentials. Simply log in with your email and password on your phone, tablet, or computer. Your points and membership status sync across all devices.',
],
[
'question' => 'What if I forget my password?',
'answer' => 'Click the forgot password link on the sign in page. Enter your email address and we will send you instructions to reset it. Follow the link in your email to create a new password within minutes.',
],
[
'question' => 'How long does account verification take?',
'answer' => 'Verification happens instantly after you confirm your email address. Check your inbox for our verification message and click the link. Your account will be fully activated and ready to use immediately.',
],
];
@endphp

<section class="bg-white px-6 py-14 md:py-20">
    <div class="mx-auto grid max-w-[1500px] grid-cols-1 gap-10 lg:grid-cols-[0.8fr_1.2fr] lg:gap-20">
        {{-- Left Content --}}
        <div class="lg:pt-2">
            <h2 class="text-3xl font-bold leading-tight text-slate-950 md:text-4xl">
                {{ $title }}
            </h2>

            @if ($description)
            <p class="mt-8 max-w-md text-[15px] leading-relaxed text-gray-600 sm:text-base">
                {{ $description }}
            </p>
            @endif

            @if ($contactLabel)
            <div class="mt-8">
                <x-buttons.link-button :href="$contactUrl" variant="outline" class="px-7 py-3 tracking-normal normal-case">
                    {{ $contactLabel }}
                </x-buttons.link-button>
            </div>
            @endif
        </div>

        {{-- FAQ Items --}}
        <div class="space-y-5">
            @foreach ($faqs as $faq)
            <details class="group border border-slate-700 bg-white p-0">
                <summary class="flex cursor-pointer list-none items-start justify-between gap-6 px-7 py-6 marker:hidden">
                    <span class="text-base font-bold leading-relaxed text-slate-950">
                        {{ $faq['question'] }}
                    </span>

                    <span class="relative mt-1 flex h-5 w-5 shrink-0 items-center justify-center text-slate-950">
                        {{-- Closed icon: arrow down --}}
                        <svg class="block h-5 w-5 group-open:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 9l6 6 6-6" />
                        </svg>

                        {{-- Open icon: minus --}}
                        <svg class="hidden h-5 w-5 group-open:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14" />
                        </svg>
                    </span>
                </summary>

                <div class="px-7 pb-7">
                    <p class="max-w-4xl text-[15px] leading-relaxed text-gray-700 sm:text-base">
                        {{ $faq['answer'] }}
                    </p>
                </div>
            </details>
            @endforeach
        </div>
    </div>
</section>
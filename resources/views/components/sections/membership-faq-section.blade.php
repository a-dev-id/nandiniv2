@props([
'section' => null,
'contactLabel' => 'Contact',
'contactUrl' => '#',
])

@php
$title = trim((string) ($section?->title ?? 'FAQs'));
$subtitle = trim((string) ($section?->subtitle ?? ''));
$description = trim((string) ($section?->description ?? 'Find answers to common questions about joining our membership program'));

$hasTitle = $title !== '';
$hasSubtitle = $subtitle !== '';
$hasDescription = $description !== '';

$backgroundColor = $section?->background_color ?: 'white';

$backgroundClass = match ($backgroundColor) {
'soft_gray' => 'bg-slate-50',
'warm_ivory' => 'bg-[#fbf8f1]',
'light_gold' => 'bg-[#f6efe2]',
'dark_navy' => 'bg-[#071a33]',
default => 'bg-white',
};

$textAlign = $section?->text_align ?: 'left';

$headerAlignClass = match ($textAlign) {
'center' => 'text-center mx-auto',
'right' => 'text-right ml-auto',
default => 'text-left',
};

$titleColorClass = $backgroundColor === 'dark_navy'
? 'text-white'
: 'text-slate-800';

$descriptionColorClass = $backgroundColor === 'dark_navy'
? 'text-white/85'
: 'text-slate-700';

$borderColorClass = $backgroundColor === 'dark_navy'
? 'border-white/30'
: 'border-slate-900';

$questionColorClass = $backgroundColor === 'dark_navy'
? 'text-white'
: 'text-slate-900';

$answerColorClass = $backgroundColor === 'dark_navy'
? 'text-white/80'
: 'text-slate-700';

$iconLineClass = $backgroundColor === 'dark_navy'
? 'bg-white'
: 'bg-slate-900';

$buttonLabel = trim((string) ($section?->button_label ?: $contactLabel));
$buttonUrl = trim((string) ($section?->button_url ?: $contactUrl)) ?: '#';

if (($section?->button_link_type ?? 'manual') === 'route' && $section?->button_route) {
$buttonUrl = \Illuminate\Support\Facades\Route::has($section->button_route)
? route($section->button_route)
: '#';
}

$defaultItems = [
[
'question' => 'How do I register?',
'answer' => 'Visit our registration page and provide your email address, name, and password. Verify your email through the link we send you. Once confirmed, your account is active and ready to earn points.',
],
[
'question' => 'Is my account information secure?',
'answer' => 'We protect your data with industry-standard encryption and security protocols. Your personal information is never shared with third parties without your consent.',
],
[
'question' => 'Can I sign in from multiple devices?',
'answer' => 'Yes. You can sign in from multiple devices using the same account credentials.',
],
[
'question' => 'What if I forget my password?',
'answer' => 'Use the forgot password option on the sign-in page. We will send password reset instructions to your registered email address.',
],
[
'question' => 'How long does account verification take?',
'answer' => 'Account verification is usually completed shortly after you confirm your email address.',
],
];

$rawItems = $section?->items ?? [];

if (is_string($rawItems)) {
$rawItems = json_decode($rawItems, true) ?: [];
}

$sectionItems = collect($rawItems)
->map(function ($item) {
return [
'question' => trim((string) ($item['question'] ?? $item['title'] ?? '')),
'answer' => trim((string) ($item['answer'] ?? $item['description'] ?? '')),
];
})
->filter(fn ($item) => filled($item['question']) || filled($item['answer']))
->values()
->all();

$faqs = count($sectionItems) > 0 ? $sectionItems : $defaultItems;
@endphp

<section class="{{ $backgroundClass }} px-6 py-14 md:py-20">
    <div class="mx-auto grid max-w-[1500px] gap-12 lg:grid-cols-[0.75fr_1.5fr] lg:gap-20">

        <div class="{{ $headerAlignClass }}">
            @if ($hasSubtitle)
            <p class="mb-4 text-sm md:text-base leading-relaxed tracking-[0.18em] uppercase text-[#b28a4a] font-medium">
                {!! str_ireplace(
                ['&lt;br&gt;', '&lt;br/&gt;', '&lt;br /&gt;'],
                '<br class="hidden md:block">',
                e($subtitle)
                ) !!}
            </p>
            @endif

            @if ($hasTitle)
            <h2 class="text-2xl sm:text-3xl md:text-3xl leading-snug tracking-[0.15em] md:tracking-[0.25em] uppercase font-medium {{ $titleColorClass }}">
                {!! str_ireplace(
                ['&lt;br&gt;', '&lt;br/&gt;', '&lt;br /&gt;'],
                '<br class="hidden md:block">',
                e($title)
                ) !!}
            </h2>
            @endif

            @if ($hasDescription)
            <div class="mt-8 max-w-[420px] text-[15px] leading-7 {{ $descriptionColorClass }} {{ $textAlign === 'center' ? 'mx-auto' : '' }} {{ $textAlign === 'right' ? 'ml-auto' : '' }}">
                {!! $description !!}
            </div>
            @endif

            @if ($buttonLabel)
            <a href="{{ $buttonUrl }}" class="mt-8 inline-flex min-w-[145px] items-center justify-center border {{ $borderColorClass }} px-6 py-4 text-xs font-semibold uppercase tracking-[0.22em] {{ $questionColorClass }} transition duration-300 hover:border-[#b28a4a] hover:bg-[#b28a4a] hover:text-white">
                {{ $buttonLabel }}
            </a>
            @endif
        </div>

        <div class="space-y-5">
            @foreach ($faqs as $faq)
            @php
            $question = trim((string) ($faq['question'] ?? ''));
            $answer = trim((string) ($faq['answer'] ?? ''));
            $itemId = 'membership-faq-' . $loop->iteration . '-' . md5($question);
            @endphp

            <div x-data="{ open: false }" class="border {{ $borderColorClass }}">
                <button type="button" class="flex w-full items-center justify-between gap-6 px-6 py-6 text-left" @click="open = !open" :aria-expanded="open.toString()" aria-controls="{{ $itemId }}">
                    <span class="text-[15px] font-semibold leading-7 {{ $questionColorClass }}">
                        {{ $question }}
                    </span>

                    <span class="relative flex h-5 w-5 shrink-0 items-center justify-center">
                        <span class="absolute h-px w-4 {{ $iconLineClass }}"></span>
                        <span class="absolute h-4 w-px {{ $iconLineClass }} transition duration-300" :class="{ 'rotate-90 opacity-0': open }"></span>
                    </span>
                </button>

                @if ($answer)
                <div id="{{ $itemId }}" x-show="open" x-collapse class="px-6 pb-6">
                    <div class="max-w-3xl text-[15px] leading-7 {{ $answerColorClass }}">
                        {!! nl2br(e($answer)) !!}
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        </div>

    </div>
</section>
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

$descriptionAlignClass = match ($textAlign) {
'center' => 'text-center mx-auto',
'right' => 'text-right ml-auto',
default => 'text-left',
};

$titleColorClass = $backgroundColor === 'dark_navy'
? 'text-white'
: 'text-slate-700';

$descriptionColorClass = $backgroundColor === 'dark_navy'
? 'text-white/85'
: 'text-gray-600';

$borderColorClass = $backgroundColor === 'dark_navy'
? 'border-white/30'
: 'border-slate-900';

$questionColorClass = $backgroundColor === 'dark_navy'
? 'text-white'
: 'text-slate-700';

$answerColorClass = $backgroundColor === 'dark_navy'
? 'text-white/80'
: 'text-gray-600';

$iconLineClass = $backgroundColor === 'dark_navy'
? 'bg-white'
: 'bg-slate-900';

$buttonLabel = trim((string) ($section?->button_label ?: $contactLabel));
$buttonUrl = trim((string) ($section?->button_url ?: $contactUrl)) ?: '#';
$buttonClass = $backgroundColor === 'dark_navy'
? 'border-[#A88444] bg-[#A88444] text-white hover:bg-white hover:text-[#071a33] hover:border-white'
: 'border-[#A88444] bg-[#A88444] text-white hover:bg-[#B8945B] hover:border-[#B8945B]';

if (($section?->button_link_type ?? 'manual') === 'route' && $section?->button_route) {
$buttonUrl = \Illuminate\Support\Facades\Route::has($section->button_route)
? route($section->button_route)
: '#';
}

$buttonLabel = \App\Support\DetailPageButtonLabel::resolve(
$buttonLabel,
$section?->button_route,
$buttonUrl,
);

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
            <p class="mb-4 text-xs md:text-base leading-relaxed uppercase text-[#b28a4a] font-medium sm:text-sm">
                {!! str_ireplace(
                ['&lt;br&gt;', '&lt;br/&gt;', '&lt;br /&gt;'],
                '<br class="hidden md:block">',
                e($subtitle)
                ) !!}
            </p>
            @endif

            @if ($hasTitle)
            <h2 class="text-lg leading-snug uppercase font-medium {{ $titleColorClass }} mb-3 sm:text-xl">
                {!! str_ireplace(
                ['&lt;br&gt;', '&lt;br/&gt;', '&lt;br /&gt;'],
                '<br class="hidden md:block">',
                e($title)
                ) !!}
            </h2>
            @endif

            @if ($hasDescription)
            <div class="mt-8 text-xs leading-relaxed max-w-2xl sm:max-w-3xl md:max-w-5xl {{ $descriptionColorClass }} {{ $descriptionAlignClass }} sm:text-sm">
                {!! $description !!}
            </div>
            @endif

            @if ($buttonLabel)
            <a href="{{ $buttonUrl }}" class="mt-8 inline-flex min-w-[130px] items-center justify-center border px-4 py-2.5 text-xs font-medium uppercase transition duration-300 {{ $buttonClass }} tracking-[0.08em] sm:text-sm">
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
                <button type="button" class="flex w-full items-center justify-between gap-6 px-4 py-6 text-left tracking-[0.08em] font-medium" @click="open = !open" :aria-expanded="open.toString()" aria-controls="{{ $itemId }}">
                    <span class="text-xs font-semibold leading-7 {{ $questionColorClass }} sm:text-sm">
                        {{ $question }}
                    </span>

                    <span class="relative flex h-5 w-5 shrink-0 items-center justify-center">
                        <span class="absolute h-px w-4 {{ $iconLineClass }}"></span>
                        <span class="absolute h-4 w-px {{ $iconLineClass }} transition duration-300" :class="{ 'rotate-90 opacity-0': open }"></span>
                    </span>
                </button>

                @if ($answer)
                <div id="{{ $itemId }}" x-show="open" x-collapse class="px-6 pb-6">
                    <div class="max-w-3xl text-xs leading-relaxed {{ $answerColorClass }} sm:text-sm">
                        {!! nl2br(e($answer)) !!}
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        </div>

    </div>
</section>

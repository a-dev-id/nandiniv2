@props([
'section' => null,
])

@if ($section)
@php
$title = trim((string) ($section->title ?? ''));
$subtitle = trim((string) ($section->subtitle ?? ''));
$description = trim((string) ($section->description ?? ''));

$hasTitle = $title !== '';
$hasSubtitle = $subtitle !== '';
$hasDescription = $description !== '';

$textAlign = $section->text_align ?: 'center';

$descriptionAlignClass = match ($textAlign) {
'left' => 'text-left',
'right' => 'text-right',
default => 'text-center',
};

/*
Keep the text block centered on the page.
The text inside can still be left / center / right aligned.
*/
$descriptionMarginClass = 'mx-auto';

/*
Left aligned text is wider.
Center/right text stays narrow like the intro section.
*/
$descriptionWidthClass = match ($textAlign) {
'left' => 'max-w-[1040px]',
default => 'max-w-[950px]',
};

$backgroundColor = $section->background_color ?: 'white';

$backgroundClass = match ($backgroundColor) {
'soft_gray' => 'bg-slate-50',
'warm_ivory' => 'bg-[#fbf8f1]',
'light_gold' => 'bg-[#f6efe2]',
'dark_navy' => 'bg-[#071a33]',
default => 'bg-white',
};

$titleColorClass = $backgroundColor === 'dark_navy'
? 'text-white'
: 'text-slate-800';

$descriptionColorClass = $backgroundColor === 'dark_navy'
? 'text-white/85'
: 'text-slate-700';

/*
If this intro section has no title/subtitle,
it is treated as a continuation text section.
Example: the description below "How It Works".
*/
$sectionSpacingClass = ($hasTitle || $hasSubtitle)
? 'py-14 md:py-20'
: 'pt-0 pb-14 md:pb-20';
@endphp

<section class="{{ $sectionSpacingClass }} px-6 {{ $backgroundClass }}">
    <div class="max-w-[1200px] mx-auto">

        {{-- Subtitle --}}
        @if ($hasSubtitle)
        <p class="mb-4 text-center text-sm md:text-base leading-relaxed tracking-[0.18em] uppercase text-[#b28a4a] font-medium">
            {!! str_ireplace(
            ['&lt;br&gt;', '&lt;br/&gt;', '&lt;br /&gt;'],
            '<br class="hidden md:block">',
            e($subtitle)
            ) !!}
        </p>
        @endif

        {{-- Title --}}
        @if ($hasTitle)
        <h2 class="text-center text-2xl sm:text-3xl md:text-3xl leading-snug tracking-[0.15em] md:tracking-[0.25em] uppercase font-medium {{ $titleColorClass }}">
            {!! str_ireplace(
            ['&lt;br&gt;', '&lt;br/&gt;', '&lt;br /&gt;'],
            '<br class="hidden md:block">',
            e($title)
            ) !!}
        </h2>
        @endif

        {{-- Description --}}
        @if ($hasDescription)
        <div class="{{ ($hasTitle || $hasSubtitle) ? 'mt-8' : '' }} {{ $descriptionWidthClass }} text-[15px] leading-7 {{ $descriptionColorClass }} {{ $descriptionMarginClass }} {{ $descriptionAlignClass }}">
            {!! $description !!}
        </div>
        @endif

    </div>
</section>
@endif
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
: 'pt-10 pb-14 md:pb-10';
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
        <div class="{{ $descriptionMarginClass }} {{ $descriptionWidthClass }} {{ $descriptionAlignClass }} {{ $descriptionColorClass }} pt-8 md:pt-10">
            <div class="
                    [&_h1]:text-2xl
                    sm:[&_h1]:text-3xl
                    md:[&_h1]:text-4xl
                    [&_h1]:leading-snug
                    [&_h1]:tracking-[0.15em]
                    md:[&_h1]:tracking-[0.25em]
                    [&_h1]:uppercase
                    [&_h1]:text-slate-800
                    [&_h1]:mb-6
                    md:[&_h1]:mb-8
                    [&_h1]:font-medium

                    [&_h2]:mb-5
                    [&_h2]:text-[28px]
                    [&_h2]:font-semibold
                    [&_h2]:leading-tight
                    [&_h2]:tracking-normal
                    [&_h2]:normal-case
                    [&_h2]:text-slate-800

                    [&_h3]:mb-4
                    [&_h3]:text-xl
                    [&_h3]:font-semibold
                    [&_h3]:leading-snug
                    [&_h3]:text-slate-800

                    [&_p]:mb-2
                    [&_p]:text-base
                    [&_p]:leading-7

                    [&_ul]:mb-5
                    [&_ul]:list-disc
                    [&_ul]:pl-6

                    [&_ol]:mb-5
                    [&_ol]:list-decimal
                    [&_ol]:pl-6

                    [&_li]:mb-2
                    [&_li]:text-base
                    [&_li]:leading-7

                    [&_ul_ul]:mt-2
                    [&_ul_ul]:list-disc
                    [&_ul_ul]:pl-6

                    [&_strong]:font-semibold
                ">
                {!! $description !!}
            </div>
        </div>
        @endif

    </div>
</section>
@endif
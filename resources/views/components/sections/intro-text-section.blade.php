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
: 'text-slate-700';

$descriptionColorClass = $backgroundColor === 'dark_navy'
? 'text-white/85'
: 'text-gray-600';

/*
If this intro section has no title/subtitle,
it is treated as a continuation text section.
Example: the description below "How It Works".
*/
$sectionSpacingClass = ($hasTitle || $hasSubtitle)
? 'py-14 md:py-20'
: 'pt-0 pb-14 md:pb-10';
@endphp

<section class="{{ $sectionSpacingClass }} px-6 {{ $backgroundClass }}">
    <div class="max-w-[1200px] mx-auto">

        {{-- Subtitle --}}
        @if ($hasSubtitle)
        <p class="mb-4 text-center text-sm md:text-base leading-relaxed uppercase text-[#b28a4a] font-medium">
            {!! str_ireplace(
            ['&lt;br&gt;', '&lt;br/&gt;', '&lt;br /&gt;'],
            '<br class="hidden md:block">',
            e($subtitle)
            ) !!}
        </p>
        @endif

        {{-- Title --}}
        @if ($hasTitle)
        <h2 class="text-xl text-center leading-snug uppercase font-medium {{ $titleColorClass }} mb-3">
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
            <div class="[&_h1]:text-2xl [&_h1]:leading-snug [&_h1]:uppercase text-slate-700 [&_h1]:mb-3 [&_h2]:mb-3 [&_h3]:mb-3 [&_h1]:font-medium [&_h2]:text-xl [&_h2]:font-medium [&_h2]:leading-snug [&_h2]:uppercase [&_h3]:text-lg [&_h3]:font-semibold [&_h3]:leading-snug [&_p]:mb-2 text-sm [&_p]:leading-relaxed [&_ul]:mb-5 [&_ul]:list-disc [&_ul]:pl-6 [&_ol]:mb-5 [&_ol]:list-decimal [&_ol]:pl-6 [&_li]:mb-2 [&_li]:text-sm [&_li]:leading-7 [&_ul_ul]:mt-2 [&_ul_ul]:list-disc [&_ul_ul]:pl-6 [&_strong]:font-semibold">
                {!! $description !!}
            </div>
        </div>
        @endif

    </div>
</section>
@endif

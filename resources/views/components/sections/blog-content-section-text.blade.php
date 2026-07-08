@props([
'title' => '',
'subtitle' => '',
'excerpt' => '',
'description' => '',
'descriptionText' => '',
'textAlignClass' => 'text-left',
'textColorClass' => 'text-slate-800',
'mutedTextColorClass' => 'text-slate-700',
])

<div class="{{ $textAlignClass }} {{ $textColorClass }}">
    @if ($title !== '')
    <h2 class="mb-2 text-sm leading-snug sm:text-base">
        {{ $title }}
    </h2>
    @endif

    @if ($subtitle !== '')
    <p class="mb-2 text-xs font-medium uppercase leading-relaxed tracking-[0.08em] {{ $mutedTextColorClass }} sm:text-sm">
        {{ $subtitle }}
    </p>
    @endif

    @if ($excerpt !== '')
    <p class="mb-4 text-xs leading-relaxed {{ $mutedTextColorClass }} sm:text-sm">
        {{ $excerpt }}
    </p>
    @endif

    @if ($descriptionText !== '')
    <div class="blog-detail-content text-xs leading-relaxed {{ $mutedTextColorClass }} [&_a]:text-[#A88444] [&_a]:underline [&_a]:underline-offset-2 [&_h1]:mb-3 [&_h2]:mb-3 [&_h2]:mt-10 [&_h2]:text-lg [&_h2]:font-semibold [&_h3]:mb-3 [&_h3]:mt-8 [&_h3]:text-base [&_h3]:font-semibold [&_li]:mb-2 [&_li]:pl-1 [&_li::marker]:text-slate-500 [&_ol]:my-6 [&_ol]:list-decimal [&_ol]:pl-6 [&_p]:mb-4 [&_p]:min-h-6 [&_p:last-child]:mb-0 [&_strong]:font-semibold [&_ul]:my-6 [&_ul]:list-disc [&_ul]:pl-6 sm:text-sm sm:[&_h2]:text-xl sm:[&_h3]:text-lg">
        {!! $description !!}
    </div>
    @endif
</div>

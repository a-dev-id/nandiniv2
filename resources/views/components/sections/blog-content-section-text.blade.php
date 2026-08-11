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
    <h2 class="blog-detail-section-title">
        {{ $title }}
    </h2>
    @endif

    @if ($subtitle !== '')
    <p class="mb-3 text-xs font-medium uppercase leading-relaxed tracking-[0.08em] {{ $mutedTextColorClass }} sm:text-[0.8125rem]">
        {{ $subtitle }}
    </p>
    @endif

    @if ($excerpt !== '')
    <p class="blog-detail-content mb-4 {{ $mutedTextColorClass }}">
        {{ $excerpt }}
    </p>
    @endif

    @if ($descriptionText !== '')
    <div class="blog-detail-content {{ $mutedTextColorClass }}">
        {!! $description !!}
    </div>
    @endif
</div>

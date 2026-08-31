@props(['section', 'page'])

@switch($section->section_key)
    @case('intro_text_section')
        <x-sections.intro-text-section :section="$section" />
        @break
    @case('how_it_works_section')
        @if ($section->images->isNotEmpty())
            <x-spa-site.feature-cards :section="$section" />
        @else
            <x-sections.how-it-works-section :section="$section" :spa-accent="true" />
        @endif
        @break
    @case('spa_information_section')
        <x-sections.spa-information-section :section="$section" :spa-accent="true" />
        @break
    @case('image_overlay_section')
        <x-sections.image-overlay-section :section="$section" :spa-accent="true" />
        @break
    @case('contained_image_section')
        <x-sections.contained-image-section :section="$section" />
        @break
    @case('split_media_section')
        <x-sections.split-media-section :section="$section" :excerpt-only="false" image-span="7" text-span="5" :spa-accent="true" />
        @break
    @case('split_media_reverse')
        <x-sections.split-media-section :section="$section" :reverse="true" :excerpt-only="false" image-span="7" text-span="5" :spa-accent="true" />
        @break
    @case('seo_split_media_section')
        <x-sections.seo-split-media-section :section="$section" :page="$page" />
        @break
    @case('seo_split_media_reverse')
        <x-sections.seo-split-media-section :section="$section" :page="$page" :reverse="true" />
        @break
    @case('three_images_section')
        <x-sections.three-images-section :section="$section" />
        @break
    @case('two_images_section')
        <x-sections.two-images-section :section="$section" />
        @break
    @case('two_images_reverse')
        <x-sections.two-images-section :section="$section" :reverse="true" />
        @break
@endswitch

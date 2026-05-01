<x-layouts.app>
    <x-heroes.video-hero video-id="eh5h5P6_3LQ" />

    <x-sections.video-text-section :page="$page" video-id="eh5h5P6_3LQ" />

    @foreach ($sections as $section)
    @if ($section->section_key === 'image_overlay_section')
    <x-sections.image-overlay-section :section="$section" />
    @endif

    @if ($section->section_key === 'split_media_section')
    <x-sections.split-media-section :section="$section" :excerpt-only="false" image-span="8" text-span="4" />
    @endif

    @if ($section->section_key === 'split_media_reverse')
    <x-sections.split-media-section :section="$section" :reverse="true" :excerpt-only="false" image-span="8" text-span="4" />
    @endif

    @if ($section->section_key === 'intro_text_section')
    <x-sections.intro-text-section :section="$section" />
    @endif
    @endforeach

    <x-sections.item-carousel :items="$experiences" />

</x-layouts.app>
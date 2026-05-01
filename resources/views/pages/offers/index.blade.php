<x-layouts.app>
    <x-heroes.image-hero :page="$page" />
    <x-sections.page-description :page="$page" />
    <x-sections.item-list model="offer" :with-filter="false" />
</x-layouts.app>
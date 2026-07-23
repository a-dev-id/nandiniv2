@php($pollingInterval = $this->getPollingInterval())

<x-filament-widgets::widget
    :attributes="
        (new \Illuminate\View\ComponentAttributeBag)
            ->merge([
                'wire:poll.' . $pollingInterval => $pollingInterval ? true : null,
            ], escape: false)
            ->class(['fi-wi-stats-overview'])
    "
>
    <x-filament::section
        :heading="$this->getSectionHeading()"
        :description="$this->getSectionDescription()"
        :collapse-id="$this->getSectionCollapseId()"
        collapsible
        collapsed
    >
        {{ $this->content }}
    </x-filament::section>
</x-filament-widgets::widget>

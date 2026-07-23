<x-filament-widgets::widget class="fi-wi-table">
    <x-filament::section
        heading="Voucher Overview"
        description="Paid voucher purchases and completed redemptions."
        collapse-id="voucher-overview"
        collapsible
        collapsed
    >
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Widgets\View\WidgetsRenderHook::TABLE_WIDGET_START, scopes: static::class) }}

        {{ $this->table ?? null }}

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Widgets\View\WidgetsRenderHook::TABLE_WIDGET_END, scopes: static::class) }}
    </x-filament::section>
</x-filament-widgets::widget>

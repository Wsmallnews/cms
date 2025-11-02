<x-filament-panels::page>
    {{ $this->form }}

    @if ($record)
        <livewire:sn-fi-navigation :navigation-type="$record" />
    @endif
</x-filament-panels::page>
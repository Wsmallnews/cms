<x-filament-panels::page>
    {{ $this->form }}

    @if ($record)
        <livewire:sn-fi-navigation :navigation-type="$record" :properties="$this->getProperties()" :key="'components-' . $record->id . '-' . $record->level" />
    @endif
</x-filament-panels::page>
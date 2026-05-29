@php
    $properties = static::getProperties();
    $canManage = static::getCanManage();
@endphp

<x-filament-panels::page>
    @if ($canManage)
        {{ $this->form }}
    @endif

    @if ($navigationType)
        <livewire:sn-cms-fi-navigation
            :properties="$properties"
            :navigation-type="$navigationType"
            :key="'fi-components-sn-navigation-' . $navigationType->id . '-' . $navigationType->level"
        />
    @endif
</x-filament-panels::page>

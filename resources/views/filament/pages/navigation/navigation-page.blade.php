@php
    $canManage = static::getCanManage();
@endphp

<x-filament-panels::page>
    @if ($canManage)
        {{ $this->form }}
    @endif

    {{ $this->content }}

    @if ($navigationType)
        @include('sn-filament-nestedset::filament.pages.components.nestedset', [
            'nestedset' => $this->getNestedset(),
            'level' => $this->getLevel(),
        ])
    @endif
</x-filament-panels::page>

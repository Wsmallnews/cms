@php
    $canManage = static::getCanManage();
@endphp

<x-filament-panels::page>
    @if ($canManage)
        {{ $this->form }}
    @endif

     @if ($navigationType)
        @php
            $nestedset = $this->getNestedset();
        @endphp
        
        @if ($headerActions = $this->getNestedsetActions())
            <div class="flex justify-end items-center">
                <x-filament::actions :actions="$headerActions" />
            </div>
        @endif

        {{ $this->content }}

        <x-sn-filament-nestedset::filament.nestedset 
            :nestedset="$nestedset" 
            :level="$level" 
            :empty-label="$emptyLabel" 
            :empty-tip-label="$emptyTipLabel" />
    @endif
</x-filament-panels::page>

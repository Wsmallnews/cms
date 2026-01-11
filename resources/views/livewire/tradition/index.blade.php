@php
    $scopeType = $this->getScopeType();
    $scopeId = $this->getScopeId();
@endphp

<div class="w-full flex flex-col grow">
    <livewire:sn-cms-components-navigation :scope-type="$scopeType" :scope-id="$scopeId" />

    <div class="w-full flex flex-col grow gap-4">
        {{-- <livewire:sn-cms-components-index-posts :limit="6" /> --}}
    </div>

    <livewire:sn-cms-components-footer :scope-type="$scopeType" :scope-id="$scopeId" />
</div>
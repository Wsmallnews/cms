@php
    $scopeType = $this->getScopeType();
    $scopeId = $this->getScopeId();
@endphp

<div class="w-full flex flex-col grow">
    <livewire:sn-components-navigation :scope-type="$scopeType" :scope-id="$scopeId" />

    <div class="w-full grow">
        <livewire:sn-components-navigation-container :scope-type="$scopeType" :scope-id="$scopeId" :slug="$slug" />
    </div>

    <livewire:sn-components-footer :scope-type="$scopeType" :scope-id="$scopeId" />
</div>

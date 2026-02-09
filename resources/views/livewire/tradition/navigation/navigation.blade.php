@php
    $scopeType = $this->getScopeType();
    $scopeId = $this->getScopeId();
@endphp

<x-dynamic-component :component="$this->getPageContainer()" :scope-type="$scopeType" :scope-id="$scopeId">
    <div class="w-full">
        <livewire:sn-cms-components-navigation-container :scope-type="$scopeType" :scope-id="$scopeId" :slug="$slug" />
    </div>
</x-dynamic-component>
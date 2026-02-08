@php
    $scopeType = $this->getScopeType();
    $scopeId = $this->getScopeId();
@endphp

<x-dynamic-component :component="$this->getPageContainer()" :scope-type="$scopeType" :scope-id="$scopeId" class="w-full">
    <div class="container mx-auto flex flex-col grow gap-4 my-4">
        <livewire:sn-cms-components-index-posts :scope-type="$scopeType" :scope-id="$scopeId" :limit="6" />

        <livewire:sn-components-index-overview />

        <livewire:sn-components-index-personnels />

        <livewire:sn-components-index-posts :scope-type="$scopeType" :scope-id="$scopeId" />

        <livewire:sn-components-index-scientific-research :scope-type="$scopeType" :scope-id="$scopeId" />
    </div>
</x-dynamic-component>

@php
    $scopeType = $this->getScopeType();
    $scopeId = $this->getScopeId();
@endphp

<x-dynamic-component :component="$this->getPageContainer()" :scope-type="$scopeType" :scope-id="$scopeId">
    <div class="sn-page">
        <livewire:sn-cms::components.post.index-posts :scope-type="$scopeType" :scope-id="$scopeId" :limit="6" />
    </div>
</x-dynamic-component>

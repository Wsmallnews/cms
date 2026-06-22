@php
    $scopeType = $this->getScopeType();
    $scopeId = $this->getScopeId();
@endphp

<x-dynamic-component :component="$this->getPageContainer()" :scope-type="$scopeType" :scope-id="$scopeId">
    <div class="container mx-auto flex flex-col grow gap-4 my-4">
        <livewire:sn-cms::components.post.index-posts :scope-type="$scopeType" :scope-id="$scopeId" :limit="6" />
    </div>
</x-dynamic-component>

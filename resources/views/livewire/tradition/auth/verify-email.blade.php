@php
    $scopeType = $this->getScopeType();
    $scopeId = $this->getScopeId();
@endphp

<x-dynamic-component :component="$this->getPageContainer()" :scope-type="$scopeType" :scope-id="$scopeId">
    <div class="container mx-auto flex flex-col grow gap-4 my-4">
        <div class="w-full mx-auto md:w-96 p-4">
            <livewire:sn-user::components.auth.verify-email :module="app(\Wsmallnews\Cms\CmsPlugin::class)->getId()" :type="$type" />
        </div>
    </div>
</x-dynamic-component>
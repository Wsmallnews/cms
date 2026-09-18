@php
    use Wsmallnews\Cms\CmsPlugin;

    $scopeType = $this->getScopeType();
    $scopeId = $this->getScopeId();
@endphp

<x-dynamic-component :component="$this->getPageContainer()" :scope-type="$scopeType" :scope-id="$scopeId">
    <div class="sn-content">
        <div class="w-full mx-auto @2xl:w-96 sn-padded">
            <livewire:sn-user::components.auth.forgot-password :module="app(CmsPlugin::class)->getId()" />
        </div>
    </div>
</x-dynamic-component>
@php
    use Wsmallnews\Cms\CmsPlugin;

    $scopeType = $this->getScopeType();
    $scopeId = $this->getScopeId();
@endphp

<x-dynamic-component :component="$this->getPageContainer()" :scope-type="$scopeType" :scope-id="$scopeId">
    <div class="container mx-auto flex flex-col grow gap-4 my-4">
        <h1 class="sn-content-text text-xl font-semibold">{{ __('sn-cms::cms.frontend.search_results') }}</h1>

        <livewire:sn-support::components.search-results :module="app(CmsPlugin::class)->getId()" :limit="10" />
    </div>
</x-dynamic-component>

@php
    use Wsmallnews\Cms\CmsPlugin;

    $scopeType = $this->getScopeType();
    $scopeId = $this->getScopeId();
@endphp

<x-dynamic-component :component="$this->getPageContainer()" :scope-type="$scopeType" :scope-id="$scopeId">
    <div class="sn-page">
        @if($breadcrumbs)
            <div class="sn-descript-text w-full flex items-center gap-2 text-left">
                {{ __('sn-cms::cms.frontend.current_location') }} :
                <x-sn-support::breadcrumbs :breadcrumbs="$breadcrumbs" />
            </div>
        @endif

        <div class="w-full flex flex-col md:flex-row items-start gap-4">
            <div class="w-full md:w-72">
                <livewire:sn-user::components.user.sidebar-menu :module="app(CmsPlugin::class)->getId()" />
            </div>

            <div class="sn-container w-full px-4 py-8">
                <div class="w-full md:w-md">
                    <livewire:sn-user::components.settings.two-factor :module="app(CmsPlugin::class)->getId()" />
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
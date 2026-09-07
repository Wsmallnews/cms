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
        
        {{-- 侧栏与内容区按比例分栏（lg 1:3，xl 起 1:4）；lg 以下上下堆叠（侧栏在上） --}}
        <div class="w-full flex flex-col lg:grid lg:grid-cols-4 xl:grid-cols-5 items-start sn-gap">
            <div class="w-full min-w-0">
                <livewire:sn-user::components.user.sidebar-menu :module="app(CmsPlugin::class)->getId()" />
            </div>

            <div class="sn-container w-full sn-padded lg:col-span-3 xl:col-span-4 min-w-0">
                <livewire:sn-user::components.user.profile :module="app(CmsPlugin::class)->getId()" />
            </div>
        </div>
    </div>
</x-dynamic-component>
@php
    use Wsmallnews\Cms\CmsPlugin;
    use Wsmallnews\Cms\Support\Utils;

    $scopeType = $this->getScopeType();
    $scopeId = $this->getScopeId();

    $user = Utils::getUser();
@endphp

<x-dynamic-component :component="$this->getPageContainer()" :scope-type="$scopeType" :scope-id="$scopeId">
    <div class="container mx-auto flex flex-col grow gap-4 my-4">
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
            <div class="sn-container w-full">
                <livewire:sn-preference::components.views
                    :scope-type="$scopeType"
                    :scope-id="$scopeId"
                    :user="$user"
                    :preferencer="$user"
                    :manageable="true"
                    :contained="false"
                    :href-route="Utils::getConfig('routes.name') . 'posts.show'" />
            </div>
        </div>
    </div>
</x-dynamic-component>

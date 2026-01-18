@php
    $scopeType = $this->getScopeType();
    $scopeId = $this->getScopeId();
@endphp

<div class="w-full flex flex-col grow gap-4">
    <livewire:sn-cms-components-navigation :scope-type="$scopeType" :scope-id="$scopeId" />

    <div class="container mx-auto flex flex-col grow gap-4">
        @if($breadcrumbs)
            <div class="w-full flex items-center gap-2 text-sm text-gray-500 text-left">
                当前位置 :
                <x-sn-support::breadcrumbs :breadcrumbs="$breadcrumbs" />
            </div>
        @endif
        
        <div class="w-full flex flex-col md:flex-row items-start gap-4">
            <div class="w-full md:w-72" >
                <livewire:sn-cms-components-user-profile-menu />
            </div>

            <div class="w-full md:w-md">
                <livewire:sn-user-components-settings-profile :module="app(\Wsmallnews\Cms\CmsPlugin::class)->getId()" />
            </div>
        </div>
    </div>
    <livewire:sn-cms-components-footer :scope-type="$scopeType" :scope-id="$scopeId" />
</div>

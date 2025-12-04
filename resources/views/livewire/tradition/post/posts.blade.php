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

        <livewire:sn-cms-components-posts :scope-type="$scopeType" :scope-id="$scopeId" :category-ids="$categoryId" item-container-wrapper-view="sn-cms::base.item-container" />
    </div>

    <livewire:sn-cms-components-footer :scope-type="$scopeType" :scope-id="$scopeId" />
</div>
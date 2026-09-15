@php
    $scopeType = $this->getScopeType();
    $scopeId = $this->getScopeId();
@endphp

<x-dynamic-component :component="$this->getPageContainer()" :scope-type="$scopeType" :scope-id="$scopeId">
    <div class="sn-page">
        {{-- 首页编排：按后台「内容编排」的行式布局渲染（实体与视图在 support 包）；未配置时渲染空状态提示 --}}
        @if (filled($compositionRows))
            <x-sn-support::composition.rows :rows="$compositionRows" />
        @else
            <div class="py-16 text-center sn-descript-text text-sm">
                {{ __('sn-cms::cms.frontend.home_empty') }}
            </div>
        @endif
    </div>
</x-dynamic-component>

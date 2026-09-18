@php
    $scopeType = $this->getScopeType();
    $scopeId = $this->getScopeId();
@endphp

<x-dynamic-component :component="$this->getPageContainer()" :scope-type="$scopeType" :scope-id="$scopeId">
    <div class="sn-content">
        {{-- 首页内容 = is_home 节点绑定的 Page，实例注入内容组件（SEO 由 Index 声明站点级，组件让渡） --}}
        @if (filled($homePage))
            <livewire:sn-support::components.page.page
                :scope-type="$scopeType"
                :scope-id="$scopeId"
                :page-record="$homePage"
                :module="$module"
                :with-seo="false"
            />
        @else
            <div class="py-16 text-center sn-descript-text text-sm">
                {{ __('sn-cms::cms.frontend.home_empty') }}
            </div>
        @endif
    </div>
</x-dynamic-component>

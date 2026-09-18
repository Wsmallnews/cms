@php
    use Wsmallnews\Cms\Support\Utils;

    $scopeType = $this->getScopeType();
    $scopeId = $this->getScopeId();

    $breadcrumbs = [
        ['label' => __('sn-cms::cms.frontend.home'), 'url' => Utils::route('index')],
        ['label' => __('sn-cms::cms.frontend.page_detail'), 'url' => Utils::route('pages.show', $slug)],
    ];
@endphp

<x-dynamic-component :component="$this->getPageContainer()" :scope-type="$scopeType" :scope-id="$scopeId">
    {{-- 页面内容组件：查询/SEO/双通道三分支渲染（support 机制层），页面级装饰（banner/兄弟导航）由页面容器注入 --}}
    <div class="sn-content">
        <div class="sn-descript-text w-full flex items-center gap-2 text-left">
            {{ __('sn-cms::cms.frontend.current_location') }} :
            <x-sn-support::breadcrumbs :breadcrumbs="$breadcrumbs" />
        </div>

        <livewire:sn-support::components.page.page
            :scope-type="$scopeType"
            :scope-id="$scopeId"
            :slug="$slug"
            :module="$module"
        />
    </div>
</x-dynamic-component>

@php

    $breadcrumbs = [
        ['label' => __('sn-cms::cms.frontend.home'), 'url' => $this->site()?->route('index')],
        ['label' => __('sn-cms::cms.frontend.page_detail'), 'url' => $this->site()?->route('pages.show', $slug)],
    ];
@endphp

<x-dynamic-component :component="$this->getPageContainer()">
    {{-- 页面内容组件：查询/SEO/双通道三分支渲染（support 机制层），页面级装饰（banner/兄弟导航）由页面容器注入 --}}
    <div class="sn-content">
        <div class="sn-descript-text w-full flex items-center gap-2 text-left">
            {{ __('sn-cms::cms.frontend.current_location') }} :
            <x-sn-support::breadcrumbs :breadcrumbs="$breadcrumbs" />
        </div>

        <livewire:sn-support::components.page.page
            :slug="$slug"
        />
    </div>
</x-dynamic-component>

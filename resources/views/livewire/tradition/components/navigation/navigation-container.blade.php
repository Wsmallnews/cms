@php
    $hasBanner = $navigation->getFirstMediaUrl('navigation_banner');
@endphp

<div class="w-full flex flex-col grow sn-gap">
    {{-- banner 图要 宽度 100% --}}
    @if ($hasBanner)
        <div class="w-full">
            <img src="{{ $navigation->getFirstMediaUrl('navigation_banner') }}" class="w-full">
        </div>
    @endif

    <div @class([
            "container mx-auto sn-page-x flex flex-col grow sn-gap",
            "sn-mt" => !$hasBanner
        ])
    >
        <livewire:sn-cms::components.navigation.breadcrumb :navigation="$navigation" />
    
        {{-- 侧栏与内容区按比例分栏（lg 1:3，xl 起 1:4）：宽度随容器伸缩，避免写死宽度挤压内容；
            lg 以下上下堆叠（侧栏在上） --}}
        <div class="w-full flex flex-col lg:grid lg:grid-cols-4 xl:grid-cols-5 items-start sn-gap">
            @if ($navigation->depth > 0)
                {{-- 必须是顶级导航下的子导航才可以显示同级导航列表 --}}
                <div class="w-full min-w-0">
                    <livewire:sn-cms::components.navigation.brothers :navigation="$navigation" />
                </div>
            @endif

            {{-- 无侧栏时内容列占满整行，避免 grid 留空轨道 --}}
            <div @class([
                'w-full min-w-0 flex flex-col sn-gap',
                'lg:col-span-3 xl:col-span-4' => $navigation->depth > 0,
                'lg:col-span-4 xl:col-span-5' => $navigation->depth <= 0,
            ])>
                @foreach ($components as $component)
                    @livewire($component['component_name'], $component['extras'], key($component['component_name'] . '-' . $loop->index))
                @endforeach
            </div>
        </div>
    </div>
</div>

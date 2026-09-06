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
    
        <div class="w-full flex flex-col md:flex-row items-start sn-gap">
            @if ($navigation->depth > 0)
                {{-- 必须是顶级导航下的子导航才可以显示同级导航列表 --}}
                <div class="w-full md:w-72 shrink-0">
                    <livewire:sn-cms::components.navigation.brothers :navigation="$navigation" />
                </div>
            @endif

            <div class="w-full flex flex-col grow sn-gap">
                @foreach ($components as $component)
                    @livewire($component['component_name'], $component['extras'], key($component['component_name'] . '-' . $loop->index))
                @endforeach
            </div>
        </div>
    </div>
</div>

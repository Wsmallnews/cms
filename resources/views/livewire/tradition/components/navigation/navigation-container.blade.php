<div class="w-full flex flex-col grow gap-4">
    <div class="w-full">
        {{-- banner 图要 宽度 100% --}}
        @if ($navigation->getFirstMediaUrl('navigation_banner'))
            <img src="{{ $navigation->getFirstMediaUrl('navigation_banner') }}" class="w-full">
        @endif
    </div>

    <div class="container mx-auto flex flex-col grow gap-4">
        <livewire:sn-cms-components-navigation-breadcrumb :navigation="$navigation" />
    
        <div class="w-full flex flex-col md:flex-row items-start gap-4">
            @if ($navigation->depth > 0)
                {{-- 必须是顶级导航下的子导航才可以显示同级导航列表 --}}
                <div class="w-full md:w-72 shrink-0">
                    <livewire:sn-cms-components-navigation-brothers :navigation="$navigation" />
                </div>
            @endif
    
            <div class="w-full flex flex-col grow gap-4">
                @foreach ($components as $component)
                    @livewire($component['component_name'], $component['extras'], key($component['component_name'] . '-' . $loop->index))
                @endforeach
            </div>
        </div>
    </div>
    <div>
        {{-- 空白占位符 外层 gap 生效 --}}
    </div>
</div>

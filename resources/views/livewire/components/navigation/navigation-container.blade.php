@php
    // $breadcrumbs = [];

    // foreach ($parents as $parent) {
    //     $breadcrumbs[$parent->url_info['url']] = $parent->name;
    // }
@endphp

@push('seo')
    {{-- {!! seo()->for($navigation) !!} --}}
@endpush


<div class="w-full flex flex-col grow gap-4">
    {{-- @if ($navigation->getFirstMediaUrl('banner'))
        <div class="w-full relative">
            <img src="{{ $navigation->getFirstMediaUrl('banner') }}" class="w-full">
        </div>
    @endif --}}

    {{-- <livewire:sn-components-navigation-breadcrumb :navigation="$navigation" /> --}}

    <div class="w-full flex flex-col md:flex-row items-start gap-4">
        @if ($navigation->parent_id)
            {{-- 必须是顶级导航下的子导航才可以显示同级导航列表 --}}
            <livewire:sn-components-navigation-brothers :navigation="$navigation" />
        @endif

        <div class="w-full flex flex-col grow gap-4">
            @foreach ($components as $component_name => $params)
                @livewire($component_name, $params, key($component_name . '-' . $loop->index))
            @endforeach
        </div>
    </div>
</div>

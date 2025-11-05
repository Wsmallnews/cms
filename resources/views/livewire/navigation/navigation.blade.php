@php
    $breadcrumbs = [];

    foreach ($parents as $parent) {
        $breadcrumbs[$parent->url_info['url']] = $parent->name;
    }
@endphp

@push('seo')
    {!! seo()->for($navigation) !!}
@endpush

<div class="w-full flex flex-col grow gap-4">
    <livewire:sn-components-navigation />

    <div class="container mx-auto flex flex-col grow gap-4">
        @if ($navigation->getFirstMediaUrl('banner'))
            <div class="w-full relative">
                <img src="{{ $navigation->getFirstMediaUrl('banner') }}" class="w-full">
            </div>
        @endif

        <div class="w-full flex items-center gap-2 text-sm text-gray-500 text-left">
            当前位置 :
            <x-filament::breadcrumbs :breadcrumbs="$breadcrumbs" />
        </div>

        <div class="w-full flex flex-col md:flex-row items-start gap-4">
            @if ($brothers->isNotEmpty())
                <ul class="flex flex-col w-full md:w-72 shrink-0 bg-primary-500">
                    @foreach ($brothers as $brother)
                        <li class="flex">
                            <a class="flex flex-grow px-4 py-4 font-bold text-white focus:underline"
                                {{ \Filament\Support\generate_href_html($brother->url_info['url'], $brother->url_info['target'] ?? '_self') }}
                                aria-current="page"
                            >
                                {{ $brother->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif

            <div class="w-full flex flex-col grow gap-4">
                @foreach ($components as $component_name => $params)
                    @livewire($component_name, $params, key($component_name . '-' . $loop->index))
                @endforeach
            </div>
        </div>
    </div>

    <livewire:sn-components-footer />
</div>

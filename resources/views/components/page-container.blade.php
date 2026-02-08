@props([
    'scopeType',
    'scopeId',
])

@php
    use Wsmallnews\Cms\CmsPlugin;
    use Wsmallnews\Cms\Support\Utils;
@endphp

<div class="flex flex-col h-dvh">
    <div class="w-full shrink-0 flex h-32 overflow-hidden bg-[url({{ asset('image/banner.jpg') }})] bg-top-right bg-cover">
        <div class="container mx-auto flex items-center justify-between">
            <img src="{{ asset('image/logo.png') }}" alt="logo" class="h-full object-contain">

            <div class="flex gap-4">
                @auth
                    <livewire:sn-user-components-user-menu :module="app(CmsPlugin::class)->getId()" switch-dark-mode="{{ Utils::hasDarkMode() && !Utils::hasDarkModeForced() }}" />
                @else
                    <x-filament::button tag="a" href="{{ Utils::route('login') }}">
                        登录
                    </x-filament::button>
                    <x-filament::button color="gray" tag="a" href="{{ Utils::route('register') }}">
                        注册
                    </x-filament::button>
                @endauth
            </div>
        </div>
    </div>

    <div class="w-full flex flex-col grow">
        <livewire:sn-cms-components-navigation :scope-type="$scopeType" :scope-id="$scopeId" />

        {{ $slot }}

        <livewire:sn-cms-components-footer :scope-type="$scopeType" :scope-id="$scopeId" />
    </div>
</div>
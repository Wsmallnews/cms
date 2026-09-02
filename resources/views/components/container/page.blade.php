@props([
    'scopeType',
    'scopeId',
])

@php
    use Wsmallnews\Cms\CmsPlugin;
    use Wsmallnews\Cms\Support\Utils;
@endphp

<div {{ $attributes->merge(['class' => 'sn-cms-container-page w-full flex flex-col h-dvh']) }}>
    {{-- 头部不能加 overflow-hidden，否则搜索下拉会被 banner 裁剪 --}}
    <div class="w-full shrink-0 flex h-32 bg-[url({{ asset('image/banner.jpg') }})] bg-top-right bg-cover">
        <div class="container mx-auto flex items-center justify-between gap-4">
            <img src="{{ asset('image/logo.png') }}" alt="logo" class="h-16 lg:h-20 shrink-0 object-contain">

            {{-- 搜索框与登录注册/个人信息 合并为一个容器整体靠右；搜索框聚焦时展开 --}}
            <div class="hidden lg:flex items-center justify-end grow gap-6">
                @if (Utils::getConfig('search.enabled', true))
                    <div class="w-56 xl:w-64 focus-within:w-80 transition-[width] duration-300 ease-in-out">
                        <livewire:sn-support::components.search
                            :limit="5"
                            :module="app(CmsPlugin::class)->getId()"
                            :display="Utils::getConfig('search.display')"
                            placeholder="{{ __('sn-cms::cms.frontend.search_placeholder') }}"
                        />
                    </div>
                @endif

                <div class="flex gap-4 shrink-0">
                    @auth(Utils::getConfig('guard', 'web'))
                        <livewire:sn-user::components.user.menu :module="app(CmsPlugin::class)->getId()" switch-dark-mode="{{ Utils::hasDarkMode() && !Utils::hasDarkModeForced() }}" />
                    @else
                        <x-filament::button tag="a" href="{{ Utils::route('login') }}">
                            {{ __('sn-cms::cms.frontend.login') }}
                        </x-filament::button>
                        <x-filament::button color="gray" tag="a" href="{{ Utils::route('register') }}">
                            {{ __('sn-cms::cms.frontend.register') }}
                        </x-filament::button>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <div class="w-full flex flex-col grow">
        <livewire:sn-cms::components.navigation.navigation :scope-type="$scopeType" :scope-id="$scopeId" />

        {{ $slot }}

        <livewire:sn-cms::components.footer :scope-type="$scopeType" :scope-id="$scopeId" />
    </div>
</div>
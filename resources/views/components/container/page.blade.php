@props([
    'scopeType',
    'scopeId',
])

@php
    use Wsmallnews\Cms\CmsPlugin;
    use Wsmallnews\Cms\Settings\GeneralSettings;
    use Wsmallnews\Cms\Support\Utils;

    $general = app(GeneralSettings::class);
    $siteName = filled($general->site_name) ? $general->site_name : config('app.name');
    $logoUrl = filled($general->logo) ? files_url($general->logo) : null;
    $bannerUrl = filled($general->homepage_banner) ? files_url($general->homepage_banner) : null;
    $hasBanner = filled($bannerUrl);
@endphp

<div {{ $attributes->merge(['class' => 'sn-cms-container-page w-full flex flex-col h-dvh']) }}>
    {{-- 头部不能加 overflow-hidden，否则搜索下拉会被 banner 裁剪；动态 URL 不能用 bg-[url()] 任意值类（Tailwind 编译期扫描不到），须内联 style --}}
    <div class="w-full shrink-0 flex h-32 bg-top-right bg-cover" @if ($hasBanner) style="background-image: url('{{ $bannerUrl }}')" @endif>
        <div class="container mx-auto flex items-center justify-between gap-4">
            @if ($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="h-16 lg:h-20 shrink-0 object-contain">
            @else
                {{-- 无 logo 时回退渲染站点名称文字（同 Filament 面板 brand 的做法） --}}
                <div class="h-16 lg:h-20 shrink-0 flex items-center">
                    <span class="text-2xl lg:text-3xl font-bold tracking-wide {{ $hasBanner ? 'text-white drop-shadow-md' : 'sn-primary-text' }}">{{ $siteName }}</span>
                </div>
            @endif

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
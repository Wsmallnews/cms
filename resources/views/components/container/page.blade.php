@props([
    'scopeType',
    'scopeId',
])

@php
    use Wsmallnews\Cms\CmsPlugin;
    use Wsmallnews\Cms\Settings\GeneralSettings;
    use Wsmallnews\Cms\Support\NavigationContext;
    use Wsmallnews\Cms\Support\Utils;

    $general = app(GeneralSettings::class);
    $siteName = filled($general->site_name) ? $general->site_name : config('app.name');
    $logoUrl = filled($general->logo) ? files_url($general->logo) : null;
    $bannerUrl = filled($general->homepage_banner) ? files_url($general->homepage_banner) : null;
    $hasBanner = filled($bannerUrl);

    // 导航分区装饰：当前请求匹配到导航时，显示该分区 banner（通栏，自身无图继承最近祖先）
    $sectionBannerUrl = NavigationContext::bannerUrl($scopeType, $scopeId);
@endphp

<div {{ $attributes->merge(['class' => 'sn-cms-container-page w-full flex flex-col h-dvh']) }}>
    {{-- 头部不能加 overflow-hidden，否则搜索下拉会被 banner 裁剪；动态 URL 不能用 bg-[url()] 任意值类（Tailwind 编译期扫描不到），须内联 style --}}
    {{-- 无 banner 时收窄头部条高度，避免出现大片空白 --}}
    <div class="w-full shrink-0 flex bg-top-right bg-cover {{ $hasBanner ? 'h-32' : 'h-24 lg:h-28' }}" @if ($hasBanner) style="background-image: url('{{ $bannerUrl }}')" @endif>
        <div class="container mx-auto sn-page-x flex items-center justify-between gap-4">
            {{-- 品牌（logo + 站名，与页脚统一逻辑；banner 上站名用白字压图） --}}
            <x-sn-cms::brand
                :logo-url="$logoUrl"
                :site-name="$siteName"
                :with-name="$general->logo_with_site_name"
                size="header"
                :name-class="$hasBanner ? 'text-2xl lg:text-3xl font-bold tracking-wide text-white drop-shadow-md' : null"
            />

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
        {{-- 导航组件只占 w-full（无容器无边框，可随处嵌入）；独立放置时由调用处提供容器与背景：
            primary 文字是白色，必须设置深色背景（sn-primary-bg），否则不可见；minimal 常规白底/深底 + 底边线 --}}
        @php
            $navStyle = Utils::navigationConfig('style', 'primary');
        @endphp
        <div @class([
            'w-full',
            'sn-primary-bg' => $navStyle === 'primary',
            'bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700' => $navStyle === 'minimal',
        ])>
            <div class="container mx-auto sn-page-x">
                <livewire:sn-cms::components.navigation.navigation :scope-type="$scopeType" :scope-id="$scopeId">
                    {{-- 用户区（移动端菜单底部）：在 cms 的模块语境里生成链接与组件参数 --}}
                    <x-slot:userZone>
                        @auth(Utils::getConfig('guard', 'web'))
                            <livewire:sn-user::components.user.menu :module="app(CmsPlugin::class)->getId()" placement="bottom-start" switch-dark-mode="{{ Utils::hasDarkMode() && ! Utils::hasDarkModeForced() }}" />
                        @else
                            <div class="flex gap-3">
                                <x-filament::button tag="a" href="{{ Utils::route('login') }}" class="flex-1">
                                    {{ __('sn-cms::cms.frontend.login') }}
                                </x-filament::button>
                                <x-filament::button color="gray" tag="a" href="{{ Utils::route('register') }}" class="flex-1">
                                    {{ __('sn-cms::cms.frontend.register') }}
                                </x-filament::button>
                            </div>
                        @endauth
                    </x-slot:userZone>
                </livewire:sn-cms::components.navigation.navigation>
            </div>
        </div>

        {{-- 分区 banner：导航条下方的通栏横图（container 外全宽）；匹配节点自身无图时继承最近祖先 --}}
        @if (filled($sectionBannerUrl))
            <div class="w-full shrink-0">
                <img src="{{ $sectionBannerUrl }}" class="w-full bg-gray-100 dark:bg-gray-800" loading="lazy" alt="">
            </div>
        @endif

        {{-- ===== 页面级容器（sn-page 全站唯一，页面视图禁止再写）：宽度对齐 + 页面节奏 + 兄弟导航注入 =====
            brothers 开关与形态由 sn-cms.php navigation 段配置：
            - top = 内容上方（PC 按钮排 / 手机手风琴卡片），间距由 sn-page 的 gap 提供
            - sidebar = 左侧分栏（lg 4 列 / xl 5 列，主列 3/4；lg 以下侧栏卡片在内容上方堆叠）
            内容级响应式一律容器断点（sn-content 自带 @container，posts 等全页组件消费最近容器） --}}
        @php
            $brothersEnabled = Utils::navigationConfig('brothers_enabled', true);
            $brothersLayout = Utils::navigationConfig('brothers_layout', 'top');
            $hasBrothers = $brothersEnabled && NavigationContext::hasBrothers($scopeType, $scopeId);
        @endphp

        <div class="w-full @container">
            <div class="sn-page">
                @if ($brothersEnabled && $brothersLayout === 'top' && $hasBrothers)
                    {{-- top 形态：内容上方（含手机手风琴），组件自身处理断点显隐 --}}
                    <livewire:sn-cms::components.navigation.brothers :scope-type="$scopeType" :scope-id="$scopeId" />
                @endif

                @if ($brothersEnabled && $brothersLayout === 'sidebar' && $hasBrothers)
                    {{-- sidebar 形态：左栏手风琴卡片 + 主列分栏（sn-split：容器断点，brothers 侧栏有无切换时行为一致） --}}
                    <div class="sn-split">
                        <aside class="w-full min-w-0">
                            <livewire:sn-cms::components.navigation.brothers :scope-type="$scopeType" :scope-id="$scopeId" layout="sidebar" />
                        </aside>
                        <div class="sn-split-main">
                            {{ $slot }}
                        </div>
                    </div>
                @else
                    {{ $slot }}
                @endif
            </div>
        </div>

        {{-- 页脚：footer 差异实例的 scope 在调用处解析后显式传入（组件内部不解析 scope） --}}
        @php
            $footerScopeable = Utils::getScopeable('footer');
        @endphp
        <livewire:sn-cms::components.footer :scope-type="$footerScopeable['scope_type']" :scope-id="$footerScopeable['scope_id']" />
    </div>
</div>
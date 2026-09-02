@php
    use Wsmallnews\Cms\CmsPlugin;
    use Wsmallnews\Cms\Support\Utils;
    
    $nestedset = $this->getNestedset();
@endphp

<nav class="sn-primary-bg w-full" x-data="{ mobileMenuIsOpen: false }" @click.away="mobileMenuIsOpen = false">
    <div class="container hidden lg:flex h-16 mx-auto px-4 sm:px-0">
        <ul class="flex h-full" role="menu">
            @foreach ($nestedset as $navigation)
                @php
                    $hasChild = $navigation->children->count() > 0;
                @endphp
                <li @class([
                        'sn-primary-bg sn-hover min-w-32 flex items-center relative group/child',
                        'sn-active' => $navigation->has_active,
                    ])
                    @if ($hasChild)
                        x-data="{ isOpen: false, openedWithKeyboard: false, leaveTimeout: null }"
                        x-on:mouseover="isOpen = true; leaveTimeout ? clearTimeout(leaveTimeout) : true"
                        x-on:mouseleave.prevent="leaveTimeout = setTimeout(() => { isOpen = false }, 50)"
                        x-on:keydown.esc.prevent="isOpen = false, openedWithKeyboard = false"
                        x-on:click.outside="isOpen = false, openedWithKeyboard = false"
                    @endif
                    role="menuitem"
                >
                    <a class="flex w-full h-full justify-center items-center px-4 font-semibold text-white gap-2 underline-offset-2 focus:outline-hidden focus:underline"
                        @if ($hasChild)
                            href="javascript:;"
                            x-on:keydown.space.prevent="openedWithKeyboard = true"
                            x-on:keydown.enter.prevent="openedWithKeyboard = true"
                            x-on:keydown.down.prevent="openedWithKeyboard = true"
                            x-bind:aria-expanded="isOpen || openedWithKeyboard"
                            aria-haspopup="true"
                        @else
                            {{ \Filament\Support\generate_href_html($navigation->url_info['url'], $navigation->url_info['target'] ?? false) }}
                        @endif
                    >
                        {{ $navigation->name_label }}
                        @if ($hasChild)
                            <x-filament::icon icon="heroicon-m-chevron-down" class="size-6 font-semibold transform transition-transform duration-300 rotate-0 group-hover/child:rotate-180" aria-hidden="true" />
                        @endif
                    </a>

                    @if ($hasChild) 
                        <div class="sn-primary-bg w-full absolute top-full left-0 z-10"
                            x-cloak x-show="isOpen || openedWithKeyboard"
                            x-transition
                            x-trap="openedWithKeyboard"
                        >
                            <ul class="flex flex-col" role="menu">
                                @foreach ($navigation->children as $child)
                                    @php
                                        $hasGrandChild = $child->children->count() > 0;
                                    @endphp
                                    <li @class([
                                            'sn-primary-bg sn-hover w-full h-14 flex items-center relative group/grandchild',
                                            'sn-active' => $child->has_active,
                                        ])
                                        @if ($hasGrandChild)
                                            x-data="{ isOpen: false, openedWithKeyboard: false, leaveTimeout: null }"
                                            x-on:mouseover="isOpen = true; leaveTimeout ? clearTimeout(leaveTimeout) : true"
                                            x-on:mouseleave.prevent="leaveTimeout = setTimeout(() => { isOpen = false }, 50)"
                                            x-on:keydown.esc.prevent="isOpen = false, openedWithKeyboard = false"
                                            x-on:click.outside="isOpen = false, openedWithKeyboard = false"
                                        @endif
                                        role="menuitem"
                                    >
                                        <a class="flex w-full h-full justify-between items-center px-4 font-semibold text-white gap-2 underline-offset-2 focus:outline-hidden focus:underline"
                                            @if ($hasGrandChild)
                                                href="javascript:;"
                                                x-on:keydown.space.prevent="openedWithKeyboard = true"
                                                x-on:keydown.enter.prevent="openedWithKeyboard = true"
                                                x-on:keydown.down.prevent="openedWithKeyboard = true"
                                                x-bind:aria-expanded="isOpen || openedWithKeyboard"
                                                aria-haspopup="true"
                                            @else
                                                {{ \Filament\Support\generate_href_html($child->url_info['url'], $child->url_info['target'] ?? false) }}
                                            @endif
                                        >
                                            {{ $child->name_label }}
                                            @if ($hasGrandChild)
                                                <x-filament::icon icon="heroicon-m-chevron-down" class="size-6 font-semibold transform transition-transform duration-300 rotate-0 group-hover/child:-rotate-90" aria-hidden="true" />
                                            @endif
                                        </a>

                                        @if ($hasGrandChild) 
                                            <div class="sn-primary-bg w-full absolute top-0 left-full"
                                                x-cloak x-show="isOpen || openedWithKeyboard"
                                                x-transition
                                                x-trap="openedWithKeyboard"
                                            >
                                                <ul class="flex flex-col" role="menu">
                                                    @foreach ($child->children as $grandChild)
                                                        <li @class([
                                                                'sn-primary-bg sn-hover w-full h-12 flex items-center',
                                                                'sn-active' => $grandChild->has_active,
                                                            ])
                                                            role="menuitem"
                                                        >
                                                            <a class="flex w-full h-full justify-between items-center px-4 font-semibold text-white gap-2 underline-offset-2 focus:outline-hidden focus:underline"
                                                                
                                                                {{ \Filament\Support\generate_href_html($grandChild->url_info['url'], $grandChild->url_info['target'] ?? false) }}
                                                            >
                                                                {{ $grandChild->name_label }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Mobile Menu Button -->
    {{-- 收起时位于亮色页头上，用深色图标；展开时位于主题色面板上，用白色图标 + 半透明底 --}}
    <button class="inline-flex items-center justify-center min-w-11 min-h-11 rounded-md cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary-500 transition-colors duration-200 motion-reduce:transition-none lg:hidden"
        @click="mobileMenuIsOpen = !mobileMenuIsOpen"
        :aria-expanded="mobileMenuIsOpen"
        x-bind:class="mobileMenuIsOpen
            ? 'fixed top-3 right-3 z-30 text-white bg-white/15 hover:bg-white/25'
            : 'absolute top-3 right-3 z-20 text-gray-700 hover:bg-primary-600 hover:text-white dark:text-gray-100 dark:hover:text-white'"
        type="button"
        aria-label="{{ __('sn-cms::cms.frontend.mobile_menu') }}"
        aria-controls="mobileMenu"
    >
        <x-filament::icon icon="heroicon-m-bars-3" class="size-6" x-cloak x-show="!mobileMenuIsOpen" aria-hidden="true" />
        <x-filament::icon icon="heroicon-m-x-mark" class="size-6" x-cloak x-show="mobileMenuIsOpen" aria-hidden="true" />
    </button>

    {{-- 移动端菜单展开时，顶部显示全局搜索和登录注册/个人信息（lg 以下；桌面端在页头）。
        pr-16 给右上角关闭按钮让位，避免压住输入框 --}}
    @if (Utils::getConfig('search.enabled', true))
        <div
            class="sn-primary-bg w-full fixed inset-x-0 top-0 z-20 pl-4 pr-16 pt-5 pb-4 lg:hidden"
            x-cloak x-show="mobileMenuIsOpen"
        >
            <livewire:sn-support::components.search
                :limit="5"
                :module="app(CmsPlugin::class)->getId()"
                :display="Utils::getConfig('search.display')"
                placeholder="{{ __('sn-cms::cms.frontend.search_placeholder') }}"
            />
        </div>
    @endif

    <!-- Mobile Menu -->
    {{-- pt-24 为顶部固定定位的关闭按钮（top-3 + 高 44px）保留安全距离，避免盖住首个导航项 --}}
    <ul
        @class([
            'sn-primary-bg w-full flex flex-col fixed max-h-svh overflow-y-auto inset-x-0 top-0 z-10 rounded-b-md pb-6 pt-24 divide-y divide-primary-400 lg:hidden',
        ])
        x-cloak x-show="mobileMenuIsOpen"
        x-transition:enter="transition motion-reduce:transition-none ease-out duration-300"
        x-transition:enter-start="-translate-y-full" x-transition:enter-end="translate-y-0"
        x-transition:leave="transition motion-reduce:transition-none ease-out duration-300"
        x-transition:leave-start="translate-y-0" x-transition:leave-end="-translate-y-full"
        id="mobileMenu"
        role="menu"
    >
        @forelse($nestedset as $treeKey => $record)
            <x-dynamic-component 
                @class([
                    'w-full',
                ]) 
                :component="$this->getRecordView()" 
                key="categories-component-{{ $record->getKey() }}" 
                :record="$record" 
                :first="$loop->first" 
                :last="$loop->last" 
                :current-level="1" 
            />
        @empty
            <li class="w-full px-3 py-2 text-center">
                {{ $this->getEmptyLabel() ?: __('sn-filament-nestedset::nestedset.nestedset.empty_label')}}
            </li>
        @endforelse

        {{-- lg 以下页头不展示登录注册/个人信息，收纳在移动端菜单底部；弹卡从左侧触发器向右展开，避免超出视口 --}}
        <li class="w-full px-3 py-4">
            @auth(Utils::getConfig('guard', 'web'))
                <livewire:sn-user::components.user.menu :module="app(CmsPlugin::class)->getId()" placement="bottom-start" switch-dark-mode="{{ Utils::hasDarkMode() && !Utils::hasDarkModeForced() }}" />
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
        </li>
    </ul>
</nav>
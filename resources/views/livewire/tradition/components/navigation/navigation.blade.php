@php
    use Filament\Support\Icons\Heroicon;
    use Wsmallnews\Cms\CmsPlugin;
    use Wsmallnews\Cms\Support\Utils;

    $nestedset = $this->getNestedset();

    // 导航配置（config/sn-cms.php 顶层 navigation 节）
    $style = Utils::navigationConfig('style', 'primary');
    $desktopStyle = Utils::navigationConfig('desktop_submenu_style', 'cascade');
    $desktopTrigger = Utils::navigationConfig('desktop_submenu_trigger', 'hover');   // cascade 深层跟随；accordion 仅一级生效
    $itemStyle = Utils::navigationConfig('desktop_item_style', 'flush');             // 一级 hover/选中形态：flush 通栏 | rounded 胶囊
    $moreStyle = Utils::navigationConfig('more_submenu_style', 'accordion');
    $moreTrigger = $moreStyle === 'cascade' ? Utils::navigationConfig('more_submenu_trigger', 'click') : 'click';
    $moreIconOnly = (bool) Utils::navigationConfig('more_icon_only', true);

    // 父项可点击：仅 hover 级联生效（直达第一个可用叶子）
    $clickable = Utils::isDesktopParentClickable();
    $moreClickable = $moreTrigger === 'hover' && (bool) Utils::navigationConfig('parent_clickable', true);

    // 递归 partial 路径按当前主题解析（主题切换时随主视图整体替换）
    $cascadeItemView = $this->getThemeView('components.navigation.partials.cascade-item');
    $accordionItemView = $this->getThemeView('components.navigation.partials.accordion-item');

    // 一级链接形态类：通栏占满行高；胶囊上下留呼吸边 + 控件级圆角
    $itemShapeClass = $itemStyle === 'rounded' ? 'my-2 rounded-md' : 'h-full';
@endphp

<nav @class(['sn-cms-nav w-full', 'sn-cms-nav-minimal' => $style === 'minimal'])
    x-data="snCmsNav({ cascadeTrigger: '{{ $desktopTrigger }}', moreTrigger: '{{ $moreTrigger }}' })"
    @click.away="mobileMenuIsOpen = false; moreOpen = false; closeAllCascade()"
>
    {{-- ===== PC 主行（lg+）：全量渲染 + Alpine 测量溢出折叠（数据不依赖后端，JS 只做显隐）=====
        组件只占 w-full，容器由调用处外层包裹；显隐走纯 CSS（hidden/lg:flex + 布局内联关键 CSS 兜底 pre-CSS 闪块）；
        overflow-x-clip：溢出测量前的全量渲染在本组件内裁切消化，不顶出页面横向滚动条（flyout 有右缘反向保证不超容器） --}}
    <div class="sn-cms-nav-bar hidden lg:flex h-16 w-full overflow-x-clip"
        x-ref="bar"
        @mouseover="cascadeOver($event)"
        @mouseleave="barLeave()"
    >
        <ul @class([
            'sn-cms-nav-list flex h-full min-w-0 flex-1',
            // 胶囊形态下项与项之间留间距（flush 通栏保持连续）
            'gap-1.5' => $itemStyle === 'rounded',
        ]) x-ref="list" role="menu">
            @foreach ($nestedset as $navigation)
                @php
                    $hasChild = $navigation->children->count() > 0;
                    $leafUrl = $hasChild && $clickable ? $navigation->first_leaf_url : null;
                @endphp
                <li @class(['sn-cms-nav-item relative flex', 'has-sub' => $hasChild, 'is-active' => $navigation->has_active])
                    data-active="{{ $navigation->has_active ? 1 : 0 }}"
                    role="none"
                >
                    @if ($desktopStyle === 'accordion' && $hasChild)
                        {{-- 手风琴：一级按 trigger（默认 hover）展开，面板内部固定 click；父项纯展开 --}}
                        <a @class([
                            'sn-cms-nav-link flex min-w-24 w-full items-center justify-center gap-1.5 px-4 text-sm font-semibold whitespace-nowrap cursor-pointer underline-offset-2 focus:outline-hidden focus-visible:underline',
                            $itemShapeClass,
                        ])
                            href="javascript:;" role="menuitem"
                            aria-haspopup="true" aria-expanded="false"
                            @click="cascadeClick($event)"
                            @keydown.down.prevent="cascadeOpenByKey($event)"
                            @keydown.esc.prevent="closeAllCascade()"
                            wire:click="$dispatch('sn-cms-navigation-node-click', { recordId: {{ $navigation->id }}, hasChild: 1 })"
                        >
                            {{ $navigation->name_label }}
                            <x-filament::icon :icon="Heroicon::ChevronDown" class="sn-cms-chev" aria-hidden="true" />
                        </a>

                        <div class="sn-cms-sub depth-1 py-1" role="menu">
                            <ul class="sn-cms-acc flex flex-col">
                                @foreach ($navigation->children as $child)
                                    {{-- depthOffset=1：面板根是二级导航（绝对 depth 从 1 起），转为相对层级让首层不缩进 --}}
                                    @include($accordionItemView, ['record' => $child, 'depthOffset' => 1])
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <a @class([
                            'sn-cms-nav-link flex min-w-24 w-full items-center justify-center gap-1.5 px-4 text-sm font-semibold whitespace-nowrap cursor-pointer underline-offset-2 focus:outline-hidden focus-visible:underline',
                            $itemShapeClass,
                        ])
                            role="menuitem"
                            @if ($hasChild)
                                {{-- hover 级联 + parent_clickable + 有可用叶子 → 真实链接直达第一个叶子；否则纯展开 --}}
                                @if ($leafUrl)
                                    {{ \Filament\Support\generate_href_html($leafUrl) }}
                                @else
                                    href="javascript:;"
                                @endif
                                aria-haspopup="true"
                                aria-expanded="false"
                                @click="cascadeClick($event)"
                                @keydown.down.prevent="cascadeOpenByKey($event)"
                                @keydown.esc.prevent="closeAllCascade()"
                                wire:click="$dispatch('sn-cms-navigation-node-click', { recordId: {{ $navigation->id }}, hasChild: 1 })"
                            @else
                                {{ \Filament\Support\generate_href_html($navigation->url_info['url'], $navigation->url_info['target'] ?? false) }}
                                wire:click="$dispatch('sn-cms-navigation-leaf-click', { recordId: {{ $navigation->id }}, hasChild: 0 })"
                            @endif
                        >
                            {{ $navigation->name_label }}
                            @if ($hasChild)
                                <x-filament::icon :icon="Heroicon::ChevronDown" class="sn-cms-chev" aria-hidden="true" />
                            @endif
                        </a>

                        @if ($hasChild)
                            {{-- 级联：一级向下弹，深层向右（右缘溢出反向）--}}
                            <ul class="sn-cms-sub depth-1" role="menu">
                                @foreach ($navigation->children as $child)
                                    @include($cascadeItemView, ['record' => $child])
                                @endforeach
                            </ul>
                        @endif
                    @endif
                </li>
            @endforeach
        </ul>

        {{-- ===== "更多"按钮（溢出折叠）：自动占据最后一个能放下的导航位置 ===== --}}
        <div class="sn-cms-nav-more relative flex flex-none" x-ref="more" data-sn-more
            :class="{ 'is-open': moreOpen, 'has-active': moreHasActive }"
        >
            <button type="button" @class([
                'sn-cms-nav-more-btn flex items-center justify-center min-w-12 h-full gap-1.5 text-sm font-semibold whitespace-nowrap cursor-pointer focus:outline-hidden focus-visible:underline underline-offset-2',
                // 纯图标居中不需要横向内边距；带文字时与一级导航对齐（px-4）
                'px-1' => $moreIconOnly,
                'px-4' => ! $moreIconOnly,
            ])
                @click="moreOpen = ! moreOpen"
                :aria-expanded="moreOpen ? 'true' : 'false'"
                aria-haspopup="true"
                aria-label="{{ __('sn-cms::cms.frontend.more') }}"
            >
                @if ($moreIconOnly)
                    <x-filament::icon :icon="Heroicon::EllipsisHorizontal" class="size-6" aria-hidden="true" />
                @else
                    <span>{{ __('sn-cms::cms.frontend.more') }}</span>
                    <x-filament::icon :icon="Heroicon::ChevronDown" class="sn-cms-chev" aria-hidden="true" />
                @endif
            </button>

            {{-- cascade 模式禁止滚动容器：overflow 会裁切向左/右弹出的 flyout 子菜单（层级显示不全 + 横向滚动条），
                长列表交由页面滚动；accordion 模式无 flyout，保留限高滚动 + 细滚动条 --}}
            <div @class([
                'sn-cms-nav-more-menu',
                'max-h-[60vh] overflow-y-auto sn-scrollbar' => $moreStyle !== 'cascade',
            ]) x-cloak x-show="moreOpen" x-transition>
                @if ($moreStyle === 'cascade')
                    <ul class="relative" role="menu">
                        @foreach ($nestedset as $i => $navigation)
                            @include($cascadeItemView, [
                                'record' => $navigation,
                                'clickable' => $moreClickable,
                                'ovIdx' => $i,
                            ])
                        @endforeach
                    </ul>
                @else
                    <ul class="sn-cms-acc flex flex-col" role="menu">
                        @foreach ($nestedset as $i => $navigation)
                            @include($accordionItemView, [
                                'record' => $navigation,
                                'ovIdx' => $i,
                            ])
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    {{-- ===== 移动端菜单按钮（< lg 汉堡）=====
        定位类直接写死（不依赖 Alpine :class）：初始化前按钮若在文档流内，会撑起 nav 高度把调用方背景透成一条色带 --}}
    <button type="button"
        class="sn-cms-nav-burger absolute top-3 right-3 z-20 inline-flex items-center justify-center min-w-11 min-h-11 rounded-md cursor-pointer transition-colors duration-200 motion-reduce:transition-none lg:hidden focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2"
        @click="mobileMenuIsOpen = ! mobileMenuIsOpen"
        :aria-expanded="mobileMenuIsOpen ? 'true' : 'false'"
        :class="mobileMenuIsOpen ? 'is-open' : 'is-closed'"
        aria-label="{{ __('sn-cms::cms.frontend.mobile_menu') }}"
        aria-controls="mobileMenu"
    >
        <x-filament::icon :icon="Heroicon::Bars3" class="size-6" x-cloak x-show="!mobileMenuIsOpen" aria-hidden="true" />
        <x-filament::icon :icon="Heroicon::XMark" class="size-6" x-cloak x-show="mobileMenuIsOpen" aria-hidden="true" />
    </button>

    {{-- 移动端菜单展开时，顶部显示全局搜索和登录注册/个人信息（lg 以下；桌面端在页头）。
        pr-16 给右上角关闭按钮让位，避免压住输入框 --}}
    @if (Utils::getConfig('search.enabled', true))
        <div class="sn-cms-nav-search-strip w-full fixed inset-x-0 top-0 z-20 pl-4 pr-16 pt-5 pb-4 lg:hidden"
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

    {{-- ===== 移动端菜单（< lg）：交互保持现状（手风琴 + 点击展开、激活链默认展开），配色随 style 换肤 ===== --}}
    {{-- pt-24 为顶部固定定位的关闭按钮（top-3 + 高 44px）保留安全距离，避免盖住首个导航项 --}}
    <ul
        @class([
            'sn-cms-nav-mobile sn-cms-acc-divide w-full flex flex-col fixed max-h-svh overflow-y-auto inset-x-0 top-0 z-10 sn-rounded-b pb-6 pt-24 lg:hidden',
        ])
        x-cloak x-show="mobileMenuIsOpen"
        x-transition:enter="transition motion-reduce:transition-none ease-out duration-300"
        x-transition:enter-start="-translate-y-full" x-transition:enter-end="translate-y-0"
        x-transition:leave="transition motion-reduce:transition-none ease-out duration-300"
        x-transition:leave-start="translate-y-0" x-transition:leave-end="-translate-y-full"
        id="mobileMenu"
        role="menu"
    >
        @forelse($nestedset as $record)
            @include($accordionItemView, ['record' => $record, 'variant' => 'menu'])
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

@once
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('snCmsNav', (config) => ({
                hiddenFrom: null,          // 第一个放不下的主行项索引；null = 全部可见
                moreHasActive: false,      // 被折叠项中有激活态 → 更多按钮高亮
                moreOpen: false,
                mobileMenuIsOpen: false,
                cascadeCloseTimer: null,
                cascadeTrigger: config.cascadeTrigger,
                moreTrigger: config.moreTrigger,

                init() {
                    this.applyOverflow();
                    if (this.$refs.bar) {
                        // 容器尺寸变化（含 lg 断点显隐、侧栏等）时重新测量 + 重新定位展开中的子菜单
                        new ResizeObserver(() => {
                            this.applyOverflow();
                            this.repositionOpenCascades();
                        }).observe(this.$refs.bar);
                    }
                    // 字体加载改变项宽
                    if (document.fonts && document.fonts.ready) {
                        document.fonts.ready.then(() => this.applyOverflow());
                    }
                    this.$watch('moreOpen', (open) => {
                        if (! open) this.closeAllCascade();
                    });
                },

                /* ===== 溢出折叠测量：主行全量渲染 → 逐项累计宽度 → 第一个放不下的项起隐藏 ===== */
                applyOverflow() {
                    const list = this.$refs.list;
                    const more = this.$refs.more;
                    if (! list || ! more) return;

                    const items = Array.from(list.children);
                    // 先全部显示 + 显示更多按钮，测量自然宽度（更多按钮宽度按实际形态预留）
                    items.forEach((li) => li.style.display = '');
                    more.style.display = '';

                    // list 是 flex-1，clientWidth 已经排除了更多按钮占位，不能再重复扣减；
                    // 胶囊形态下列表带 gap，逐项累计时一并计入；+1 为取整累计的舍入保护
                    const gap = parseFloat(getComputedStyle(list).columnGap) || 0;
                    const avail = list.clientWidth + 1;
                    let used = 0;
                    let cut = items.length;
                    for (let i = 0; i < items.length; i++) {
                        used += items[i].offsetWidth + gap;
                        if (used > avail) { cut = i; break; }
                    }

                    const hidden = items.slice(cut);
                    if (hidden.length) {
                        hidden.forEach((li) => li.style.display = 'none');
                        this.hiddenFrom = cut;
                        this.moreHasActive = hidden.some((li) => li.dataset.active === '1');
                    } else {
                        more.style.display = 'none';
                        this.hiddenFrom = null;
                        this.moreHasActive = false;
                        this.moreOpen = false;
                    }
                },

                /* ===== 级联交互：hover 区（打开即时、关闭延迟 300ms、祖先链保持、同级互斥、右缘反向）===== */
                regionTrigger(target) {
                    return target.closest('[data-sn-more]') ? this.moreTrigger : this.cascadeTrigger;
                },

                cascadeOver(e) {
                    if (this.regionTrigger(e.target) !== 'hover') return;
                    this.clearCascadeTimer();

                    const li = e.target.closest('li.has-sub');
                    if (! li) {
                        // 鼠标在普通项/空白上：收起所有子菜单（子菜单内部除外）
                        if (! e.target.closest('.sn-cms-sub')) this.closeAllCascade();
                        return;
                    }

                    this.closeSiblingsOf(li);
                    this.setCascadeOpen(li, true);
                },

                barLeave() {
                    this.clearCascadeTimer();
                    this.cascadeCloseTimer = setTimeout(() => this.closeAllCascade(), 300);
                },

                clearCascadeTimer() {
                    if (this.cascadeCloseTimer) {
                        clearTimeout(this.cascadeCloseTimer);
                        this.cascadeCloseTimer = null;
                    }
                },

                /* 点击展开：click 区一律切换；hover 区真实链接放行跳转，占位链接（纯展开）也允许点击切换（触屏/键盘兜底） */
                cascadeClick(e) {
                    const li = e.target.closest('li.has-sub');
                    if (! li) return;

                    const link = li.querySelector(':scope > a');
                    const isPlaceholder = ! link || (link.getAttribute('href') || '').startsWith('javascript:');
                    if (this.regionTrigger(e.target) !== 'click' && ! isPlaceholder) return;
                    e.preventDefault();

                    const open = ! li.classList.contains('is-open');
                    if (open) this.closeSiblingsOf(li);
                    this.setCascadeOpen(li, open);
                },

                cascadeOpenByKey(e) {
                    const li = e.target.closest('li.has-sub');
                    if (! li) return;

                    this.clearCascadeTimer();
                    this.closeSiblingsOf(li);
                    this.setCascadeOpen(li, true);
                },

                /* 关闭 li 之外的所有打开项（保持祖先链） */
                closeSiblingsOf(li) {
                    const bar = this.$refs.bar;
                    if (! bar) return;

                    const chain = new Set();
                    let p = li;
                    while (p && p !== bar) {
                        chain.add(p);
                        p = p.parentElement ? p.parentElement.closest('li.has-sub') : null;
                    }
                    bar.querySelectorAll('li.has-sub.is-open').forEach((el) => {
                        if (! chain.has(el)) this.setCascadeOpen(el, false);
                    });
                },

                /* ===== 弹出方向检测 =====
                   裁切基准是导航条自身边界（overflow-x-clip 在此裁切；条在居中容器内，比视口窄），
                   右侧与去除滚动条的可视区取小。用 window.innerWidth 会把滚动条与容器留白都算进去，
                   导致面板实际已被裁切却判定"放得下" */
                flipBounds() {
                    const bar = this.$refs.bar;
                    const clientWidth = document.documentElement.clientWidth;
                    if (! bar) {
                        return { left: 0, right: clientWidth };
                    }

                    const rect = bar.getBoundingClientRect();

                    return { left: rect.left, right: Math.min(rect.right, clientWidth) };
                },

                /* 按左右边界决定弹出方向：默认向右；右缘放不下翻转向左；向左也放不下（深层链穿出条左缘）翻回向右 */
                positionCascade(li) {
                    const sub = li.querySelector(':scope > .sn-cms-sub');
                    if (! sub) return;

                    const margin = 8;    // 与边界保留的安全间距
                    const bounds = this.flipBounds();

                    sub.classList.remove('pop-left');
                    if (sub.getBoundingClientRect().right > bounds.right - margin) {
                        sub.classList.add('pop-left');
                        if (sub.getBoundingClientRect().left < bounds.left + margin) {
                            sub.classList.remove('pop-left');
                        }
                    }
                },

                /* 容器尺寸变化后，重新定位所有展开中的级联子菜单（方向可能需要翻转） */
                repositionOpenCascades() {
                    if (! this.$refs.bar) return;
                    this.$refs.bar.querySelectorAll('li.has-sub.is-open').forEach((li) => this.positionCascade(li));
                },

                setCascadeOpen(li, open) {
                    li.classList.toggle('is-open', open);
                    const link = li.querySelector(':scope > a');
                    if (link) link.setAttribute('aria-expanded', open ? 'true' : 'false');
                    if (! open) return;

                    this.positionCascade(li);
                },

                closeAllCascade() {
                    if (! this.$refs.bar) return;
                    this.$refs.bar.querySelectorAll('li.has-sub.is-open').forEach((el) => this.setCascadeOpen(el, false));
                },
            }));
        });
    </script>
@endonce

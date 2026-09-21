@php
    use Filament\Support\Icons\Heroicon;

    // 同级导航（brothers）：匹配到二级分组时展示兄弟节点，layout 形态在本视图内分支
    // 配置经 HasModuleContext 解析（消费模块的 navigation 节优先，回落 cms）
    $nestedset = $this->getNestedset();
    $style = $this->navigationConfig('style', 'primary');
    $submenuStyle = $this->navigationConfig('brothers_submenu_style', 'cascade');
    $alignClass = match ($this->navigationConfig('brothers_align', 'left')) {
        'center' => 'justify-center',
        'right' => 'justify-end',
        default => null,
    };

    // 递归 partial 路径按当前主题解析（与主导航主视图一致；include 子视图无 $this 绑定，需传入）
    $cascadeItemView = $this->getThemeView('components.navigation.partials.cascade-item');
    $accordionItemView = $this->getThemeView('components.navigation.partials.accordion-item');

    // hover 级联下父项直达第一个可用叶子（与主导航 PC 主行同语义）
    $clickable = $this->isDesktopParentClickable();

    // 独立按钮自带边界：primary = 主题色面板；minimal = 白底描边（文字/hover/激活色由皮肤规则接管）
    $buttonSkinClass = $style === 'minimal'
        ? 'bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-700'
        : 'sn-primary-bg';

    // 手风琴卡片类（sidebar 形态与 top 的手机段共用）：区块级圆角 + 阴影（页面卡片语言）
    $accordionCardClass = [
        'sn-cms-nav sn-cms-brothers sn-rounded w-full flex flex-col py-2 overflow-hidden shadow-(--sn-shadow-card)',
    ];
    $style === 'minimal' && $accordionCardClass[] = 'sn-cms-nav-minimal';
@endphp

@if ($nestedset->isEmpty())
    {{-- 无上下文 / 匹配到顶级节点：隐藏占位（Livewire 需要根节点），零视觉占位 --}}
    <div hidden></div>
@elseif ($this->layout === 'sidebar')
    {{-- sidebar 形态：左侧手风琴卡片（多级），区块级圆角 + 阴影（页面卡片语言） --}}
    <ul @class($accordionCardClass)
        role="menu"
    >
        @foreach ($nestedset as $record)
            {{-- depthOffset=1：侧栏根是二级分组的兄弟（绝对 depth 从 1 起），转为相对层级让首层不缩进 --}}
            @include($accordionItemView, ['record' => $record, 'variant' => 'menu', 'depthOffset' => 1])
        @endforeach
    </ul>
@else
    {{-- ===== top 形态：外层单根（Livewire 组件要求），PC 按钮排与手机手风琴按断点显隐 ===== --}}
    <div class="w-full">

    {{-- PC（lg+）：一排独立导航按钮（主导航一级项同款皮肤与类名）；有子级时 hover 弹下拉，
        展开形式由 brothers_submenu_style 配置（cascade 级联 / accordion 手风琴，与主导航 PC 菜单同款两种形态） --}}
    <nav @class([
        'sn-cms-nav sn-cms-brothers-top hidden lg:flex flex-wrap items-center sn-gap w-full',
        $alignClass,
        'sn-cms-nav-minimal' => $style === 'minimal',
    ])
        role="menu"
    >
        @foreach ($nestedset as $record)
            @php
                $hasChild = $record->children->count() > 0;
                $leafUrl = $hasChild && $clickable ? $record->first_leaf_url : null;
            @endphp

            <div @class([
                'sn-cms-nav-item relative flex',
                'has-sub' => $hasChild,
                // has_active 含子级激活链（与主导航选中语义一致）
                'is-active' => $record->has_active,
            ])
                role="none"
            >
                <a @class([
                    'sn-cms-nav-link flex items-center gap-1.5 px-4 py-2.5 rounded-md text-sm font-semibold whitespace-nowrap cursor-pointer underline-offset-2 focus:outline-hidden focus-visible:underline',
                    $buttonSkinClass,
                ])
                    role="menuitem"
                    @if ($hasChild)
                        @if ($leafUrl)
                            {{ \Filament\Support\generate_href_html($leafUrl) }}
                        @else
                            href="javascript:;"
                        @endif
                        aria-haspopup="true" aria-expanded="false"
                        wire:click="$dispatch('sn-cms-navigation-node-click', { recordId: {{ $record->id }}, hasChild: 1 })"
                    @else
                        {{ \Filament\Support\generate_href_html($record->url_info['url'], $record->url_info['target'] ?? false) }}
                        wire:click="$dispatch('sn-cms-navigation-leaf-click', { recordId: {{ $record->id }}, hasChild: 0 })"
                    @endif
                >
                    {{ $record->name_label }}
                    @if ($hasChild)
                        <x-filament::icon :icon="Heroicon::ChevronDown" class="sn-cms-chev" aria-hidden="true" />
                    @endif
                </a>

                @if ($hasChild)
                    @if ($submenuStyle === 'accordion')
                        {{-- 手风琴：下拉面板内点击展开层级（与主导航 PC accordion 分支同构）；depthOffset=1 让面板首层不缩进 --}}
                        <div class="sn-cms-sub depth-1 py-1" role="menu">
                            <ul class="sn-cms-acc flex flex-col">
                                @foreach ($record->children as $child)
                                    @include($accordionItemView, ['record' => $child, 'depthOffset' => 1])
                                @endforeach
                            </ul>
                        </div>
                    @else
                        {{-- 级联：一级向下弹，深层向右（与主导航共用 cascade-item） --}}
                        <ul class="sn-cms-sub depth-1" role="menu">
                            @foreach ($record->children as $child)
                                @include($cascadeItemView, ['record' => $child])
                            @endforeach
                        </ul>
                    @endif
                @endif
            </div>
        @endforeach
    </nav>

    {{-- 手机（< lg）：sidebar 同款手风琴卡片（原侧边栏样式，触控友好多级展开） --}}
    <div class="lg:hidden w-full">
        <ul @class($accordionCardClass)
            role="menu"
        >
            @foreach ($nestedset as $record)
                @include($accordionItemView, ['record' => $record, 'variant' => 'menu', 'depthOffset' => 1])
            @endforeach
        </ul>
    </div>

    </div>
@endif

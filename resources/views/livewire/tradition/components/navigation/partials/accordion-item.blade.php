@php
    use Filament\Support\Icons\Heroicon;

    // 递归手风琴项：「更多」下拉 / PC 主行 accordion / 移动端菜单 / Brothers 同级导航共用
    // 父子级区分 = 缩进块（在 <a> 内部、文字之前，N = depth，hover/选中背景通栏）+ 父级字重 600 / 叶子 400
    // 上下文变量：$record（必传）、$defaultOpen（默认 has_active 时展开）
    // $variant：menu = 触控尺寸（移动端 / Brothers 侧栏）；默认面板尺寸
    // $accordionItemView：本 partial 视图路径（由 Livewire 主视图按主题解析后传入；include 子视图无 $this 绑定，不能自行解析）
    // $ovIdx：「更多」下拉中使用，控制溢出折叠显隐（其他场景不传）
    $hasChild = $record->children->count() > 0;
    $hasActive = $record->has_active;
    $defaultOpen = $defaultOpen ?? $hasActive;
    $depth = max(0, (int) ($record->depth ?? 0) - (int) ($depthOffset ?? 0));    // withDepth 计算，根为 0；Brothers 侧栏传 depthOffset 转相对层级
    $isMenu = ($variant ?? 'panel') === 'menu';
    $accordionItemView = $accordionItemView ?? 'sn-cms::livewire.tradition.components.navigation.partials.accordion-item';
@endphp

<li @class(['sn-cms-acc-item', 'is-active' => $hasActive])
    @if ($hasChild)
        x-data="{ isExpanded: {{ $defaultOpen ? 'true' : 'false' }} }"
    @endif
    @if (isset($ovIdx))
        x-show="hiddenFrom !== null && {{ $ovIdx }} >= hiddenFrom" x-cloak
    @endif
>
    @if ($hasChild)
        {{-- 纯展开模式：整行一体（文字 + 箭头是同一个展开动作），hover 任一处整行联动 --}}
        <a @class([
            'sn-cms-acc-row flex w-full items-center justify-between gap-2 text-sm font-semibold whitespace-nowrap cursor-pointer focus:outline-hidden focus-visible:underline underline-offset-2',
            'px-3.5 py-2.5' => ! $isMenu,
            'px-4 py-3.5' => $isMenu,
        ])
            href="javascript:;" role="menuitem"
            aria-haspopup="true" :aria-expanded="isExpanded ? 'true' : 'false'"
            @click="isExpanded = ! isExpanded"
            @keydown.esc.prevent="isExpanded = false"
            wire:click="$dispatch('sn-cms-navigation-node-click', { recordId: {{ $record->id }}, hasChild: 1 })"
        >
            <span class="flex items-center gap-1 min-w-0">
                @for ($i = 0; $i < $depth; $i++)
                    <div class="w-5 flex-none"></div>
                @endfor
                <span class="truncate">{{ $record->name_label }}</span>
            </span>
            <x-filament::icon :icon="Heroicon::ChevronDown" class="size-5 flex-none transition-transform duration-300" ::class="isExpanded ? 'rotate-180' : ''" aria-hidden="true" />
        </a>

        <div x-cloak x-show="isExpanded" x-collapse>
            <ul class="sn-cms-acc flex flex-col" role="menu">
                @foreach ($record->children as $child)
                    {{-- 不传 defaultOpen：每层各自按激活链决定默认展开（深层激活时整条祖先链展开） --}}
                    @include($accordionItemView, ['record' => $child])
                @endforeach
            </ul>
        </div>
    @else
        <a @class([
            'sn-cms-acc-leaf flex w-full items-center gap-1 text-sm font-normal whitespace-nowrap cursor-pointer focus:outline-hidden focus-visible:underline underline-offset-2',
            'px-3.5 py-2.5' => ! $isMenu,
            'px-4 py-3.5' => $isMenu,
        ])
            role="menuitem"
            {{ \Filament\Support\generate_href_html($record->url_info['url'], $record->url_info['target'] ?? false) }}
            wire:click="$dispatch('sn-cms-navigation-leaf-click', { recordId: {{ $record->id }}, hasChild: 0 })"
        >
            @for ($i = 0; $i < $depth; $i++)
                <div class="w-5 flex-none"></div>
            @endfor
            <span class="truncate">{{ $record->name_label }}</span>
        </a>
    @endif
</li>

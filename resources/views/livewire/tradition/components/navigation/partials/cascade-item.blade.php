@php
    use Filament\Support\Icons\Heroicon;

    // 级联子菜单行（递归）：一级向下弹在主行 li 上，本 partial 的子级一律 depth-n（向右弹，右缘溢出反向）
    // 上下文变量：$record（必传）、$clickable 由父视图 include 上下文继承（hover 级联下父项直达第一个叶子）
    // $cascadeItemView：本 partial 视图路径（由 Livewire 主视图按主题解析后传入；include 子视图无 $this 绑定，不能自行解析）
    // $ovIdx：「更多」下拉中使用，控制溢出折叠显隐（主行内不传）
    $hasChild = $record->children->count() > 0;
    $leafUrl = $hasChild && ($clickable ?? false) ? $record->first_leaf_url : null;
    $cascadeItemView = $cascadeItemView ?? 'sn-cms::livewire.tradition.components.navigation.partials.cascade-item';
@endphp

<li @class(['sn-cms-sub-item relative', 'has-sub' => $hasChild, 'is-active' => $record->has_active])
    @if (isset($ovIdx))
        x-show="hiddenFrom !== null && {{ $ovIdx }} >= hiddenFrom" x-cloak
    @endif
    role="none"
>
    <a class="sn-cms-sub-link flex items-center justify-between gap-2 px-3.5 py-2.5 text-sm font-medium whitespace-nowrap cursor-pointer focus:outline-hidden focus-visible:underline underline-offset-2"
        role="menuitem"
        @if ($hasChild)
            {{-- hover 级联 + parent_clickable + 有可用叶子 → 真实链接直达；否则纯展开 --}}
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
            wire:click="$dispatch('sn-cms-navigation-node-click', { recordId: {{ $record->id }}, hasChild: 1 })"
        @else
            {{ \Filament\Support\generate_href_html($record->url_info['url'], $record->url_info['target'] ?? false) }}
            wire:click="$dispatch('sn-cms-navigation-leaf-click', { recordId: {{ $record->id }}, hasChild: 0 })"
        @endif
    >
        <span class="min-w-0 truncate">{{ $record->name_label }}</span>
        @if ($hasChild)
            <x-filament::icon :icon="Heroicon::ChevronDown" class="sn-cms-chev" aria-hidden="true" />
        @endif
    </a>

    @if ($hasChild)
        <ul class="sn-cms-sub depth-n" role="menu">
            @foreach ($record->children as $child)
                @include($cascadeItemView, ['record' => $child])
            @endforeach
        </ul>
    @endif
</li>

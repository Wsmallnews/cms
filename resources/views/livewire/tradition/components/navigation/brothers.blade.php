@php
    use Wsmallnews\Cms\Support\Utils;

    $nestedset = $this->getNestedset();

    // 与前台导航共用双皮肤：根类挂 sn-cms-nav（minimal 时加换肤类）
    $style = Utils::navigationConfig('style', 'primary');

    // 递归 partial 路径按当前主题解析（与导航主视图一致；include 子视图无 $this 绑定，需传入）
    $accordionItemView = $this->getThemeView('components.navigation.partials.accordion-item');
@endphp

{{-- 同级导航卡片：区块级圆角 + 阴影（页面卡片语言）；行高比下拉面板大，上下留边相应加大；
    内部通栏 hover、层级靠缩进与字重表达（无分割线） --}}
<ul @class([
    'sn-cms-nav sn-cms-brothers sn-rounded w-full flex flex-col py-2 overflow-hidden shadow-(--sn-shadow-card)',
    'sn-cms-nav-minimal' => $style === 'minimal',
])
    role="menu"
>
    @forelse($nestedset as $record)
        {{-- depthOffset=1：侧栏根是一级导航的子级（绝对 depth 从 1 起），转为相对层级让顶层无缩进 --}}
        @include($accordionItemView, ['record' => $record, 'variant' => 'menu', 'depthOffset' => 1])
    @empty
        <li class="w-full px-3 py-2 text-center">
            {{ $this->getEmptyLabel() ?: __('sn-filament-nestedset::nestedset.nestedset.empty_label')}}
        </li>
    @endforelse
</ul>

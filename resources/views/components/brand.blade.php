{{--
    品牌 Logo + 站名（页头/页脚统一渲染逻辑，logo 与站名交叉轴居中对齐）：
    - withName = true（默认）：方形图位（推荐 1:1 图形标）+ 站名
    - withName = false：仅显示 Logo 图（等高自适应宽，适合上传图文一体的横版组合标）
    - 无 Logo：回退为「站名首字标块 + 站名」，任何模式下都保证站点身份
    页头/页脚的差异仅是尺寸（size）与站名颜色（nameClass，如 banner 上用白字）。
--}}
@props([
    'logoUrl' => null,
    'siteName' => '',
    'withName' => true,
    'size' => 'footer',
    'nameClass' => null,
])

@php
    $isHeader = $size === 'header';
    $logoBoxClass = $isHeader ? 'h-14 lg:h-16 w-14 lg:w-16' : 'h-11 w-11';
    $logoSoloClass = $isHeader ? 'h-12 lg:h-14' : 'h-10';
    $nameClass = $nameClass ?? ($isHeader ? 'text-2xl lg:text-3xl font-bold tracking-wide sn-primary-text' : 'sn-h3-text');
@endphp

<div class="flex items-center gap-3">
    @if ($logoUrl)
        @if ($withName)
            {{-- 图形标模式：方形图位 + 站名 --}}
            <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="{{ $logoBoxClass }} rounded-lg object-contain shrink-0">
        @else
            {{-- 组合标模式：仅图，等高自适应宽（横版图文一体长图） --}}
            <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="{{ $logoSoloClass }} w-auto object-contain shrink-0">
        @endif
    @else
        {{-- 无 Logo 回退：首字标块 + 站名（不受开关影响，保证站点身份） --}}
        <span class="{{ $logoBoxClass }} rounded-lg sn-primary-bg flex items-center justify-center text-xl lg:text-2xl font-extrabold text-white shrink-0">{{ mb_substr($siteName, 0, 1) }}</span>
    @endif

    @if ($withName || ! $logoUrl)
        <span class="{{ $nameClass }}">{{ $siteName }}</span>
    @endif
</div>

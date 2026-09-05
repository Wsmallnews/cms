@php
    use function Filament\Support\generate_href_html;

    // 底部导航为空时（未建树），footer 退化为 品牌区 + 合规条
    $hasGroups = $groups->isNotEmpty();
    $hasFlats = $flats->isNotEmpty();
    $hasNavs = $hasGroups || $hasFlats;
@endphp

<footer class="sn-bg sn-contour-only border-t-2 w-full mt-12 pt-10">
    <div class="container mx-auto flex flex-col px-4">

        @if (! $hasNavs)
            {{-- 空树形态：仅品牌区 --}}
            <div class="w-full max-w-md">
                <x-dynamic-component
                    :component="$this->getBladeThemeView('components.footer-brand')"
                    :general="$general"
                />
            </div>
        @elseif ($hasGroups)
            {{-- 分组形态：品牌区 + 分组列（Grid 自适应列数，分组多时自动换行）；无子级的一级导航进下方快捷条 --}}
            <div class="w-full flex flex-col md:flex-row gap-10 md:gap-14">
                <div class="w-full md:w-1/3 lg:w-1/4 shrink-0">
                    <x-dynamic-component
                        :component="$this->getBladeThemeView('components.footer-brand')"
                        :general="$general"
                    />
                </div>

                <nav class="w-full md:w-2/3 lg:w-3/4 grid grid-cols-2 md:grid-cols-[repeat(auto-fit,minmax(11rem,15rem))] gap-x-8 gap-y-10 self-start" aria-label="{{ __('sn-cms::cms.frontend.footer_nav') }}">
                    @foreach ($groups as $group)
                        <div class="flex flex-col gap-3.5 min-w-0">
                            {{-- 一级导航（有子级）作为分组标题，不跳转 --}}
                            <h3 class="sn-content-text font-semibold flex items-center gap-2">
                                {{ $group->name }}
                                <span class="sn-primary-bg w-7 h-1 rounded-full" aria-hidden="true"></span>
                            </h3>
                            <ul class="flex flex-col gap-2.5" role="list">
                                @foreach ($group->children as $child)
                                    <li class="min-w-0">
                                        <a class="sn-descript-text hover:text-primary-600 dark:hover:text-primary-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded-sm block truncate"
                                            {{ generate_href_html($child->url_info['url'], $child->url_info['target'] ?? false) }}>
                                            {{ $child->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </nav>
            </div>

            @if ($hasFlats)
                {{-- 快捷链接条：无子级的一级导航横向平铺 --}}
                <nav class="w-full py-3.5 mt-9 border-t border-gray-300 dark:border-gray-600 flex flex-wrap items-center gap-y-2" aria-label="{{ __('sn-cms::cms.frontend.footer_quick_nav') }}">
                    @foreach ($flats as $flat)
                        <a class="sn-content-text hover:text-primary-600 dark:hover:text-primary-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded-sm px-3 border-r border-gray-200 dark:border-gray-700 {{ $loop->last ? 'border-r-0' : '' }}"
                            {{ generate_href_html($flat->url_info['url'], $flat->url_info['target'] ?? false) }}>
                            {{ $flat->name }}
                        </a>
                    @endforeach
                </nav>
            @endif
        @else
            {{-- 全一级形态：品牌区居左 + 快捷链接居右（自动换行） --}}
            <div class="w-full flex flex-col md:flex-row md:items-start md:justify-between gap-10">
                <div class="w-full md:w-1/3 lg:w-1/4 shrink-0">
                    <x-dynamic-component
                        :component="$this->getBladeThemeView('components.footer-brand')"
                        :general="$general"
                    />
                </div>

                <nav class="w-full md:w-2/3 lg:w-3/4 flex flex-wrap justify-start md:justify-end gap-x-3 gap-y-3 md:pt-1 self-start" aria-label="{{ __('sn-cms::cms.frontend.footer_quick_nav') }}">
                    @foreach ($flats as $flat)
                        <a class="sn-content-text hover:text-primary-600 dark:hover:text-primary-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded-sm px-4 border-r border-gray-200 dark:border-gray-700 {{ $loop->last ? 'border-r-0' : '' }}"
                            {{ generate_href_html($flat->url_info['url'], $flat->url_info['target'] ?? false) }}>
                            {{ $flat->name }}
                        </a>
                    @endforeach
                </nav>
            </div>
        @endif

        {{-- 合规条：版权 + ICP 备案 + 公安备案 --}}
        <div class="w-full py-4 {{ $hasNavs ? 'mt-9' : 'mt-10' }} border-t border-gray-300 dark:border-gray-600 flex flex-wrap items-center justify-center gap-x-5 gap-y-1.5 sn-tip-text text-center">
            @if ($general->copyright || $general->copytime)
                <span>{{ __('sn-cms::cms.frontend.copyright', ['copytime' => $general->copytime, 'copyright' => $general->copyright]) }}</span>
            @endif
            @if ($general->beian_url && $general->beian_no)
                <a href="{{ $general->beian_url }}" target="_blank" rel="noopener" class="hover:text-primary-600 dark:hover:text-primary-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded-sm">
                    {{ $general->beian_no }}
                </a>
            @endif
            @if ($general->beian_police_url && $general->beian_police_no)
                <a href="{{ $general->beian_police_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 hover:text-primary-600 dark:hover:text-primary-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded-sm">
                    <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="11" fill="currentColor" opacity="0.25"/><path d="M12 4l6 2.4v5c0 4-2.6 7.2-6 8.6-3.4-1.4-6-4.6-6-8.6v-5L12 4z" fill="currentColor" opacity="0.6"/><path d="M9 12.2l2 2 4-4.2" stroke="currentColor" stroke-width="1.6" fill="none"/></svg>
                    {{ $general->beian_police_no }}
                </a>
            @endif
        </div>
    </div>
</footer>

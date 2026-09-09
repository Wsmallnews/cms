@php
    use function Filament\Support\generate_href_html;

    // 底部导航为空时（未建树），footer 退化为 品牌区 + 合规条
    $hasGroups = $groups->isNotEmpty();
    $hasFlats = $flats->isNotEmpty();
    $hasNavs = $hasGroups || $hasFlats;
    $showFeed = $feeds->isNotEmpty();
@endphp

<footer class="sn-bg sn-contour-only border-t-2 w-full mt-12">
    <div class="container mx-auto sn-page-x">

        {{-- 主区：品牌区（4/12）+ 导航区（8/12，分组列 + 快捷入口列） --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-x-12 gap-y-10 pt-10 pb-8">
            <div class="lg:col-span-4 w-full min-w-0 flex flex-col">
                @include($this->getThemeView('components.footer-brand'), ['general' => $general])
            </div>

            @if ($hasNavs)
                <nav class="lg:col-span-8 w-full min-w-0 grid grid-cols-2 sm:grid-cols-3 gap-x-8 gap-y-8 self-start" aria-label="{{ __('sn-cms::cms.frontend.footer_nav') }}">
                    @foreach ($groups as $group)
                        {{-- 一级导航（有子级）作为分组标题，不跳转 --}}
                        <div class="flex flex-col gap-3 min-w-0">
                            <h3 class="sn-content-text text-sm font-semibold flex items-center gap-2">
                                {{ $group->name }}
                                <span class="sn-primary-bg w-7 h-1 rounded-full" aria-hidden="true"></span>
                            </h3>
                            <ul class="flex flex-col gap-2 text-sm" role="list">
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

                    @if ($hasFlats || $showFeed)
                        {{-- 快捷入口列：无子级的一级导航 + RSS 订阅 --}}
                        <nav class="flex flex-col gap-3 min-w-0 col-span-2 sm:col-span-1" aria-label="{{ __('sn-cms::cms.frontend.footer_quick_nav') }}">
                            <h3 class="sn-content-text text-sm font-semibold flex items-center gap-2">
                                {{ __('sn-cms::cms.frontend.quick_entry') }}
                                <span class="sn-primary-bg w-7 h-1 rounded-full" aria-hidden="true"></span>
                            </h3>
                            <ul class="flex flex-col gap-2 text-sm" role="list">
                                @foreach ($flats as $flat)
                                    <li class="min-w-0">
                                        <a class="sn-descript-text hover:text-primary-600 dark:hover:text-primary-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded-sm block truncate"
                                            {{ generate_href_html($flat->url_info['url'], $flat->url_info['target'] ?? false) }}>
                                            {{ $flat->name }}
                                        </a>
                                    </li>
                                @endforeach
                                @if ($showFeed)
                                    @include($this->getThemeView('components.footer-rss'), ['feeds' => $feeds])
                                @endif
                            </ul>
                        </nav>
                    @endif
                </nav>
            @endif
        </div>

        {{-- 友链条：启用状态的友链紧凑平铺（合规条上方） --}}
        @if ($links->isNotEmpty())
            <nav class="border-t border-gray-200 dark:border-gray-800 pt-5 pb-4 flex flex-wrap items-center gap-x-5 gap-y-2" aria-label="{{ __('sn-cms::cms.frontend.friend_links') }}">
                <span class="sn-tip-text font-semibold tracking-wide">{{ __('sn-cms::cms.frontend.friend_links') }}</span>
                @foreach ($links as $link)
                    <a rel="noopener{{ $link->nofollow ? ' nofollow' : '' }}" title="{{ $link->description }}"
                        class="sn-tip-text hover:text-primary-600 dark:hover:text-primary-400 hover:underline underline-offset-4 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded-sm transition-colors"
                        {{ generate_href_html($link->url, true) }}>
                        {{ $link->name }}
                    </a>
                @endforeach
            </nav>
        @endif
    </div>

    {{-- 底部合规条：版权 + ICP 备案 + 公安备案，居中一体式窄条（浅灰底） --}}
    <div class="border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-950/50">
        <div class="container mx-auto sn-page-x py-3 flex flex-wrap items-center justify-center gap-x-5 gap-y-1.5 sn-tip-text text-center">
            @if ($general->copyright || $general->copytime)
                <span>{{ __('sn-cms::cms.frontend.copyright', ['copytime' => $general->copytime, 'copyright' => $general->copyright]) }}</span>
            @endif
            @if ($general->beian_url && $general->beian_no)
                <a {{ generate_href_html($general->beian_url, true) }} rel="noopener"
                    class="hover:text-primary-600 dark:hover:text-primary-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded-sm transition-colors">
                    {{ $general->beian_no }}
                </a>
            @endif
            @if ($general->beian_police_url && $general->beian_police_no)
                <a {{ generate_href_html($general->beian_police_url, true) }} rel="noopener"
                    class="inline-flex items-center gap-1.5 hover:text-primary-600 dark:hover:text-primary-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded-sm transition-colors">
                    <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="11" fill="currentColor" opacity="0.25"/><path d="M12 4l6 2.4v5c0 4-2.6 7.2-6 8.6-3.4-1.4-6-4.6-6-8.6v-5L12 4z" fill="currentColor" opacity="0.6"/><path d="M9 12.2l2 2 4-4.2" stroke="currentColor" stroke-width="1.6" fill="none"/></svg>
                    {{ $general->beian_police_no }}
                </a>
            @endif
        </div>
    </div>
</footer>

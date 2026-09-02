@php
    use Filament\Support\Icons\Heroicon;
    use Wsmallnews\Cms\Support\Utils;

    $flagEnum = Utils::getFlagEnum();

    $view = $this->getThemeView('components.category.categories');
    $recordView = $this->getBladeThemeView('components.category.category-record');
@endphp

<div class="w-full flex flex-col lg:flex-row gap-4 relative">
    <x-sn-support::loading.overlay />

    @if ($categoryStyle == 'tree')
        <div class="w-full lg:w-72 shrink-0">
            {{-- 分类树区块：亮色白底 / 暗色深底 --}}
            <div class="sn-container p-2">
                <livewire:sn-category::components.categories
                    :scope-type="$scopeType"
                    :use-url="false"
                    :active-category-id="$categoryId"
                    :view="$view"
                    :record-view="$recordView"
                    :key="'sn-category::components.categories-' . $this->getFingerprint()" />
            </div>
        </div>
    @endif

    <div class="w-full flex flex-col gap-4 grow min-w-0">
        @if ($categoryStyle == 'select' && $categories->isNotEmpty())
            <div class="flex flex-wrap gap-4">
                @foreach ($categories as $category)
                    <span class="sn-badge sn-badge-lg sn-badge-solid-primary">{{ $category->name_label }}</span>
                @endforeach
            </div>
        @endif

        {{-- 右侧整体一个区块：头部（标签左 + 搜索右）+ 分割线 + 文章列表 --}}
        <div class="sn-container rounded-md overflow-hidden">

            <div class="flex flex-wrap items-center justify-between gap-x-4 gap-y-3 px-4 py-3 border-b border-gray-200 dark:border-gray-800">
                {{-- 剥掉 fi-tabs 自带的容器外观（白底/边框/阴影/内边距/居中 margin），只保留 tab 悬停与激活态 --}}
                <x-filament::tabs label="flags" class="min-w-0 mx-0! bg-transparent! shadow-none! ring-0! rounded-none! p-0!">
                    <x-filament::tabs.item
                        wire:click="$set('flag', '')"
                        :active="blank($flag)"
                        class="sn-tabs-item-vivid"
                        style="{{ sn_badge_color('primary', 'solid', asVariables: true)['style'] }}"
                    >
                        {{ __('sn-cms::cms.frontend.all') }}
                    </x-filament::tabs.item>
                    @foreach ($flagEnum::cases() as $flagCase)
                        @php
                            // 选中态：flag 色实底 + 对比文字（醒目，同 ToggleButtons 选中效果）
                            $tabStyle = sn_badge_color($flagCase->getColor(), 'solid', asVariables: true)['style'];
                        @endphp
                        <x-filament::tabs.item
                            wire:click="$set('flag', '{{ $flagCase->value }}')"
                            :active="$flagCase->value == $flag"
                            :icon="$flagCase->getIcon()"
                            class="sn-tabs-item-vivid"
                            style="{{ $tabStyle }}"
                        >
                            {{ $flagCase->getLabel() }}
                        </x-filament::tabs.item>
                    @endforeach
                </x-filament::tabs>

                <x-filament::input.wrapper
                    class="w-full sm:w-72"
                    inline-prefix
                    :prefix-icon="Heroicon::MagnifyingGlass"
                >
                    <label for="post-search" class="sr-only">{{ __('sn-cms::cms.frontend.search') }}</label>
                    <x-filament::input
                        id="post-search"
                        type="search"
                        placeholder="{{ __('sn-cms::cms.frontend.search_placeholder') }}"
                        wire:model.live.debounce.250ms="search"
                    />
                </x-filament::input.wrapper>
            </div>

            {{-- 文章列表：无独立卡片，行间分割线 --}}
            <x-sn-support::paginators.container :page-type="$pageType" :page-info="$pageInfo" :paginator-link="$paginatorLink" :page-name="$pageName">
                <div class="w-full flex flex-col divide-y divide-gray-100 dark:divide-gray-800/70">
                    @forelse ($posts as $post)
                        {{-- 行高写死；图片区 4:3 横版满行高，cover 居中裁剪；sm 起行高加大，容纳 flag 徽章行 --}}
                        <x-sn-cms::container.block-link
                            class="group flex flex-row gap-4 h-36 sm:h-40 p-4 overflow-hidden transition-colors duration-200 motion-reduce:transition-none hover:bg-primary-50/50 dark:hover:bg-primary-900/15 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-primary-500"
                            href="{{ \Wsmallnews\Cms\Support\Utils::route('posts.show', $post) }}"
                        >
                            <div class="h-full sn-aspect-landscape max-w-[45%] shrink-0 rounded-md overflow-hidden bg-gray-100 dark:bg-gray-800">
                                @if ($post->getFirstMediaUrl('post_image'))
                                    <img class="sn-motion-scale w-full h-full object-cover" src="{{ $post->getFirstMediaUrl('post_image') }}" alt="{{ $post->title }}" loading="lazy" />
                                @else
                                    <div class="sn-image-placeholder sn-motion-scale">
                                        <x-filament::icon :icon="Heroicon::OutlinedPhoto" class="w-10 h-10" aria-hidden="true" />
                                    </div>
                                @endif
                            </div>

                            <div class="flex flex-col grow min-w-0 gap-1 sm:gap-1.5">
                                {{-- 顶行：分类标签（左）+ flag 标识（右）--}}
                                @if ($post->categories->isNotEmpty() || filled($post->flags))
                                    <div class="flex items-start justify-between gap-2">
                                        @if ($post->categories->isNotEmpty())
                                            <div class="flex flex-wrap gap-1.5 min-w-0">
                                                @foreach ($post->categories as $category)
                                                    <span class="sn-badge sn-badge-primary">{{ $category->name }}</span>
                                                @endforeach
                                            </div>
                                        @endif

                                        @if (filled($post->flags))
                                            {{-- 最多展示 2 个 flag，避免窄屏换行撑高行 --}}
                                            <div class="flex flex-wrap justify-end gap-1.5 shrink-0 ml-auto">
                                                @foreach (array_slice($post->flags ?? [], 0, 2) as $flagType)
                                                    @php
                                                        $flagCase = $flagEnum::tryFrom($flagType);
                                                    @endphp
                                                    @continue(blank($flagCase))
                                                    @php
                                                        $badge = sn_badge_color($flagCase->getColor());
                                                    @endphp
                                                    <span class="sn-badge {{ $badge['class'] }}" style="{{ $badge['style'] }}">
                                                        @if (filled($flagCase->getIcon()))
                                                            <x-filament::icon :icon="$flagCase->getIcon()" class="w-3.5 h-3.5" aria-hidden="true" />
                                                        @endif
                                                        {{ $flagCase->getLabel() }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <div class="sn-h3-text sn-hover line-clamp-1 transition duration-300">
                                    {{ $post->title }}
                                </div>

                                <div class="sn-descript-text line-clamp-2">
                                    {{ $post->description }}
                                </div>

                                {{-- 底行：日期，贴底对齐 --}}
                                <div class="sn-tip-text mt-auto">
                                    {{ $post->updated_at->format('Y-m-d') }}
                                </div>
                            </div>
                        </x-sn-cms::container.block-link>
                    @empty
                        <div class="py-16 text-center sn-descript-text">{{ __('sn-cms::cms.frontend.no_posts') }}</div>
                    @endforelse
                </div>
            </x-sn-support::paginators.container>
        </div>
    </div>
</div>

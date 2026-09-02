{{-- Post 搜索结果条目（经来源注册的 view 选项使用，结构同 cms/posts 页面的文章条目）
    数据契约：$result（SearchResult，含 ->record 原始 Post 模型）、$query 关键词（高亮用 text_highlight() 助手）；
    外层的链接包裹由 support 统一处理（外层 <a> 行带 sn-link hover 联动），本视图只负责条目内容区。
    同时用于头部搜索下拉浮层与搜索结果页：图片比 posts 列表条目小一档，兼顾浮层紧凑度。 --}}

@php
    use Filament\Support\Icons\Heroicon;
    use Wsmallnews\Cms\Support\Utils;

    $post = $result->record;
    $flagEnum = Utils::getFlagEnum();
@endphp

<div class="flex flex-row items-center gap-3 sm:gap-4 grow min-w-0 text-left">
    <div class="h-24 sm:h-28 sn-aspect-landscape shrink-0 rounded-md overflow-hidden bg-gray-100 dark:bg-gray-800">
        @if ($post && $post->getFirstMediaUrl('post_image'))
            <img
                class="sn-motion-scale w-full h-full object-cover"
                src="{{ $post->getFirstMediaUrl('post_image') }}"
                alt="{{ $result->title }}"
                loading="lazy"
            />
        @else
            <div class="sn-image-placeholder sn-motion-scale">
                <x-filament::icon :icon="Heroicon::OutlinedPhoto" class="w-8 h-8" aria-hidden="true" />
            </div>
        @endif
    </div>

    <div class="flex flex-col grow min-w-0 gap-1">
        {{-- 顶行：分类标签（左）+ flag 标识（右）--}}
        @if ($post && ($post->categories->isNotEmpty() || filled($post->flags)))
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
            {!! text_highlight($result->title, $query ?? '') !!}
        </div>

        @if ($result->description)
            <div class="sn-descript-text line-clamp-2">{!! text_highlight($result->description, $query ?? '') !!}</div>
        @endif

        {{-- 底行：日期，贴底对齐（同 posts 列表条目） --}}
        @if ($post?->updated_at)
            <div class="sn-tip-text mt-auto">{{ $post->updated_at->format('Y-m-d') }}</div>
        @endif
    </div>
</div>

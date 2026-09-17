@php
    use Filament\Support\Icons\Heroicon;
@endphp

{{-- 相关文章推荐：块头（标题/描述）由编排 block 包装层输出，组件只渲染列表。
    行式列表：行的 sn-padded 是行自身的边距语义（嵌套时保留），容器只是卡片皮 + 裁分割线圆角 --}}
<div @class([
    'w-full',
    'sn-container overflow-hidden' => $contained,
])>
    @if ($posts->isNotEmpty())
        <div class="w-full flex flex-col divide-y divide-gray-100 dark:divide-gray-800/70">
            @foreach ($posts as $post)
                <a
                    {{ \Filament\Support\generate_href_html(\Wsmallnews\Cms\Support\Utils::route('posts.show', $post)) }}
                    class="sn-link group flex flex-row items-center gap-3 sn-padded"
                >
                    <div class="h-16 shrink-0 sn-aspect-landscape rounded-md overflow-hidden bg-gray-100 dark:bg-gray-800">
                        @if ($post->getFirstMediaUrl('post_image'))
                            <img class="sn-motion-scale w-full h-full object-cover" src="{{ $post->getFirstMediaUrl('post_image') }}" alt="{{ $post->title }}" loading="lazy" />
                        @else
                            <div class="sn-image-placeholder">
                                <x-filament::icon :icon="Heroicon::OutlinedPhoto" class="w-7 h-7" aria-hidden="true" />
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-col grow min-w-0 gap-1">
                        <div class="sn-h4-text sn-hover line-clamp-2">
                            {{ $post->title }}
                        </div>
                        <div class="sn-tip-text">
                            {{ optional($post->updated_at)->format('Y-m-d') }}
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        {{-- 空态：有来源（暂无相关）与无来源共用版式，文案区分 --}}
        <x-sn-support::empty
            :icon="Heroicon::OutlinedNewspaper"
            icon-color="gray"
            icon-size="md"
            :description="$hasSource ? __('sn-cms::cms.frontend.no_related_posts') : __('sn-cms::cms.frontend.related_posts_no_source')"
            :contained="false"
        />
    @endif
</div>

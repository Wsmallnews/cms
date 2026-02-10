@php
    $maxListNum = 4;
    $count = $posts->count();

    // 如果不足四条，则轮播图和 列表显示的是一样的 post 内容
    $slideNum = $count > $maxListNum ? $count - $maxListNum : $count;
    $listStartIndex = $count > $maxListNum ? $slideNum : 0;

    // post 前 $slideNum 条
    $slides = $posts->take($slideNum)->map(function ($post) {
        return [
            'image' => $post->getFirstMediaUrl('post_image'),
            'label' => $post->title,
            'url' => \Wsmallnews\Cms\Support\Utils::route('posts.show', $post->id),
        ];
    })->toArray();
@endphp

<div class="w-full flex flex-col lg:flex-row gap-2 lg:gap-4">
    <x-sn-support::swiper @class([
        'sn-container rounded-md' => $contained,
        'w-full aspect-video overflow-hidden'
    ]) :slides="$slides" :has-thumb="false" />

    <div @class([
        'sn-container rounded-md' => $contained,
        'w-full flex flex-col lg:grid lg:grid-rows-4 lg:aspect-video overflow-hidden'
    ])>
        @foreach($posts as $post)
            @if ($loop->index >= $listStartIndex)
                <x-sn-cms::container.block-link
                    href="{{ \Wsmallnews\Cms\Support\Utils::route('posts.show', $post->id) }}" 
                    class="flex w-full h-28 lg:h-auto min-h-0 gap-2 group hover:bg-primary-100/30 transition-colors duration-300 {{ $loop->index !== $posts->count() - 1 ? 'border-b border-slate-100' : '' }}"
                >
                    @if ($post->getFirstMediaUrl('post_image'))
                        <div class="h-full aspect-4/3 shrink-0 rounded-md">
                            <img class="w-full h-full object-cover transition duration-300 group-hover:scale-110" src="{{ $post->getFirstMediaUrl('post_image') }}" />
                        </div>
                    @endif

                    <div class="min-w-0 flex flex-col grow py-2 px-4 gap-1">
                        <div class="text-base text-slate-900 font-bold line-clamp-1 transition duration-300 group-hover:text-primary-500">
                            {{ $post->title }}
                        </div>

                        <div class="lg:h-0 xl:h-auto text-sm grow text-slate-500 leading-5 line-clamp-2 xl:line-clamp-1 2xl:line-clamp-2">
                            {{ $post->description }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ optional($post->updated_at)->format('Y-m-d') }}
                        </div>
                    </div>
                </x-sn-cms::container.block-link>
            @endif
        @endforeach
    </div>
</div>
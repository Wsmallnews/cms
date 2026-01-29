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

<x-dynamic-component :component="$this->getBlockContainerWrapperView()" class="w-full flex flex-col lg:flex-row gap-2 lg:gap-4">
    <x-sn-support::swiper class="w-full aspect-[16/9]" :slides="$slides" :has-thumb="false" />

    <div class="w-full h-112 lg:h-auto lg:aspect-[16/9] flex flex-col divide-dashed divide-y overflow-hidden gap-2">
        @foreach($posts as $post)
            @if ($loop->index >= $listStartIndex)
                <x-sn-cms::base.empty 
                    tag="a" 
                    href="{{ \Wsmallnews\Cms\Support\Utils::route('posts.show', $post->id) }}" 
                    class="flex-1 flex w-full min-h-0 gap-2 hover:bg-primary-100"
                >
                    @if ($post->getFirstMediaUrl('post_image'))
                        <div class="h-full flex-shrink-0 rounded-md p-1">
                            <img class="w-full h-full object-cover transition duration-300 group-hover:scale-105" src="{{ $post->getFirstMediaUrl('post_image') }}" />
                        </div>
                    @endif

                    <div class="flex-1 min-w-0 flex flex-col grow p-2 gap-1">
                        <div class="text-base line-clamp-1 transition duration-300 group-hover:text-primary-500">
                            {{ $post->title }}
                        </div>

                        <div class="lg:h-0 xl:h-auto text-sm grow text-gray-500 leading-5 line-clamp-2 xl:line-clamp-1 2xl:line-clamp-2">
                            {{ $post->description }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ optional($post->updated_at)->format('Y-m-d') }}
                        </div>
                    </div>
                </x-sn-cms::base.empty>
            @endif
        @endforeach
    </div>
</x-dynamic-component>
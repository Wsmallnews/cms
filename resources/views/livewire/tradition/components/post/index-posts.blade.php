@php
    use Filament\Support\Icons\Heroicon;
    
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
            'url' => \Wsmallnews\Cms\Support\Utils::route('posts.show', $post),
        ];
    })->toArray();
@endphp

<div class="w-full flex flex-col lg:flex-row gap-2 lg:gap-4">
    <x-sn-support::swiper @class([
        'sn-container sn-hover rounded-md' => $contained,
        'w-full aspect-video overflow-hidden'
    ]) :slides="$slides" :has-thumb="false" />

    <div @class([
        'sn-container sn-hover rounded-md' => $contained,
        'w-full flex flex-col lg:grid lg:grid-rows-4 lg:aspect-video overflow-hidden sn-divide-y'
    ])>
        @foreach($posts as $post)
            @if ($loop->index >= $listStartIndex)
                <x-sn-cms::container.block-link
                    href="{{ \Wsmallnews\Cms\Support\Utils::route('posts.show', $post) }}" 
                    class="sn-link flex w-full h-28 lg:h-auto min-h-0 gap-2 group"
                >
                    @if ($post->getFirstMediaUrl('post_image'))
                        <div class="h-full aspect-4/3 shrink-0 rounded-md">
                            <img class="w-full h-full object-cover transition duration-300 group-hover:scale-110" src="{{ $post->getFirstMediaUrl('post_image') }}" />
                        </div>
                    @else
                        <div class="sn-image-placeholder">
                            <x-filament::icon :icon="Heroicon::OutlinedPhoto" class="w-10 h-10" aria-hidden="true" />
                        </div>
                    @endif

                    <div class="min-w-0 flex flex-col grow py-2 px-4 gap-1">
                        <div class="sn-h4-text sn-hover line-clamp-1">
                            {{ $post->title }}
                        </div>

                        <div class="sn-descript-text lg:h-0 xl:h-auto grow line-clamp-2 xl:line-clamp-1 2xl:line-clamp-2">
                            {{ $post->description }}
                        </div>
                        <div class="sn-tip-text">
                            {{ optional($post->updated_at)->format('Y-m-d') }}
                        </div>
                    </div>
                </x-sn-cms::container.block-link>
            @endif
        @endforeach
    </div>
</div>
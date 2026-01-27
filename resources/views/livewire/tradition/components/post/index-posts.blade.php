@php
    $slides = $posts->take($limit / 2)->map(function ($post) {
        return [
            'image' => $post->getFirstMediaUrl('post_image'),
            'label' => $post->title,
            'url' => \Wsmallnews\Cms\Support\Utils::route('posts.show', $post->id),
        ];
    })->toArray();
@endphp

<x-dynamic-component :component="$this->getBlockContainerWrapperView()" class="w-full flex flex-col md:flex-row gap-4">
    <x-sn-support::swiper class="w-full aspect-[16/9]" :slides="$slides" :has-thumb="false" />

    <div class="w-full">
        @foreach($posts as $post)
            @if ($loop->index >= ($limit / 2))
                <x-sn-cms::base.empty tag="a" href="{{ \Wsmallnews\Cms\Support\Utils::route('posts.show', $post->id) }}" class="flex items-center">
                    <div class="w-[176px] h-[100px]">
                        <img src="{{ $post->getFirstMediaUrl('main', 'thumb') }}" class="w-full h-full object-cover" />
                    </div>
                    <div class="h-[100px] bg-[#F3E8DB] box-border pl-[16px] pr-[20px] pt-[10px]">
                        <div class="text-[#3C2F21] text-[18px] leading-[18px] line-clamp-1 text-left">
                            {{ $post->title }}
                        </div>
                        <div class="mt-[14px] text-[#666666] text-[16px] leading-[16px] line-clamp-1 text-left">
                            {{ $post->description }}
                        </div>
                        <div class="mt-[14px] text-[#999999] text-[14px] leading-[14px] text-left">
                            {{ optional($post->updated_at)->format('Y-m-d') }}
                        </div>
                    </div>
                </x-sn-cms::base.empty>
            @endif
        @endforeach
    </div>
</x-dynamic-component>
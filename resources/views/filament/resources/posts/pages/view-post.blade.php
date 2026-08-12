@php
    $post = $this->getRecord();
    $images = [];
    foreach ($post->getMedia('post_images') as $media) {
        $images[] = $media->getUrl();
    }
@endphp

<x-filament-panels::page>
    <div @class([
        'sn-container px-4 py-8',
        'w-full flex flex-col gap-4'
    ])>
        <div class="sn-h2-text">
            {{ $post->title }}
        </div>

        <div class="sn-tip-text flex gap-8">
            <div>{{ __('sn-cms::cms.frontend.published_at') }}：{{ $post->published_at?->format('Y-m-d H:i:s') }}</div>
            <div>{{ __('sn-cms::cms.frontend.views') }}：{{ $post->counter['view_num'] }}</div>
        </div>

        <div class="sn-descript-text sn-gray-bg p-2 rounded-md">
            {{ $post->description }}
        </div>

        @if ($images)
            <x-sn-support::swiper class="w-full aspect-video" :slides="$images" />
        @endif

        @if ($post->content)
            <x-sn-support::content
                :content-type="$post->content->content_type"
                :content="$post->content->content"
            />
        @endif
    </div>
</x-filament-panels::page>
@php
    $images = [];
    foreach ($post->getMedia('post_images') as $media) {
        $images[] = $media->getUrl();
    }
@endphp

<div @class([
    'sn-container px-4 py-8' => $contained,
    'w-full flex flex-col gap-4'
])>
    <div class="sn-h2-text">
        {{ $post->title }}
    </div>

    <div class="sn-tip-text">
        {{ $post->created_at->format('Y-m-d') }}
    </div>

    <div class="sn-descript-text sn-gray-bg p-2 rounded-md">
        {{ $post->description }}
    </div>

    @if ($images)
        <x-sn-support::swiper class="w-full aspect-video" :slides="$images" />
    @endif

    <div class="sn-content-text">
        {!! $post->content?->content !!}
    </div>
</div>
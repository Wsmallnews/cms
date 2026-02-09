@php
    $images = [];
    foreach ($post->getMedia('post_images') as $media) {
        $images[] = $media->getUrl();
    }
@endphp

<div class="sn-block w-full flex flex-col gap-4 px-4 py-8">
    <div class="text-3xl font-bold">
        {{ $post->title }}
    </div>

    <div class="text-sm text-gray-500">
        {{ $post->created_at->format('Y-m-d') }}
    </div>

    <div class="text-gray-500 bg-gray-100 p-2 rounded-md">
        {{ $post->description }}
    </div>

    @if ($images)
        <x-sn-support::swiper class="w-full aspect-[16/9]" :slides="$images" />
    @endif

    <div class="">
        {!! $post->content?->content !!}
    </div>
</div>
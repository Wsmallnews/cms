@php
    $images = [];
    foreach ($post->getMedia('post_images') as $media) {
        $images[] = $media->getUrl();
    }
@endphp

<x-dynamic-component :component="$this->getBlockContainerWrapperView()" class="w-full">
    <div class="w-full flex flex-col gap-4">
        <div class="text-3xl font-bold">
            {{ $post->title }}
        </div>

        <div class="text-sm text-gray-500">
            {{ $post->created_at->format('Y-m-d') }}
        </div>

        <div class="text-gray-500 bg-gray-100 p-2 rounded-md">
            {{ $post->description }}
        </div>

        <x-sn-support::swiper class="w-full" :images="$images" />

        <div class="">
            {!! $post->content?->content !!}
        </div>
    </div>
</x-dynamic-component>
<x-dynamic-component :component="$wrapperView" class="w-full">
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

        <div class="">
            {!! $post->content?->content !!}
        </div>
    </div>
</x-dynamic-component>
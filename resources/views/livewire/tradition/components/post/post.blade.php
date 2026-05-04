@php
    $images = [];
    foreach ($post->getMedia('post_images') as $media) {
        $images[] = $media->getUrl();
    }
@endphp

<div class="w-full flex flex-col gap-4">
    <div @class([
        'sn-container px-4 py-8' => $contained,
        'w-full flex flex-col gap-4'
    ])>
        <div class="sn-h2-text">
            {{ $post->title }}
        </div>

        <div class="sn-tip-text flex gap-8">
            <div>{{ __('发布时间') }}：{{ $post->published_at?->format('Y-m-d H:i:s') }}</div>
            <div>{{ __('浏览量') }}：{{ $post->counter['view_num'] }}</div>
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

    @if ($canComment)
        <div @class([
            'sn-container px-4 py-8' => $contained,
            'w-full flex flex-col gap-4'
        ])>
            <livewire:sn-comment-components-comments
                :scope-type="$scopeType"
                :scope-id="$scopeId"
                :contained="false"
                :commentable="$post"
                :editor-type="\Wsmallnews\Support\Enums\EditorType::Textarea"
                :user="$user"
                page-name="cp"
            />
        </div>
    @endif
</div>
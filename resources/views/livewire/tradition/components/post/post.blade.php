@php
    $images = [];
    foreach ($post->getMedia('post_images') as $media) {
        $images[] = $media->getUrl();
    }
@endphp

<div class="w-full flex flex-col gap-4">
    <div @class([
        'sn-container sn-padded' => $contained,
        'w-full flex flex-col gap-4'
    ])>
        <div class="sn-h2-text">
            {{ $post->title }}
        </div>

        <div class="sn-tip-text flex gap-8">
            <div>{{ __('sn-cms::cms.frontend.published_at') }}：{{ $post->published_at?->format('Y-m-d H:i:s') }}</div>
            <div>{{ __('sn-cms::cms.frontend.views') }}：{{ $post->counter['view_num'] }}</div>
        </div>

        @if ($post->description)
            <div class="sn-descript-text sn-gray-bg p-2 rounded-md">
                {{ $post->description }}
            </div>
        @endif

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

    @if ($hasComment)
        <div @class([
            'sn-container sn-padded' => $contained,
            'w-full flex flex-col gap-4'
        ])>
            <livewire:sn-comment::components.comments
                :scope-type="$scopeType"
                :scope-id="$scopeId"
                :contained="false"
                :commentable="$post"
                :content-type="$contentType"
                :can-add-comment="$canAddComment"
                :comment-status="$commentStatus"
                :auth-user="$authUser"
                page-name="cp"
            />
        </div>
    @endif
</div>
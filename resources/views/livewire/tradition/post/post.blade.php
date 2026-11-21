@php

    $hasComment = $this->commentOption('post', 'enable', false);
    $canAddComment = $this->commentOption('post', 'can_add_comment', false);
    $contentType = $this->commentOption('post', 'content_type', false);
    $commentStatus = $this->commentOption('post', 'comment_status', false);
@endphp

<x-dynamic-component :component="$this->getPageContainer()">
    <div class="sn-content">
        @if($breadcrumbs)
            <div class="sn-descript-text w-full flex items-center gap-2 text-left">
                {{ __('sn-cms::cms.frontend.current_location') }} :
                <x-sn-support::breadcrumbs :breadcrumbs="$breadcrumbs" />
            </div>
        @endif

        @if ($sidebar)
            {{-- purpose 侧栏（post-sidebar 编排命中）：分栏渲染，position 决定侧栏 DOM 位置 --}}
            <div class="sn-split">
                @if ($sidebar['position'] === 'left')
                    {{-- 左侧栏：DOM 在前 = 窄屏堆叠时在上 --}}
                    <aside class="w-full min-w-0">
                        <x-sn-support::composition.rows :rows="$sidebar['rows']" />
                    </aside>
                @endif

                <div class="sn-split-main">
                    <livewire:sn-cms::components.post.post
                        :slug="$slug"
                        :has-comment="$hasComment"
                        :can-add-comment="$canAddComment"
                        :content-type="$contentType"
                        :comment-status="$commentStatus"
                    />
                </div>

                @if ($sidebar['position'] !== 'left')
                    {{-- 右侧栏：内容在前、侧栏在后（grid 按源顺序放置 = 内容左、侧栏右） --}}
                    <aside class="w-full min-w-0">
                        <x-sn-support::composition.rows :rows="$sidebar['rows']" />
                    </aside>
                @endif
            </div>
        @else
            {{-- 无 post-sidebar 编排：全宽回退（与既有布局一致） --}}
            <livewire:sn-cms::components.post.post
                :slug="$slug"
                :has-comment="$hasComment"
                :can-add-comment="$canAddComment"
                :content-type="$contentType"
                :comment-status="$commentStatus"
            />
        @endif
    </div>
</x-dynamic-component>

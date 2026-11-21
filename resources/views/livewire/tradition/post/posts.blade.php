<x-dynamic-component :component="$this->getPageContainer()">
    <div class="sn-content">
        @if($breadcrumbs)
            <div class="sn-descript-text w-full flex items-center gap-2 text-left">
                {{ __('sn-cms::cms.frontend.current_location') }} :
                <x-sn-support::breadcrumbs :breadcrumbs="$breadcrumbs" />
            </div>
        @endif

        <livewire:sn-cms::components.post.posts
            :category-ids="$categoryId"
            category-style="tree"
            :custom-routes="['detail' => 'posts.show']" />
    </div>
</x-dynamic-component>
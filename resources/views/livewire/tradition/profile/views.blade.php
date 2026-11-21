@php
    $user = $this->getAuthUser();
@endphp

<x-dynamic-component :component="$this->getPageContainer()">
    <div class="sn-content">
        @if($breadcrumbs)
            <div class="sn-descript-text w-full flex items-center gap-2 text-left">
                {{ __('sn-cms::cms.frontend.current_location') }} :
                <x-sn-support::breadcrumbs :breadcrumbs="$breadcrumbs" />
            </div>
        @endif

        {{-- 侧栏与内容区按比例分栏（lg 1:3，xl 起 1:4）；lg 以下上下堆叠（侧栏在上） --}}
        <div class="sn-split">
            <div class="w-full min-w-0">
                <livewire:sn-user::components.user.sidebar-menu />
            </div>
            <div class="sn-container sn-split-main">
                <livewire:sn-preference::components.views
                    :preferencer="$user"
                    :manageable="true"
                    :contained="false"
                    :href-route="$this->site()?->routeName('posts.show')" />
            </div>
        </div>
    </div>
</x-dynamic-component>

<x-dynamic-component :component="$this->getPageContainer()">
    <div class="sn-content">
        <h1 class="sn-content-text text-xl font-semibold">{{ __('sn-cms::cms.frontend.search_results') }}</h1>

        <livewire:sn-support::components.search-results :limit="10" placeholder="{{ __('sn-cms::cms.frontend.search_placeholder') }}" />
    </div>
</x-dynamic-component>

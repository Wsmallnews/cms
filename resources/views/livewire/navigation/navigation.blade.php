@php
    // $breadcrumbs = [];

    // foreach ($parents as $parent) {
    //     $breadcrumbs[$parent->url_info['url']] = $parent->name;
    // }

    $scopeType = $this->getScopeType();
    $scopeId = $this->getScopeId();
@endphp

@push('seo')
    {{-- {!! seo()->for($navigation) !!} --}}
@endpush

<div class="w-full flex flex-col grow gap-4">
    <livewire:sn-components-navigation :scope-type="$scopeType" :scope-id="$scopeId" />

    <div class="container mx-auto flex flex-col grow gap-4">
        <livewire:sn-components-navigation-container :scope-type="$scopeType" :scope-id="$scopeId" :slug="$slug" />
    </div>

    <livewire:sn-components-footer :scope-type="$scopeType" :scope-id="$scopeId" />
</div>

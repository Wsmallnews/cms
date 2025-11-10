@push('seo')
    {{-- {!! seo()->for($navigation) !!} --}}
@endpush

<div class="w-full flex flex-col grow gap-4">
    <livewire:sn-components-navigation scope-type="{{ $this->getScopeType() }}" scope-id="{{ $this->getScopeId() }}" />

    <div class="container mx-auto flex flex-col grow gap-4">
        <livewire:sn-components-posts scope-type="{{ $this->getScopeType() }}" scope-id="{{ $this->getScopeId() }}" :category_ids="$category_id" />
    </div>

    <livewire:sn-components-footer scope-type="{{ $this->getScopeType() }}" scope-id="{{ $this->getScopeId() }}" />
</div>
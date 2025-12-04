<div class="w-full flex flex-col grow">
    <livewire:sn-cms-components-navigation scope-type="{{ $this->getScopeType() }}" scope-id="{{ $this->getScopeId() }}" />

    <div class="w-full flex flex-col grow gap-4">
        {{-- <livewire:sn-cms-components-index-posts :limit="6" /> --}}
    </div>

    <livewire:sn-cms-components-footer scope-type="{{ $this->getScopeType() }}" scope-id="{{ $this->getScopeId() }}" />
</div>
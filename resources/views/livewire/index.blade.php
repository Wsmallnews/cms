<div class="w-full flex flex-col grow">
    <livewire:sn-components-navigation scope-type="{{ $this->getScopeType() }}" scope-id="{{ $this->getScopeId() }}" />

    <div class="w-full flex flex-col grow gap-4">
        {{-- <livewire:sn-components-index-posts :limit="6" /> --}}
    </div>

    <livewire:sn-components-footer scope-type="{{ $this->getScopeType() }}" scope-id="{{ $this->getScopeId() }}" />
</div>
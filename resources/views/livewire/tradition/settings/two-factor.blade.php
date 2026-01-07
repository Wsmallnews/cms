<div class="w-full flex flex-col grow">
    {{-- <livewire:sn-cms-components-navigation scope-type="{{ $this->getScopeType() }}" scope-id="{{ $this->getScopeId() }}" /> --}}

    <div class="container mx-auto flex flex-col grow gap-4">
        <div class="w-full mx-auto md:w-96 p-4">
            <livewire:sn-user-components-settings-two-factor :module="app(\Wsmallnews\Cms\CmsPlugin::class)->getId()" />
        </div>
    </div>

    {{-- <livewire:sn-cms-components-footer scope-type="{{ $this->getScopeType() }}" scope-id="{{ $this->getScopeId() }}" /> --}}
</div>
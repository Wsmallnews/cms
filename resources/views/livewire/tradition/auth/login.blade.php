<x-dynamic-component :component="$this->getPageContainer()">
    <div class="sn-content">
        <div class="w-full mx-auto @2xl:w-96 sn-padded">
            <livewire:sn-user::components.auth.login />
        </div>
    </div>
</x-dynamic-component>
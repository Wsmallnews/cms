@push('seo')
    {{-- {!! seo()->for($navigation) !!} --}}
@endpush

<div class="w-full flex flex-col grow gap-4">
    <livewire:sn-components-navigation />

    <div class="container mx-auto flex flex-col grow gap-4">
        <div class="flex flex-col md:flex-row items-start gap-4">
            <livewire:sn-components-post :id="$id" />
        </div>
    </div>

    <livewire:sn-components-footer />
</div>

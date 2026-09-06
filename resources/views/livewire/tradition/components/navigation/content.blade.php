<div @class([
    'sn-container sn-padded' => $contained,
    'w-full'
])>
    @if ($content)
            <x-sn-support::content
                :content-type="$content->content_type"
                :content="$content->content"
            />
        @endif
</div>
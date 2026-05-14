<div @class([
    'sn-container p-4' => $contained,
    'w-full'
])>
    @if ($content)
            <x-sn-support::content
                :content-type="$content->content_type"
                :content="$content->content"
            />
        @endif
</div>